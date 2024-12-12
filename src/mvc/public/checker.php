<?php

use bbn\X;
use bbn\Str;
/** @var $ctrl \bbn\Mvc\Controller */

if (isset($ctrl->post['idx'])) {
  $ctrl->action();
}
else {
  $ctrl->combo(_("Search checker"), true);
}

