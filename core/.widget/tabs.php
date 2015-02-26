<?php

namespace Widgets;

class Tabs extends \Widget {

	// Описание компонента
	// -------------------
	public static $component = array(
		'type' => 'widget',
		'title' => 'Элемент построения табов',
		'id' => 'tabs'
	);


	// Отображение
	// -----------
	private function renderTabContent($tabData) {

		// Генерируем идентификтор для кнопки
		// ----------------------------------
		$tabButtonID = first_var(@ $tabData['tabButtonID'], 'flexTabButton'.uniqid());
		$tabID = first_var(@ $tabData['tabID'], 'flexTab'.uniqid());


		// Для статуса активности
		// ------------------
		$headingAddin = '';	
		$tabAddin = '';

		if (@$tabData['index'] == $this->activeTab) $headingAddin = ' active';
		else $tabAddin = ' style="display: none" ';

		// Заголовок
		// ------------------
		$this->heading .= '<h4 id="'.$tabButtonID.'" data-tabID="'.$tabID.'" class="ui-tabs-button'.$headingAddin.'">'.@ $tabData['title'].'</h4>';

		// Добавляем содержимое таба
		// ------------------
		$tabContent = '<div class="ui-tabs-tab" id="'.$tabID.'"'.$tabAddin.'>'. @$tabData['content'].'</div>';
		return $tabContent;

	}

	// Визуализация
	// ------------
	public function render() {

		// Если табов нет, ничего не делаем
		// ---------------------
		if (empty($this->args['tabs'])) return '';

		// Сборка данных
		// ---------------------
		$tabsContent = '';
		$widgetArgs = array();

		// Инициализация
		// -------------
		$this->heading = '';

		// Определяем индекс активного таба
		// ---------------------
		$this->activeTab = 0;
		foreach ($this->args['tabs'] as $tabIndex => $tabData) {
			if (@ $tabData['active'] == true) $this->activeTab = $tabIndex;
		}
		
		// Конструируем набор табов
		// ---------------------
		foreach ($this->args['tabs'] as $tabIndex => $tabData) {
			$tabData['index'] = $tabIndex;
			$tabsContent .= $this->renderTabContent($tabData);
		}

		// Заворачиваем контент
		// ----------------------
		$tabsHeading = 
		$tabsContent = '<div class="ui-tabs-content">'.$tabsContent.'</div>';

		// Генерация уникального идентификатора
		// ----------------------
		$tabsID = first_var(@ $args['tabsID'], 'flexTabs'.uniqid());

		// Вывод содержимого
		// ----------------------
		$content = '<div class="ui-tabs" id="'.$tabsID.'">
			<div class="ui-tabs-heading">'.@ $this->heading.'</div>'.
			@ $tabsContent.
		'</div>';

		// Подключение скрипта
		// -------------------
		$widgetArgs['widget'] = '#'.$tabsID;
        \Events::send('addEndScript', 'var '.$tabsID.' = new UI.Tabs('.json_encode($widgetArgs).'); ');
		return $content;


	}

}