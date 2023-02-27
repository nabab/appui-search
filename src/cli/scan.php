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
  if (defined('BBN_PREFERENCES') && BBN_PREFERENCES) {
    $prefCls = is_string(BBN_PREFERENCES) && class_exists(BBN_PREFERENCES) ?
      BBN_PREFERENCES :
      '\\bbn\\User\\Preferences';
    $prefCls = new $prefCls($ctrl->db);
  }
  if (defined('BBN_PERMISSIONS') && BBN_PERMISSIONS) {
    $permCls = is_string(BBN_PERMISSIONS) && class_exists(BBN_PERMISSIONS) ?
      BBN_PERMISSIONS :
      '\\bbn\\User\\Permissions';
    $permCls =  new $permCls($ctrl->getRoutes());
  }
  foreach ($models as $m) {
    if (is_null(X::find($options, ['plugin' => $m['plugin'], 'filename' => $m['filename']]))
      && ($idOpt = $ctrl->inc->options->add([
        'id_parent' => $idOption,
        'text' => (!empty($m['plugin']) ? $m['plugin'] . '/' : '') . $m['filename'],
        'plugin' => $m['plugin'],
        'filename' => $m['filename']
      ]))
      && $permCls->optionToPermission($idOpt, true)
    ) {
      $added++;
    }
  }
  if ($added) {
    X::adump($added);
  }
}
