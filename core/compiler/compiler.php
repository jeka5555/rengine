<?php

namespace Modules;

class Compiler extends \Module {

	public static $component = array(
		'id' => 'compiler',
		'title' => 'Компилятор ресурсов',
		'hasSettings' => true
	);


	// Settings
	// --------
	public static $settings = array(
		'enabled' => true
	);

	// Settings format
	// ---------------
	public static $componentSettingsFormat = array(
		'enabled' => array('type' => 'boolean', 'title' => 'Включить компиляцию')
	);


	// Compile
	// -------
	public function compile() {

//		if (static::$settings['enabled'] == true) {
			$this->compileCSS();
			$this->compileJS();
//		}
	}

	// Compole CSS into single file
	// ----------------------------
	public function compileCSS() {

		// Data
		// ----
		$needCompilation = false;

		// Get all js files
		// ----------------
		$cssFiles = \Loader::$files['css'];

		// Get hash
		// --------
		$nameHash = '';
		$newestFileTime = 0;

		// Update time and hash
		// --------------------
		foreach($cssFiles as $css) {

			// Update time
			// -----------
			$cssTime = filemtime(__DR__.$css);
			if ($cssTime > $newestFileTime) $newestFileTime = $cssTime;

			// Update hash
			// -----------
			$nameHash .= $css;
		}

		// Recalc cache
		// ------------
		$nameHash = md5($nameHash);

		// Cached file name
		// ----------------
		$compiledFile = __DR__ . 'private/cache/css/compiled-' . $nameHash . '.css';

		// Modification time
		// -----------------
		$compiledFileTime = 0;

		// Remember file modification time
		// -------------------------------
		if (file_exists($compiledFile)) {

			// Recache
			// -------
			if (filemtime($compiledFile) >= $newestFileTime) {
				\Loader::$files['css'] = array('/private/cache/css/compiled-' . $nameHash . '.css');
				return;
			}

		}


		// If files are exists, collect them and join
		// ------------------------------------------
		if (!empty($cssFiles)) {

			// Collect content here
			// ---------------------
			$content = '';

			// Add each file to end
			// --------------------
			foreach($cssFiles as $css) {


				// Replace file path
				// -----------------
				$cssContent = file_get_contents(__DR__.$css);
				$basePath = dirname($css).'/';
				$cssContent = str_replace('{%BASE%}', $basePath, $cssContent);

				// Append content
				// --------------
				$content .= $cssContent."\n";
			}

			// Write content and update modification time
			// ------------------------------------------
			file_put_contents($compiledFile, $content);
			touch($compiledFile, $newestFileTime);

			// Replace js with compiled one
			// ----------------------------
			\Loader::$files['css'] = array('/compiled/compiled-'.$nameHash.'.css');
		}

	}


	// Compile JS into single file
	// ---------------------------
	public function compileJS() {

		// Data
		// ----
		$needCompilation = false;

		// Get all js files
		// ----------------
		$jsFiles = \Loader::$files['js'];

		// Get hash
		// --------
		$nameHash = '';
		$newestFileTime = 0;

		// Update time and hash
		// --------------------
		foreach($jsFiles as $js) {

			// Update time
			// -----------
			$jsTime = filemtime(__DR__.$js);
			if ($jsTime > $newestFileTime) $newestFileTime = $jsTime;

			// Update hash
			// -----------
			$nameHash .= $js;
		}

		// Recalc cache
		// ------------
		$nameHash = md5($nameHash);

		// Cached file name
		// ----------------
		$compiledFile = __DR__ . 'private/cache/js/compiled-' . $nameHash . '.js';

		// Modification time
		// -----------------
		$compiledFileTime = 0;

		// Remember file modification time
		// -------------------------------
		if (file_exists($compiledFile)) {

			// Recache
			// -------
			if (filemtime($compiledFile) >= $newestFileTime) {
				\Loader::$files['js'] = array('/private/cache/js/compiled-' . $nameHash . '.js');
				return;
			}

		}


		// If files are exists, collect them and join
		// ------------------------------------------
		if (!empty($jsFiles)) {

			// Collect content here
			// ---------------------
			$content = '';

			// Add each file to end
			// --------------------
			foreach($jsFiles as $js) {

				// Append content
				// --------------
				$content .= file_get_contents(__DR__.$js)."\n";
			}

			// Write content and update modification time
			// ------------------------------------------
			file_put_contents($compiledFile, $content);
			touch($compiledFile, $newestFileTime);

			// Replace js with compiled one
			// ----------------------------
			\Loader::$files['js'] = array('/private/cache/css/compiled-' . $nameHash . '.js');
		}

	}

}
