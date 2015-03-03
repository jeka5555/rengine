<?php

// Загрзука утилит
// ---------------
require_once('.lib/utils.php');
require_once('.lib/log.php');
require_once('.lib/loader.php');
require_once('.lib/core-object.php');
require_once('.lib/components.php');
require_once('.lib/component.php');
require_once('.lib/extension.php');
require_once('.lib/packages.php');
require_once('.lib/modules.php');
require_once('.lib/module.php');

// Init core
// ---------
require_once('core.php');
\Core::initComponent();

// Load and init database
// ----------------------
\Loader::importPackage('core.db');
\DB::initComponent();

// Load other stuff
// ----------------
\Loader::importPackage('core.events');
\Loader::importPackage('core.translate');

// Module for page stuff
// ---------------------
\Loader::importPackage('core.pages');

// Start
//------
\Loader::importPackage('core');
$core = \Core::getInstance();
$core->start();
