<?php

use bbn\Appui\Search;

if ($model->hasData('value', true)) {
  $search = new Search($model, $model->hasData('models') ? $model->data['models'] : []);
  return $search->stream($model->data['value'], $model->data['step'] ?? 0);
}