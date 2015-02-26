<?php

namespace Core\Sites\Applications;

class NodeWebApplication extends \Core\Sites\Applications\WebApplication {

	// Component
	// ---------
	public static $component = array(
		'type' => 'application',
		'id' => 'node-web-application',
		'title' => 'Node-based приложение'
	);

	// Process route
	// -------------
	public function route($request = null) {

		$path = $request->path;

		// API hooks?
		// ----------
		if (@ $request->path[0] == 'module') {

			// If no module ID, return
			// ------------------------
			if (empty($request->path[1])) break;

			// Get module instance
			// -------------------
			$module = \Core::getModule($request->path[1]);
			if (empty($module)) return;

			// Get module action
			// -----------------
			$result = $module->action( $request->path[2], $request->data, $request->path);

			// Return result
			// -------------
			return $result;

		}

		// Get site class
		// --------------
		$nodeClass = \Core::getClass('node');

		// Get children nodes
		// ------------------
		$nodesClass = \Core::getClass('node');
		$children = $nodesClass::find(array(
			'query' => array(
				'parent' => null,
				'hidden' => array('$ne' => true),
				'isSystem' => true
			)
		));

		// Process each system children node
		// ---------------------------------
		if (!empty($children)) {
			foreach($children as $child) {
				$childNode = \Node::getNodeObject($child);
				$childNode->processPath($path, 0);
			}
		}

		//  Get site nodes
		// ---------------
		$sites = $nodesClass::find(array(
			'query' => array(
				'type' => 'site',
				'parent' => null,
				'hidden' => array('$ne' => true),
				'isSystem' => array('$ne' => true)
			)
		));

		// Process children node
		// ---------------------
		if (!empty($sites)) {

			foreach ($sites as $siteObject) {

				// Get site object
				// ---------------
				$siteNode = \Node::getNodeObject($siteObject);

				// If site is enabled, process
				// ---------------------------
				if ($siteNode->isEnabled()) $site = $siteNode;
			}
		}

		// Error, site wasn't found
		// ------------------------
		if (empty($site)) {
			return;
		}

		// Find alias node
		// ---------------
		$aliasNode = $nodesClass::findOne(array('query' => array('seo.alias' => $request->uri)));
		if(!empty($aliasNode)) {
			$aliasNode = \Node::getNodeObject($aliasNode);
			$path = $aliasNode->getRequestPath();
		}

		// Or process site
		// ---------------
		$site->processPath($path, 0);

	}


}
