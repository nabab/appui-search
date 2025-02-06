<?php
use bbn\X;

/** bbn\Mvc\Controller $ctrl */
$res = ['success' => false];
if (!empty($ctrl->post['uid'])) {
  $res['uid'] = $ctrl->post['uid'];
  $file = $ctrl->dataPath() . $ctrl->post['uid'] . '.json';
  if (file_exists($file)) {
    $res['success'] = unlink($file);
  }
}

$ctrl->obj = X::toObject($res);
