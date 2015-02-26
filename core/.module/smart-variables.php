<?php

// Модуль применения прави
// --------------------------
class SmartVariables {

	// Вычисление значения переменной
	// ----------------------
	public static function processVariable($var, $recursive = false, & $data = null) {

		// Если это простое значение, оставляем как есть
		// -------------------
		if (!is_array($var)) return $var;

		// Если это смарт-переменная, вычислям
		// -------------------
		if (@$var['smart'] == true) {
			switch ($var['type']) {
				case "field" : return @ $data[$var['fieldID']]; break;
				case "get": return @$_GET[$var['variable']]; break;
				case "post": return @$_POST[$var['variable']]; break;
				case "host": return @ $_SERVER['HTTP_HOST']; break;
				case "request": return @$_REQUEST[$var['variable']]; break;
				case "session": return @$_SESSION[$var['variable']]; break;
				case "path": return @ URI::$path[$var['index']]; break;
				case "var": return RequestVars::$vars[$var['variable']]; break;
				case "value": return @ $vars['value']; break;
				case "constant": return @ constant($var['constant']); break;
				case "eval": return eval($var['eval']); break;
				case "function": return execute($var['function']); break;
				case "date": return mktime(0, 0, 0, $var['day'], $var['month'], $var['year']); break;
				case "weekday": return date("w", time()); break;
				case "month": return date("n", time()); break;
				case "year": return date("Y", time()); break;
				case "time": return time(); break;
				case "rand": return rand(first_var(@ $var['from'], 0), first_var(@ $var['to'],1)); break;

				// Переключение переменных по условиям
				// ------------------------------
				case "switch":

					// Перебор вариантов и возврат подходящего значения
					// --------------------------
					if (!empty($var['variants'])) {
						foreach($var['variants'] as $variant) {
							if (Rules::check($variant['rules'])) {
								return $variant['value'];
							}
						}
					}
					// Если есть значение по умолчанию, берем его
					// -----------------------------------
					if (isset($var['default'])) return $var['default'];
				
					// Иначе возврат нуля
					// -----------------------------------
					return null;
					break;
				default: return null; break;
			}

		}
	
		// Если рекурсивная обработка, то перебор всего массива
		// -------------------
		if ($recursive) {	
			foreach($var as $varPropertyKey => $varProperty) {
				$var[$varPropertyKey] = self::processVariable($varProperty, true);
	
			}
		}
		return $var;

	}

	
	// Применение переменных к объекту
	// -----------------------
	public static function processObject($object, $recursive = false, & $values = null ) {

		if (!is_array($object) || empty($object)) return $object; // Если не массив, вычисляем праметры

		// Запуск преобразования
		// --------------------
		foreach($object as $variableKey => $variable) {
			$object[$variableKey] = self::processVariable($variable, $recursive, $values);
		}

		return $object;
	}

}