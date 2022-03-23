<?php
/**
 * What is my purpose?
 *
 **/

/** @var $model \bbn\Mvc\Model*/

use bbn\X;

if ($model->hasData(['data', 'id'], true)) {
  $search = new bbn\Appui\Search($model);
  return [
    'success' => $search->setResult($model->data['id'], $model->data['data'])
  ];
}
