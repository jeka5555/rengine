<?php

// Класс для отображения страницы ошибки
// --------------------------------------
class ErrorPage extends \Templates\Page {

	public static $component = array(
		'type' => 'template',
		'id' => 'error-page',
		'title' => 'Страница вывода ошибок'
	);


	// Выдать страницу ошибки
	// ----------------------------------
	public static function get ($args = array()) {

		// Создаем страницу
		// -----------------
		$class = get_called_class();
		$template = new $class();

		// Определяем название страницы ошибки
		// -------------------------------
		if (isset($args['title'])) $template->title = $args['title'];
		else $template->title = "REngine CMS G4. Ошибка";

		// Код ошибки
		// ----------
		if (!empty($args['code'])) $response_code =  $args['code'];
		else $response_code = 400;
		header("Status: ".$response_code);

		// Если передан контент страницы, вставляем
		// -------------------------------
		if (@ $args['content']) {
				$template->content = '<div class="error-frame"><div class="error-code">'.$response_code.'</div><div class="error-text">'.$args['content'].'</div></div>';
		}

		// Иначе - контент ошибки по-умолчанию
		// -------------------------------
		else $template->content = '<div class="error-frame">Возникла непредвиденная ошибка. Вы можете связаться с разработиком, по указанному e-mail:<a href="mailto:info@rework.pro">info@rework.pro</a></div>';

		// Отображение
		// -----------
		$content = $template->render();
		die($content);

	}

}