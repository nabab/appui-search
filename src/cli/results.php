<?php
use bbn\X;

if (!empty($ctrl->post['item']) && !empty($ctrl->post['file'])) {
  $step = $ctrl->post['step'] ?? 0;
  //X::log("[STEP $step] " . microtime(true) . " BEFORE RESULTS ON $step", 'searchTimings');
  //X::log($ctrl->post['item'], 'searchCli');
  try {
    $data = $ctrl->getModel(
      $ctrl->pluginUrl('appui-search') . '/stream',
      ['item' => $ctrl->post['item'], 'step' => $step]
    );
  }
  catch (Exception $e) {
    $data = null;
  }

  $num = count($data['results'] ?? []);
  //X::log("[STEP $step] " . microtime(true) . " AFTER  $num RESULTS ON $step", 'searchTimings');
  if ($num) {
    file_put_contents($ctrl->post['file'], json_encode($data));
  }

  echo json_encode([
    'num' => $num,
    'cli' => true,
    'step' => $ctrl->post['step'] ?? 0,
    'item' => $ctrl->post['item']
  ]);
}
else {
  echo json_encode(['error' => 'No value']);
}

die();
