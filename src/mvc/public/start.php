<?php
use bbn\X;
use bbn\Str;
use bbn\Appui\Search\Manager;

//set_time_limit(0);
/** @var bbn\Mvc\Controller $ctrl */
if (!empty($ctrl->post['uid']) && !empty($ctrl->post['conditions'])) {
  // Make sure we return data in streaming mode
  $ctrl->setStream();

  // Create the SearchManager instance
  $manager = new Manager($ctrl, $ctrl->post['uid'], $ctrl->post['conditions']);
  // Run the search process
  while (ob_get_level()) {
    ob_end_flush();
  }

  foreach ($manager->run() as $res) {
    $num = count($res['data'] ?? []);
    X::log('yy ' . microtime(true) . " STREAMING $num RESULTS", 'searchTimings');
    $ctrl->stream($res);
    while (ob_get_level()) {
      ob_end_flush();
    }
    flush();
  }
  X::log('yy ' . microtime(true) . " FINISHED STREAMING RESULTS", 'searchTimings');
  
  /*
  // Sets output handling to streaming mode (so data can be sent incrementally to the client)
  $ctrl->setStream();

  // Build the path to a JSON file (used for storing search conditions)
  $file = $ctrl->pluginDataPath('appui-search') . 'config/' . $ctrl->post['uid'] . '.json';

  // Log file base path (used for storing temporary logs)
  $logFile = $ctrl->pluginDataPath('appui-search') . 'config/' . $ctrl->post['uid'];

  // Writes the first condition into the JSON file
  file_put_contents($file, json_encode($ctrl->post['conditions'][0]));

  // Mark process as successful (will remain true unless something fails)
  $res['success'] = true;

  // Some loop counters and limits
  $i = 0;
  $max = 50;       // Maximum number of results we want to collect
  $maxWorkers = 5; // Maximum number of parallel processes
  $workers = [];   // Array to keep track of running workers

  // Resource descriptor array for proc_open (stdin, stdout, stderr)
  // "pipe" means we can read/write from/to these resources
  // stderr is redirected to a file (defined at runtime)
  $desc = [
    ["pipe", "r"],   
    ["pipe", "w"],  
    ["file", null, "a"] 
  ];

  $current = null;

  // The command template used to run router.php in a separate process
  // It expects two placeholders: the URL endpoint and a JSON-encoded string with the needed data
  $cmd = 'php -f router.php %s "%s"';

  // A function to check if the JSON file still exists and if it still matches the current condition
  $isOk = function($condition) use ($file) {
    if (!file_exists($file)) {
      return false;
    }

    $tmp = json_decode(file_get_contents($file), true);
    if ($tmp['value'] !== $condition['value']) {
      return 0;
    }
    return 1;
  };

  // Simple logging function using bbn\X::log
  $log = function($msg) {
    X::log($msg, 'search');
  };

  // Cleans up workers by terminating their processes, closing them, and removing log files
  $rm = function(&$workers) {
    while (count($workers)) {
      $w = array_shift($workers);
      $status = proc_get_status($w['proc']);
      if ($status['running']) {
        proc_terminate($w['proc']);
      }
      proc_close($w['proc']);
      unlink($w['log']);
    }
  };

  // The root path of the current application
  $root = $ctrl->appPath();

  // The URL endpoint to which search results will be posted
  $url = $ctrl->pluginUrl('appui-search') . '/results';

  // Helper function to add a new worker (child process) to the $workers array
  $addWorker = function(&$workers, $result, $step) use ($cmd, $root, $desc, $logFile, $url, $log) {
    $w = [
      'proc' => null,
      'id' => $result['id'],
      // Build the final command by inserting the URL and a JSON-encoded argument
      'cmd' => sprintf(
        $cmd,
        $url,
        bbn\Str::escapeDquotes(json_encode(['item' => $result['item'] ?? null, 'step' => $step]))
      ),
      'uid' => Str::genpwd(), // Generate a unique ID for the worker
      'pipes' => []
    ];
    // Create a separate log file for this worker
    $w['log'] = $logFile . '-' . $w['uid'] . '.log';
    file_put_contents($w['log'], '');

    // Prepare the descriptor array for this worker
    $wdesc = $desc;
    // Set the error-log descriptor to the newly created log file
    $wdesc[2][1] = $w['log'];

    $log($w['log']);
    // Create a new process using proc_open
    $w['proc'] = proc_open(
      $w['cmd'],
      $wdesc,
      $w['pipes'],
      $root
    );

    // Non-blocking read on stdout pipe
    stream_set_blocking($w['pipes'][1], 0);

    // Register the newly created worker
    $workers[] = $w;
  };

  // Keep track of total number of search results we have processed/streamed
  $tot = 0;

  // Read the current condition from the JSON file
  $condition = json_decode(file_get_contents($file), true);

  // Create a timer (not directly shown doing anything in code, but presumably helpful in bbn\Util)
  $timer = new bbn\Util\Timer();
  $last = null;

  // Main loop: will keep running while the condition file is still valid and matches the original search
  while ($isOk($condition) !== false) {

    if (!empty($condition['value'])) {
      // If we're starting a new cycle with a new search value, reset counters and kill existing workers
      if ($current !== $condition['value']) {
        $current = $condition['value'];
        $rm($workers);

        $step = 0;
        $tot = 0;
        $results = [];
      }

      // If we haven't reached the global max results yet
      if ($tot < $max) {
        // Launch new worker processes while we still have capacity (<= $maxWorkers)
        // and as long as the model continues to provide valid next-step data
        while (
          (count($workers) <= $maxWorkers)
          && ($result = $ctrl->getModel(['value' => $condition['value'], 'step' => $step]))
        ) {
          // Create a new worker
          $addWorker($workers, $result, $step);

          // If the condition file is missing, break immediately
          if (!file_exists($file)) {
            $rm($workers);
            break;
          }
          // If the model says there is another step, update $step
          elseif (!empty($result['next_step'])) {
            $step = $result['next_step'];
          }
          else {
            // No next step, exit the worker-adding loop
            break;
          }
        }

        // Check each worker to see if it has finished, and retrieve its results
        for ($j = 0; $j < count($workers); $j++) {
          // Re-check the condition file each time
          if ($isOk($condition)) {
            $w = $workers[$j];
            $status = proc_get_status($w['proc']);
            X::log($j . ' ' . microtime(true) . ' ' . $status['running'], 'searchTimings');
            // If the process is no longer running, read its output
            if (!$status['running']) {
              $tmp = stream_get_contents($w['pipes'][1]);
              // If there's valid JSON in stdout, decode it
              if ($tmp && Str::isJson($tmp)) {
                X::log($j . ' ' . microtime(true) . ' JSON OK', 'searchTimings');
                $ret = json_decode($tmp, true);

                // If there are valid results, add them to the stream
                if (!empty($ret['data']['results'])) {
                  X::log($j . ' ' . microtime(true) . ' RESULTS OK', 'searchTimings');
                  $tot += count($ret['data']['results']);
                  // If adding them exceeds $max, clip the results
                  if ($tot > $max) {
                    array_splice($ret['data']['results'], count($ret['data']['results']) - ($tot - $max));
                  }

                  // Stream the partial results to the client
                  $ctrl->stream([
                    'data' => $ret['data']['results'],
                    'step' => $ret['step'],
                    'item' => $ret['item'],
                    'id'   => $w['id']
                  ]);
                }
                else {
                  X::log($j . ' ' . microtime(true) . ' RESULTS NOT OK ' . $tmp, 'searchTimings');
                }

              }

              // Check the log file for any errors and stream them as well
              if (file_exists($w['log'])) {
                $err = file_get_contents($w['log']);
                if ($err) {
                  $res['errors'][] = $err;
                  $ctrl->stream(['error' => $err, 'command' => $w['cmd']]);
                }
              }

              // If we have reached the maximum result count, kill all workers and stop
              if ($tot > $max) {
                $rm($workers);
                break;
              }
              else {
                // Remove the current worker from the array and terminate it
                array_splice($workers, $j, 1);
                $j--;
                $arr = [$w];
                $rm($arr);
              }
            }
          }
          else {
            // If condition file changed or is missing, reload the condition and kill workers
            $condition = json_decode(file_get_contents($file), true);
            $rm($workers);
            break;
          }
        }
      }
    }

    // Sleep for a short time to avoid busy-wait
    usleep(50000);
    if (connection_aborted()) {
      // If the connection is aborted, clean up and exit
      X::log('Connection aborted', 'searchAbort');
      $rm($workers);
      break;
    }
    $i++;
  }

  // End of main loop; clean up any remaining workers
  $rm($workers);

  // Remove the condition file if it still exists
  if (file_exists($file)) {
    unlink($file);
  }

  // Add the number of iterations to the output
  $res['progress'] = $i;

  // Stream the final response back
  $ctrl->stream($res);
  */
}
