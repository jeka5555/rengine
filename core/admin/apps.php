<?php
namespace Modules;

class Apps extends \Module {

	// Component
	// ---------
	public static $component = array(
		'id' => 'apps',
		'description' => 'Интерфейс подключения к приложениям'
	);

	// Route action to applications
	// ----------------------------
	public function action($appID, $data = null, $path = null) {

		// Get application class
		// ---------------------
		$application = \Core::getComponent('admin-application', $appID);
		if (empty($application)) return;

		// Get command
		// -----------
		$command = @$path[3];
		if (empty($command)) return;

		// Create and run
		// --------------
		$app = new $application();
		return $app->command($command, $data, $path);

	}

}
