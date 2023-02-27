<?php
use \bbn\File\Dir;
use \bbn\X;

if ($idOption = $ctrl->inc->options->fromCode('models', 'search', 'appui')) {
  $models = [];
  $added = 0;
  if (is_dir(BBN_APP_PATH . 'plugins/appui-search/model')
    && ($files = Dir::getFiles(BBN_APP_PATH . 'plugins/appui-search/model'))
  ) {
    foreach ($files as $file) {
      $models[] = [
        'plugin' => false,
        'filename' => basename($file, '.php')
      ];
    }
  }
  if ($plugins = $ctrl->getPlugins()) {
    foreach ($plugins as $plugin) {
      if (!empty($plugin['path'])
        && !empty($plugin['name'])
        && is_dir($plugin['path'] . 'src/plugins/appui-search/model')
        && ($files = Dir::getFiles($plugin['path'] . 'src/plugins/appui-search/model'))
      ) {
        foreach ($files as $file) {
          $models[] = [
            'plugin' => $plugin['name'],
            'filename' => basename($file, '.php')
          ];
        }
      }
    }
  }
  $options = $ctrl->inc->options->fullOptions($idOption) ?: [];
  foreach ($models as $m) {
    if (is_null(X::find($options, ['plugin' => $m['plugin'], 'filename' => $m['filename']]))
      && ($idOpt = $ctrl->inc->options->add([
        'id_parent' => $idOption,
        'text' => (!empty($m['plugin']) ? $m['plugin'] . '/' : '') . $m['filename'],
        'plugin' => $m['plugin'],
        'filename' => $m['filename']
      ]))
    ) {
      $ctrl->inc->permissions->optionToPermission($idOpt, true);
      $added++;
    }
  }
  if ($added) {
    X::adump($added);
  }
}
