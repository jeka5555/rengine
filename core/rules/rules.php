<?php

class Rules extends \Module {

	// Component
	// ---------
	public static $component = array(
		'id' => 'rules',
		'title' => 'Модуль правил'
	);

	// Выполнение одной операции
	// ----------------------
	public static function evalOperation($base, $value, $operation = '$eq') {

		switch (@$operation) {

			case '$eq': return ($base == $value);	break;
			case '$ne':	return ($base != $value);	break;
			case '$gt':	return ($base > $value); break;
			case '$lt':	return ($base < $value); break;
			case '$gte': return ($base >= $value); break;
			case '$lte': return ($base <= $value); break;
			case '$regexp': return preg_match('#'.$value.'#i', $base); break;
			case '$exists': {
				return isset($base);
				break;
			}	
			case '$null': 	return (!isset($base) || is_null($base));	break;
			case '$notnull': return !is_null($base); break;
			case '$contain': return (strpos($base, $value)); break;
			case '$in': return in_array($base, $value); break;
			case '$nin': return !in_array($base, $value); break;
			default: return ($base == $value); break;
		}
	}

	// Проверка одного правила
	// ----------------------
	public static function checkRule($rule = array()) {

		$result = true;

		// Guard
		// -----
		if (empty($rule['type'])) return true;

		// Get class
		// ---------
		$ruleComponentClass = \Core::getComponent('rule', $rule['type']);

		if (empty($ruleComponentClass)) {
			return false;
		}

		// Create and check
		// ----------------
		$ruleObject = $ruleComponentClass::getInstance($rule);
		$result = $ruleObject->check();


		// Если флаг инверсии, инвертируем
		// --------------------
		if (@ $rule['invert'] == true) $result = !$result;

		// Возврат результата
		// --------------------
		return $result;
	}

	// Check rules set
	// ---------------
	public static function check($rules = array(), $mode = 'and') {

		// Check for boolean values
		// ------------------------
		if (@ $rules === true) return true;
		else if (@ $rules === false) return false;

		// Default value for tests
		// -----------------------
		$result = ($mode == 'and') ? true : false;

		// If not empty rules, check it
		// ----------------------------
		if (!empty($rules) && is_array($rules)) {

			foreach($rules as $rule) {

				// Test result
				// -----------
				$ruleResult = self::checkRule($rule);

				// Проверяем, делать ли выход
				// ---------------
				if ($mode == 'and' && $ruleResult == false) {
					$result = false;
					break;
				}
				else if ($mode == 'or' && $ruleResult == true) {
					$result = true;
					break;
				}
			}
		}

		return $result;

	}

	// Get rules info
	// --------------
	public static function actionGetRulesInfo() {

		// Result is here
		// --------------
		$result = array(
			'rules' => array(),
			'operations' => array()
		);

		// Collect rules
		// -------------
		$rules = \Extension::$ext['rule'];
		if (!empty($rules)) {
			foreach ($rules as $ruleID => $rule) {
				$ruleClass = \Core::getComponent('rule', $ruleID);
				if (empty($ruleClass)) continue;

				$result['rules'][$ruleID] = array(
					'title' => first_var($ruleClass::$component['title'], $ruleID),
					'format' => $ruleClass::$ruleFormat
				);
			}
		}

		// Return result
		// -------------
		return $result;
	}
}
