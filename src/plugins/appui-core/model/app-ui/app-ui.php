<?php

use bbn\Mvc;
/** @var bbn\Mvc\Model $model The current model */

return [
  'headright' => [
    'content' => Mvc::getInstance()->subpluginView('app-ui/icon', 'html', [], 'appui-search', 'appui-core'),
    'script' => Mvc::getInstance()->subpluginView('app-ui/icon', 'js', [], 'appui-search', 'appui-core'),
  ],
  'after' => [
    'content' => Mvc::getInstance()->subpluginView('app-ui/search', 'html', [], 'appui-search', 'appui-core'),
    'script' => Mvc::getInstance()->subpluginView('app-ui/search', 'js', [], 'appui-search', 'appui-core'),
  ]
];


