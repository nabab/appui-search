<?php
/**
 * What is my purpose?
 *
 **/

use bbn\X;
use bbn\Str;
use bbn\Appui\Grid;
/** @var $model \bbn\Mvc\Model*/
if ($model->hasData('id')) {
  $r = ['success' => false];
  if ($model->db->selectOne('bbn_search', 'id_user', ['id' => $model->data['id']])) {
    $r['success'] = $model->db->delete('bbn_search', ['id' => $model->data['id']]);
  }

  return $r;
}
elseif ($model->hasData('limit')) {
  $grid = new Grid($model->db, $model->data, [
    'table' => 'bbn_search',
    'fields' => [
      'bbn_search.id', 'bbn_search.last', 'bbn_search.num', 'bbn_search.value',
      'results' => 'COUNT(bbn_search_results.id)'
    ],
    'join' => [
      [
        'table' => 'bbn_search_results',
        'on' => [
          [
            'field' => 'id_search',
            'exp' => 'bbn_search.id'
          ]
        ]
      ]
    ],
    'filters' => ['id_user' => $model->inc->user->getId()],
    'group_by' => ['bbn_search.id']
  ]);
  if ($grid->check()) {
    return $grid->getDatatable();
  }

  return ['success' => false];
}
