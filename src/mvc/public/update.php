<?php
use bbn\X;

/** bbn\Mvc\Controller $ctrl */
$res = ['success' => false];
use bbn\Appui\Search\Manager;

//set_time_limit(0);
/** @var bbn\Mvc\Controller $ctrl */
if (!empty($ctrl->post['uid']) && !empty($ctrl->post['filters'])) {
  // Create the SearchManager instance
  $manager = new Manager($ctrl, $ctrl->post['uid'], $ctrl->post['filters']['conditions']);
  $res['success'] = $manager->setCondition($ctrl->post['filters']['conditions'][0]);
}

$ctrl->obj = X::toObject($res);
