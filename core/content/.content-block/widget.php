<?php
namespace Core\ContentBlocks;

class Widget extends \Core\Components\ContentBlock {

	// Component
	// ---------
	public static $component = array(
		'id' => 'widget',
		'type' => 'content-block',
		'title' => 'Отображение виджета, определяемого пользователем'
	);

	// Render
	// ------
	public function renderContent() {
		$widget = \Core::getModule('widgets')->createWidget($this->data);
		return $widget->get();
	}
}
