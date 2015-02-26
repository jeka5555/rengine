<?php

class Content {

	public static $types = null;

	// Построение тэга
	// --------------------------------------
	public static function buildTag($args = array()) {

		// Идентификатор тэга и класс
		// ----------------------------------
		if (!empty($args['htmlID'])) $idAddin = ' id="'.$args['htmlID'].'" '; else $idAddin = '';

		// Определяем класс для вывода
		// ----------------------------------
		$classAddin = '';

		if (!empty($args['htmlClasses'])) {
			if (is_string($args['htmlClasses'])) $classAddin = ' class="'.$args['htmlClasses'].'"';
			if (is_array($args['htmlClasses'])) $classAddin = ' class="'.join(" ", $args['htmlClasses']).'" ';
		}

		if (!empty($args['css'])) {

			$style = '';

			foreach($args['css'] as $cssProperty => $cssValue) {
				$style .= ' '.$cssProperty.': '.$cssValue;
			}
			$args['htmlAttributes']['style'] = $style;
		}

		// Parse attributes
		// ----------------
		$attrContent = '';
		if (!empty($args['htmlAttributes'])) {

			$attrContent = '';

			foreach($args['htmlAttributes'] as $attributeID => $attributeValue) {

				// Фильтруем пустные значения
				// --------------------------
				if (empty($attributeValue)) continue;

				// Слкеиваем с одну строчку
				// ------------------------
				$attrContent .= " $attributeID=\"$attributeValue\"";
			}
		}

		// Собираем тэг
		// ----------------------------------
		$tag = first_var(@ $args['tag'], 'div');
		return "<$tag $idAddin $classAddin $attrContent>".$args['content']."</$tag>";
	}

}





