<?php
/**
 * What is my purpose?
 *
 **/

/** @var bbn\Mvc\Model $model */

use bbn\X;
use bbn\Appui\Search;

if ($model->hasData(['data', 'id'], true)) {
  $search = new Search($model);
  return [
    'success' => $search->setResult($model->data['id'], $model->data['data'])
  ];
}
