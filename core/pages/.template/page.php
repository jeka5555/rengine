<?php

namespace Templates;

// Шаблон основной страницы
// -------------------------
class Page extends \Template {

	// Инициализация компонента
	// ------------------------
	public static $component = array(
		'id' => 'page',
		'title' => 'Простая страница',
		'templateClass' => 'page'
	);

	public static $templateBlocks = array(
		'content' => array('title' => 'Содержимое страницы')
	);

	// Визуализация страницы
	// ---------------------
	public function render() {
        \Core::getApplication()->data['page']['htmlClasses'][] = 'default-rengine';
		return $this->content;
	}
}
