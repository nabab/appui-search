<?php
use bbn\X;
use bbn\Str;
use bbn\Appui\Search;

/** bbn\Mvc\Controller $ctrl */
if (!empty($ctrl->post['uid']) && !empty($ctrl->post['conditions'])) {
  $res = ['success' => false, 'errors' => []];
  $ctrl->setStream();
  $file = $ctrl->dataPath() . $ctrl->post['uid'] . '.json';
  $logFile = $ctrl->dataPath() . $ctrl->post['uid'];
  file_put_contents($file, json_encode($ctrl->post['conditions'][0]));
  $res['success'] = true;
  $i = 0;
  $max = 50;
  $maxWorkers = 5;
  $workers = [];
  $desc = [
    ["pipe", "r"],  // stdin is a pipe that the child will read from
    ["pipe", "w"],  // stdout is a pipe that the child will write to
    ["file", null, "a"] // stderr is a file to write to
  ];
  $current = null;
  $cmd = 'php -f router.php %s "%s"';
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
  $log = function($msg) {
    X::log($msg, 'search');
  };
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
  $root = $ctrl->appPath();
  $url = $ctrl->pluginUrl('appui-search') . '/results';
  $addWorker = function(&$workers, $result, $step) use ($cmd, $root, $desc, $logFile, $url) {
    $w = [
      'proc' => null,
      'id' => $result['id'],
      'cmd' => sprintf(
        $cmd,
        $url,
        bbn\Str::escapeDquotes(json_encode(['item' => $result['item'] ?? null, 'step' => $step]))
      ),
      'uid' => Str::genpwd(),
      'pipes' => []
    ];
    $w['log'] = $logFile . '-' . $w['uid'] . '.log';
    file_put_contents($w['log'], '');
    $wdesc = $desc;
    $wdesc[2][1] = $w['log'];
    $w['proc'] = proc_open(
      $w['cmd'],
      $wdesc,
      $w['pipes'],
      $root
    );
    stream_set_blocking($w['pipes'][1], 0);
    $workers[] = $w;
  };

  $tot = 0;
  $condition = json_decode(file_get_contents($file), true);
  $timer = new bbn\Util\Timer();
  $last = null;
  while ($isOk($condition) !== false) {
    if (!empty($condition['value'])) {
      // Starting a new cycle with a new value
      if ($current !== $condition['value']) {
        $current = $condition['value'];
        $rm($workers);

        $step = 0;
        $tot = 0;
        $results = [];
      }


      if ($tot < $max) {
        while ((count($workers) <= $maxWorkers) && ($result = $ctrl->getModel(['value' => $condition['value'], 'step' => $step]))) {
          $addWorker($workers, $result, $step);
          if (!file_exists($file)) {
            $rm($workers);
            break;
          }
          elseif (!empty($result['next_step'])) {
            $step = $result['next_step'];
          }
          else {
            break;
          }
        }

        // Going through the workers until extinction
        for ($j = 0; $j < count($workers); $j++) {
          if ($isOk($condition)) {
            $w = $workers[$j];
            $status = proc_get_status($w['proc']);
            if (!$status['running']) {
              $tmp = stream_get_contents($w['pipes'][1]);
              if ($tmp && Str::isJson($tmp)) {
                $ret = json_decode($tmp, true);
                if (!empty($ret['data']['results'])) {
                  $tot += count($ret['data']['results']);
                  if ($tot > $max) {
                    array_splice($ret['data']['results'], count($ret['data']['results']) - ($tot - $max));
                  }
                  $ctrl->stream([
                    'data' => $ret['data']['results'],
                    'step' => $ret['step'],
                    'item' => $ret['item'],
                    'id' => $w['id']
                  ]);
                }
              }

              if (file_exists($w['log'])) {
                $err = file_get_contents($w['log']);
                if ($err) {
                  $res['errors'][] = $err;
                  $ctrl->stream(['error' => $err, 'command' => $w['cmd']]);
                }
              }

              if ($tot > $max) {
                $rm($workers);
                break;
              }
              else {
                array_splice($workers, $j, 1);
                $j--;
                $arr = [$w];
                $rm($arr);
              }
            }
          }
          else {
            $condition = json_decode(file_get_contents($file), true);
            $rm($workers);
            break;
          }
        }
      }
    }
  
    usleep(50000);
    $i++;
  }

  $rm($workers);
  if (file_exists($file)) {
    unlink($file);
  }

  $res['progress'] = $i;
  $ctrl->stream($res);
}
