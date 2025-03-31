<?php
use bbn\X;

if (!empty($ctrl->post['item'])) {
  $step = $ctrl->post['step'] ?? 0;
  X::log('00 ' . microtime(true) . " BEFORE RESULTS ON $step", 'searchTimings');
  X::log($ctrl->post['item'], 'searchCli');
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
  X::log('99 ' . microtime(true) . " AFTER  $num RESULTS ON $step", 'searchTimings');
  X::log($data, 'searchCli');
  echo json_encode([
    'data' => $data,
    'cli' => true,
    'step' => $ctrl->post['step'] ?? 0,
    'item' => $ctrl->post['item']
  ]);
}
else {
  echo json_encode(['error' => 'No value']);
}

die();
