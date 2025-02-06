<?php
use bbn\X;
use bbn\Str;
use bbn\Appui\Search;

/** bbn\Mvc\Controller $ctrl */
if (!empty($ctrl->post['uid']) && !empty($ctrl->post['conditions'])) {
  $res = ['success' => false, 'errors' => []];
  $ctrl->setStream();
  $file = $ctrl->dataPath() . $ctrl->post['uid'] . '.json';
  $log = $ctrl->dataPath() . $ctrl->post['uid'];
  file_put_contents($file, json_encode($ctrl->post['conditions'][0]));
  $res['success'] = true;
  $i = 0;
  $workers = [];
  $desc = [
    ["pipe", "r"],  // stdin is a pipe that the child will read from
    ["pipe", "w"],  // stdout is a pipe that the child will write to
    ["file", null, "a"] // stderr is a file to write to
  ];
  $current = null;
  $cmd = 'php -f router.php %s "%s"';
  while (file_exists($file)) {
    $condition = json_decode(file_get_contents($file), true);
    if (!empty($condition['value'])) {
      if ($current !== $condition['value']) {
        $current = $condition['value'];
        while (count($workers)) {
          $w = array_shift($workers);
          proc_close($w['proc']);
          unlink($w['log']);
        }

        $step = 0;
        $results = [];
        while (($result = $ctrl->getModel(['value' => $condition['value'], 'step' => $step]))
            && !empty($result['item'])
          && ($step < 100)
        ) {
          ob_end_clean();
          $w = [
            'proc' => null,
            'id' => $result['id'],
            'cmd' => sprintf(
              $cmd,
              $ctrl->pluginUrl('appui-search') . '/results',
              bbn\Str::escapeDquotes(json_encode(['item' => $result['item'], 'step' => $step]))
            ),
            'uid' => Str::genpwd(),
            'pipes' => []
          ];
          $w['log'] = $log . $w['uid'] . '.log';
          file_put_contents($w['log'], '');
          $wdesc = $desc;
          $wdesc[2][1] = $w['log'];
          $w['proc'] = proc_open(
            $w['cmd'],
            $wdesc,
            $w['pipes'],
            $ctrl->appPath()
          );
          stream_set_blocking($w['pipes'][1], 0);
          $workers[] = $w;
          $step++;
        }

      }
      
        ob_end_clean();

      for ($j = 0; $j < count($workers); $j++) {
        $w = $workers[$j];
        $status = proc_get_status($w['proc']);
        if (!$status['running']) {
          $tmp = stream_get_contents($w['pipes'][1]);
          if ($tmp && Str::isJson($tmp)) {
            $ret = json_decode($tmp, true);
            if (!empty($ret['data']['results'])) {
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

          array_splice($workers, $j, 1);
          $j--;
          proc_close($w['proc']);
          unlink($w['log']);
        }
      }
    }
  
    usleep(100000);
    $i++;
  }

  while (count($workers)) {
    proc_close(array_shift($workers));
  }

  $res['progress'] = $i;
  $ctrl->stream($res);
}
