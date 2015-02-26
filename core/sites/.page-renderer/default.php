<?php

namespace Core\Pages\PageRenderers;

class PageRenderer extends \Component {

	// Component
	// ------------------------
	public static $component = array('id' => 'default', 'type' => 'page-renderer');

	// Get encoding
	// ------------
	public function getEncoding() {
		$encoding = first_var(@$GLOBALS['encoding'], @ \Core::getInstance()->settings['encoding'], 'utf-8');
		return $encoding;
	}

	// Render heading
	// --------------
	public function renderHeading() {

		// Get header
		// ----------
		$encoding = $this->getEncoding();
		header('Content-type: text/html;charset='.$encoding);

		// Title
		// -----
		$content .= '<title>'.first_var(@ $this->title, @ \Core::$settings['title'], 'REngine G4').'</title>';

		// Encoding
		// --------
		$content .= '<meta http-equiv="Content-Type" content="text/html; charset='.$encoding.'" />';

		// Favicon
		// --------
		if (!empty($this->favicon)) {
			$content .= '<link rel="icon" type="image/png" href="'.$this->favicon.'">';
		}
		
		if (!empty($this->headContentAddin)) {
			$content .= $this->headContentAddin; 
		}

		return $content;

	}

	// Render assets
	// -------------
	public function renderAssets() {

		// CSS
		// ---
		$cssContent = '';
		if (!empty($this->assets['css']))
		foreach($this->assets['css'] as $css) {
			$cssContent .= '<link type="text/css" rel="stylesheet" href="'.$css.'">'."\n";
		}

		// Scripts
		// -------
		$jsContent = '';
		if (!empty($this->assets['js']))
		foreach($this->assets['js'] as $js) {
			$jsContent .= '<script type="text/javascript" charset="utf-8" src="'.$js.'"></script>'."\n";
		}


		// Return content
		// --------------
		return $cssContent.$jsContent;

	}


	// Render SEO content
	// ------------------
	private function renderSEOContent() {

		$seoContent = '';

		// Keywords
		// --------
		$keywordsContent = '';
		if (!empty($this->keywords)) {
			$keywordsContent = join(",", array_unique($this->keywords));
		}
		$seoContent .= '<meta name="keywords" content="'.$keywordsContent.'"/>';

		// Page description
		// ----------------
		$descriptionContent = '';
		if (!empty($this->description)) {
			$descriptionContent = $this->description;
		}
		$seoContent .= '<meta name="description" content="'.$descriptionContent.'"/>';

		// Return
		// ------
		return $seoContent;

	}


	// Render scripts
	// --------------
	private function renderScript() {

		// Collect core args
		// -----------------
		$coreArgs = array(
			'location' => \Core::getApplication()->request->uri,
			'siteID' => \Core::getApplication()->data['siteID'],
			'siteNodeID' => \Core::getApplication()->data['siteNodeID']
		);

		// Complete start-up script
		// ------------------------
		return '<script type="text/javascript">
			$(document).ready(function() {
				Core.start('.json_encode($coreArgs).');
				Core.assets = {"js" : '.json_encode($this->assets['js']).', "css" : '.json_encode($this->assets['css']).' };
				'.join("\n", $this->script).'
			});
		</script>';
	}


	// Render final page
	// -----------------
	public function render() {

		// Get page html class
		// -------------------
		$bodyClass = 'page';
		if (!empty($this->template)) $bodyClass .= ' page-template-'.$this->template;

		if (!empty($this->htmlClasses)) $classAddin = ' '.join(" ", $this->htmlClasses);
		
		$bodyClass = 'class="'.$bodyClass.@$classAddin.'"';

		// Whole page content
		// ------------------
		$content = '<!DOCTYPE html>
			<html>
			<head>'.
				@ $this->renderHeading().
				@ $this->renderSEOContent().
				@ $this->renderAssets().
				@ $this->renderScript().
			'</head>
			<body '.$bodyClass.'>'.
				@ $this->content.
				@ $this->autoContent.
				@ $this->renderEndScript().
			'</body>
			</html>';


		// Return content
		// --------------
		return $content;

	}

	// Render content
	// --------------
	public function renderContent() {
		$templateID = first_var(@ $this->template, 'page');
		$this->content = \Templates::get($templateID);
		$this->autoContent = \Widgets::get('block', array('id' => 'auto'));
	}

	// Render end script
	// -----------------
	private function renderEndScript() {
		return '<script type="text/javascript">
			$(document).ready(function() {'.join("\n", $this->endScript).'});
		</script>';
	}

	// Render auto content
	// -------------------
	public function prerender() {
		$widgetsModule = \Core::getModule('widgets');

		// If some widgets are set to page
		// -------------------------------
		if (!empty($widgetsModule->widgets)) {

			// Call prerender function of them
			// --------------------------------
			foreach($widgetsModule->widgets as $widgetID => $widget) {

				// Check widget for output
				// ------------------------
				if (!empty($widget->enableRules)) {

					// If test not passed, remove widget from queue
					// --------------------------------------------
					$test = \Rules::check($widget->enableRules);

					if (! $test ) {
						unset($widgetsModule->widgets[$widgetID]);
					}
				}
				$widget->preRender();
			}
		}
	}


}
