<?php

namespace Core\Nodes;

class Reference extends \Node{

	// Component
	// ---------
	public static $component = array(
		'id' => 'reference',
		'title' => 'Ссылка на узел'
	);

	// Node data format
	// ----------------
	public static function getNodeDataFormat() {
		return array(
			'node' => array('type' => 'object', 'title' => 'Узел ссылки', 'class' => 'node'),
			'referenceOptions' => array('type' => 'record', 'title' => 'Параметеры ссылки', 'mode' => 'full', 'format' => array(
				'useOriginalPath' => array('type' => 'boolean', 'title' => 'Использовать оригинальные пути'),
				'useOriginalTitle' => array('type' => 'boolean', 'title' => 'Использовать оригинальное имя')
			))
		);
	}


	// Get URI
	// -------
	public function getURI() {

		// Get node
		// --------
		if (empty($this->data['node'])) return parent::getURI();

		$nodeClass = \Core::getClass('node');
		$node = $nodeClass::findPK($this->data['node']);
		if (empty($node)) return parent::getURI();

		// Return original node URI
		// ------------------------
		$nodeObject = \Node::getNodeObject($node);
		return $nodeObject->getURI();
	}
}
