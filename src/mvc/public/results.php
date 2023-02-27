<?php
use bbn\X;

/** @var bbn\Mvc\Controller $ctrl */

$ctrl->data['value'] = '';
if (!isset($ctrl->post['value'])) {
  if (isset($ctrl->post['filter']) && !empty($ctrl->post['filter']['filters']) && !empty($ctrl->post['filter']['filters'][0]['value'])) {
	  $ctrl->data['value'] = $ctrl->post['filter']['filters'][0]['value'];
  }
  elseif ( isset($ctrl->post['filters']) && !empty($ctrl->post['filters']['conditions']) ){
	  $ctrl->data['value'] = $ctrl->post['filters']['conditions'][0]['value'];
	}
}
if (!empty($ctrl->post['models'])
  || (!empty($ctrl->post['data']) && !empty($ctrl->post['data']['models']))
) {
  if (!empty($ctrl->post['models'])) {
    $ctrl->data['models'] = $ctrl->post['models'];
  }
  else if (!empty($ctrl->post['data']) && !empty($ctrl->post['data']['models'])) {
    $ctrl->data['models'] = $ctrl->post['data']['models'];
  }
  if (!is_array($ctrl->data['models'])) {
    $ctrl->data['models'] = [$ctrl->data['models']];
  }
}

$ctrl->action();

