<?php
use bbn\X;

/** bbn\Mvc\Controller $ctrl */
$res = ['success' => false];
if (!empty($ctrl->post['uid']) && !empty($ctrl->post['conditions'])) {
  $file = $ctrl->dataPath() . $ctrl->post['uid'] . '.json';
  $res['success'] = file_put_contents($file, json_encode($ctrl->post['conditions'][0]));
}

$ctrl->obj = X::toObject($res);
