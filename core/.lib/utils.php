<?php


// Функция возврате первого реального значения
// ------------------------------------
function first_var() {
	$fargs = func_get_args();
	if (!empty($fargs))	foreach($fargs as $arg) if (isset($arg) && !is_null($arg)) return $arg;
}

// Функция автозагрузки
// --------------------
function __autoload ($class) {

	// Определение пути
	// ----------------
	$path = str_replace('_', '/', $class);
	$basePath = __DR__.'libs/';
	$fullPath = $basePath . $path;

	// Если есть файл, загружаем
	// -------------------------
	if (file_exists($fullPath.'.php')) require_once($fullPath . '.php');
}


// функция для обработки завершения
// --------------------------------
register_shutdown_function(function() {
	$last_error = error_get_last();

	// Если есть ошибка, то распечатываем
	// ----------------------------------
	if (
			!empty($last_error) 
			&& ($last_error['type'] == 64 || $last_error['type'] == 1 || $last_error['type'] == 4)
		) {
			debug_print_backtrace();
			var_dump($last_error);
	}
});
