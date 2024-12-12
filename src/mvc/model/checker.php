<?php
/**
 * What is my purpose?
 *
 **/

use bbn\X;
use bbn\Str;
/** @var $model \bbn\Mvc\Model*/

if (!$model->hasData('idx')) {
  $model->addData(['value' => 'Any Value']);
}

$search = new bbn\Appui\Search($model, []);
$fns = $search->getExecutedCfg($model->data['value']);
array_walk($fns, function(&$a, $i) {
  $a['name'] = basename($a['file'], '.php') . ' - ' . ($a['score'] ?? '?') . ($a['alternative'] ? ' (' . $a['alternative'] . ')' : '');
});

if ($model->hasData('idx') && ($ele = X::getRow($fns, ['signature' => $model->data['idx']]))) {
  return [
    'data' => $search->getResult($ele),
    'sql' => $model->db->last(),
    'cfg' => $ele
  ];
}
  
return [
  'fns' => $fns
];
