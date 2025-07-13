<?php
/**
 * What is my purpose?
 *
 **/

use bbn\X;
use bbn\Appui\Search;
/** @var $model bbn\Mvc\Model */

$res = ['success' => false, 'data' => []];
if ($model->hasData('data', true) && isset($model->data['data']['value'])) {
  $res['success'] = true;
  $v = $model->data['data']['value'];
  if (!empty($v)) {
    $search = new Search($model, []);
    $res['data'] = $search->getExecutedCfg($v);
    array_walk($res['data'], function(&$a, $i) {
      $a['text'] = $a['name'] . ' - ' 
        . $a['score'] . ' ' . X::_("points")
        . (isset($a['alternative']) ? ' (' . X::_("alternative") . ' ' . $a['alternative'] . ')' : '');
    });
  }
}
  
return $res;
