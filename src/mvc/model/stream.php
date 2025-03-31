<?php
use bbn\X;
use bbn\Appui\Search;

if ($model->hasData('item', true)) {
  return ['results' => Search::seekResult($model->db, $model->data['item'])];
}
