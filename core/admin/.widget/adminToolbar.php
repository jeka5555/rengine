<?php

namespace Widgets;

class AdminToolbar extends \Widget {

	// Component
	// ---------
	public static $component = array(
		'type' => 'widget',
		'id' => 'admin-toolbar'
	);

	// Buttons
	// -------
	public $buttons = array();

	// Add script
	// ----------
	public function addControllerScript() {

		// Collect options here
		// --------------------
		$widgetArgs = array(
			'apps' => $this->buttons
		);

		// Create admin toolbar
		// --------------------
		$toolbarID = 'adminToolbar'.uniqid();

		// Return content
		// --------------
		$content = 'var '.$toolbarID.' = new UI.AdminToolbar('.json_encode($widgetArgs).');';

		// Submit script
		// -------------
		\Events::send('addEndScript', $content);

	}

	// Добавление приложения в пул панели
	// ----------------------------------
	private function addApplicationButton($application) {

		// Подготовка опций
		// ----------------
		$options = array(
			'id' => @ $application::$component['id'],
			'title' => @ $application::$component['title'],
			'iconClass' => @ $application::$component['iconClass'],
			'icon' => @ $application::$component['icon'],
			'appID' => $application::$component['id'],
		);

		// Если есть команда для запуска
		// -----------------------------
		if ($command = call_user_func(array($application, 'getExecuteCommand'))) {
			$options['command'] = $command;
		}

		// Добавляем кнопку
		// --------------------------
		$this->buttons[] = $options;
	}


	// Поиск всех доступных приложений
	// -------------------------------
	private function findApplications() {

		$appsList = array();
		$applications = &\Components::$types['admin-application'];


		// Каждое приложение проверяем на возможность вызова
		// -------------------------------------------------
		if (!empty($applications))
		foreach($applications as $application) {

			if (@$application::$component['addOnToolbar'] != true) continue;
			if (!empty($application::$component['access'])) {
				if (!\Rules::check($application::$component['access'])) continue;
			}

			// Если
			$appsList[] = $application;
		}

		return $appsList;

	}



	// Визуализация
	// ------------
	public function render() {

		// Only for logged users
		// ---------------------
		if (! @ \Core::getModule('users')->hasRole('administrator')) $this->cancel();

		// Get applications list
		// ---------------------
		if ($applications = $this->findApplications());
		if (empty($applications))	$this->cancel();

		foreach($applications as $application) {
			$this->addApplicationButton($application);
		}

	}


}