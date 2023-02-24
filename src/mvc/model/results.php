<?php
use bbn\X;

$max = 100;
if ($model->hasData('value', true)) {
  $search = new bbn\Appui\Search($model, !empty($model->data['models']) ? $model->data['models'] : []);
  return $search->get($model->data['value'], $model->data['step'] ?? 0);
}
