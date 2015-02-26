<?php

namespace Core\Widgets;

class ListWidget extends \Widget {

	// Информация компонента
	// ---------------------
	public static $component = array(
		'type' => 'widget',
		'id' => 'list'
	);


	// Отображение заголовка
	// ---------------------
	private function renderTitle() {
		if (empty($this->title)) return;
		return '<div class="heading"><h2>'.@$this->title.'</h2></div>';
	}

	// Отображение контента
	// --------------------
	public function renderContent() {
		if (empty($this->content)) return;
		return '<div class="content">'.@$this->content.'</div>';
	}

	// Отображение страничной навигации
	// --------------------------------
	private function renderPaginator() {
		if (@ $this->pageCount < 1) return;
		return '<div class="paginator">Страничная навигация</div>';
	}


	// Отображение
	// -----------
	public function render() {

		if (!empty($this->args['title'])) $this->title = $this->args['title'];

		// Сборка контента
		// ---------------
		$content =
			$this->renderTitle().
			$this->renderContent().
			$this->renderPaginator();

		// Возврат
		// -------
		return $content;
	}
}