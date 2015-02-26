<?php

// Виджет. Расширенная версия страничной навигации
// ----------------------------------
namespace Widgets;

class Paginator extends \Widget {

	// Регистрация компопнента
	// -----------------------
	public static $component = array(
		'id' => 'paginator',
		'title' => 'Общие.Страничная навигация',
		'fields' => array(
			array('id' => 'format', 'type' =>'text', 'title' => 'Формат вывода страничной навигации'),
			array('id' => 'count', 'type' => 'number', 'title' => 'Количество страниц'),
			array('id' => 'active', 'type' => 'number', 'title' => 'Активная страница'),
			array('id' => 'maxVisiblePages', 'type' => 'number', 'title' => 'Сколько видимо страниц')
		),
		'editable' => true,
		'group' => 'ui'
	);


    // Add widget controller
    // ---------------------
    public function addControllerScript() {

        parent::addControllerScript();

        // Get ID
        // ------
        $htmlID = $this->generateHtmlID();


        // Get script properties
        // ---------------------
        $controllerArgs = array(
            'widget' => '#'.$htmlID,
        );

        \Events::send('addEndScript', '
			paginatorController'.str_replace($htmlID,'-','').' = new PaginatorController('.json_encode($controllerArgs).');
		');

    }

	// Отображение
	// -----------
	public function render() {

		// Если неадекватное количество страниц, то выходим и ничего не делаем
		// --------------------------
		if (@ $this->args['count'] < 2 ) return false;
		$this->count = $this->args['count'];

		// Данные для тестирования
		// --------------------------
		$this->active = (int) first_var(@ $this->args['active'], 1);
		$this->function = @ $this->args['function'];

		$content = '';

		// Видимое количество страниц
		// --------------------------
		$this->maxPages = first_var(@ $this->args['maxPages'], 10);

		// Определение первой и последней страницы
		// --------------------------------------
		$this->firstPage = ($this->active - round($this->maxPages / 2) < 1) ?  1 : $this->active - round($this->maxPages / 2);
		$this->lastPage = $this->active+ round($this->maxPages / 2);
		if ($this->lastPage > $this->count) $this->lastPage = $this->count;

		// Определяем видимость начальной и конечной страницы
		// ----------------------------------
		if (($this->firstPage != 1)) {
			$content = $this->getPage(1, array('title' => 'первая'));
		}

		// Добвляем страницы в пределах -/+ 5 от текущей
		// -------------------------------------
		for ($page = $this->firstPage; $page <= $this->lastPage; $page++) {
			$content .=  $this->getPage($page);
		}

		// Определяем видимость последней страницы
		// ---------------------------------------
		if (($this->active + round($this->maxPages /2)) < $this->count) {
			$content .= $this->getPage($this->count, array('title' => 'последняя'));
		}

		// Добавляем название пейджинатора
		// ----------------------------
		if (@ $this->args['show-tilte'] !== false) {
			$title = first_var(@ $this->args['title'], 'Страницы: ');
			$titleAddin = '<span class="title">'.$title.'</span>';
		}

		// Вывод полученного результата
		// ----------------------------
		$content = $titleAddin.$content;
		return $content;

	}

	// Получение кода страницы
	// -----------------------
	private function getPage($page, $args = array()) {

		// Название страницы
		// --------------
		$title = first_var(@ $args['title'], $page);

		// Если страница активна
		// --------------
		if (@$this->args['active'] == $page) $activeAddin = ' active';

		// Формируем ссылку
		// ----------------
		$link = @call_user_func($this->function, $page);

		// Вывод информации
		// ----------------
		return '<a class="page'.@ $activeAddin.'" href="'.$link.'">'.$title.'</a>';

	}
}