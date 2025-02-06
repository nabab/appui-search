<?php

if (!empty($ctrl->post['item'])) {
  echo json_encode([
    'data' => $ctrl->addData(['item' => $ctrl->post['item'], 'step' => $ctrl->post['step'] ?? 0])
      ->getModel($ctrl->pluginUrl('appui-search') . '/stream'),
    'cli' => true,
    'step' => $ctrl->post['step'] ?? 0,
    'item' => $ctrl->post['item']
  ]);
}
else {
  echo json_encode(['error' => 'No value']);
}

die();