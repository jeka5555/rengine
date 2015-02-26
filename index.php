<?php         
error_reporting(E_ALL);
setlocale(LC_ALL, 'ru_RU.UTF-8'); 

@session_set_cookie_params(0, '/', $_SERVER['HTTP_HOST'], false, false);
@ini_set('session.cookie_domain', $_SERVER['HTTP_HOST']);
@session_start();

define("__DR__", rtrim($_SERVER['DOCUMENT_ROOT'],'/').'/');
header('Content-type: text/plain;charset=utf-8');
//die('проходят технические работы. Система будет доступна в течении 30 минут');
require_once('core/loader.php');