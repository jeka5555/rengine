<?php

// Load utilities
// --------------
require_once('.libs/utils.php');
require_once('.libs/loader.php');
require_once('.libs/component.php');
require_once('.libs/components.php');

// Init core
// ---------
require_once('core.php');
$core = new \Core();
$core->start();