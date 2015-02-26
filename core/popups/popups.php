<?php

class Popups extends \Module {

	// Register module component
	// -------------------------
	public static $component = array(
		'id' => 'popups',
		'title' => 'Всплывающие окна'
	);

	// Show popup
	// ----------
	public static function show($popupID, $data = array()) {

		// Get popup
		// ---------
		$popup = \Core::getComponent('popup', $popupID);

		if (empty($popup)) return;

		// Get popup instance
		// ------------------
		$popupInstance = $popup::getInstance($data);

		// Generate class and id
		// ---------------------
		$popupObjectID = 'popup'.uniqid();

		// Generate data
		// -------------
		$popupData = array(
			'htmlClass' => $popupInstance->htmlClass,
            'dialogClass' => $popupInstance->dialogClass,

			'popupID' => 'popup-'.$popupID,
			'class' => $popupInstance->class,
			'showTitle' => $popupInstance->showTitle,
			'title' => $popupInstance->title,
			'type' => $popupInstance->type,
			'modal' => $popupInstance->modal,
            'maximizable' => $popupInstance->maximizable,
			'minimizable' => @$popupInstance->minimizable,
			'collapsable' => @$popupInstance->collapsable,
			'resizable' => @$popupInstance->resizable,

			'buttons' => $popupInstance->buttons,
			'content' => $popupInstance->content,

			'width' => $popupInstance->width,
			'height' => $popupInstance->height,
			'maxWidth' => $popupInstance->maxWidth,
			'maxHeight' => $popupInstance->maxHeight,

            'closeOnEscape' => $popupInstance->closeOnEscape,
            'overflowHtmlHidden' => @$popupInstance->overflowHtmlHidden,

			'data' => first_var($data, $popupInstance->data),
			'controller' => $popupInstance->controller
		);

		// Make popup
		// ----------
		\Events::send('addScript', ' var '.$popupObjectID.' = new Popup('.json_encode($popupData).');');

	}
}
