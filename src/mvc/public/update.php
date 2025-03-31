<?php
use bbn\X;

/** bbn\Mvc\Controller $ctrl */
$res = ['success' => false];
use bbn\Appui\Search\Manager;

//set_time_limit(0);
/** @var bbn\Mvc\Controller $ctrl */
if (!empty($ctrl->post['uid']) && !empty($ctrl->post['conditions'])) {
  // Create the SearchManager instance
  $manager = new Manager($ctrl, $ctrl->post['uid'], $ctrl->post['conditions']);
  $res['success'] = $manager->setCondition($ctrl->post['conditions'][0]);
}

$ctrl->obj = X::toObject($res);
