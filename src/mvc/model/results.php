<?php
use bbn\X;
use bbn\Appui\Search;

$max = 100;
if ($model->hasData('value', true)) {
  $search = new Search($model, !empty($model->data['models']) ? $model->data['models'] : []);
  return $search->get($model->data['value'], $model->data['step'] ?? 0);
}
