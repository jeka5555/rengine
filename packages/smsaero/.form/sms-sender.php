<?php

namespace SMSaero\Forms;

class SMSSender extends \Form {

	// Component
	// ---------
	public static $component = array(
		'id' => 'sms-sender',
		'title' => 'Форма отправки SMS сообщения'
	);

	// Format
	// ------
	public function getFormat() {
		return array(
			'to' => array('type' => 'list', 'title' => 'Список получателей', 'format' => array(
				'type' => 'text'
			)),
			'text' => array('type' => 'textarea', 'title' => 'Текст сообщения'),
		);
	}

	// Form buttons
	// ------------
	public $buttons = array(array('id' => 'submit', 'type' => 'submit', 'title' => 'Отправить'));


	// Submit form
	// -----------
	public function submit() {

  	if (empty($this->value['to'])) {
  		\Core::getModule('flash-messages')->add(array('text' => 'Ошибка отправки! Не указаны получатели.'));
			return false;
		}
		
		$smsaeroModule = \Core::getModule('smsaero');
  	foreach($this->value['to'] as $item) {
			
			$send = $smsaeroModule->send(array(
				'to' => $item,
				'message' => $this->value['text']
			));	
			
			if (@$send)
				\Core::getModule('flash-messages')->add(array('text' => 'SMS на номер <strong>'.$item.'</strong> отправлена успешно'));
			else
				\Core::getModule('flash-messages')->add(array('text' => 'Ошибка при отправки SMS на номер <strong>'.$item.'</strong>'));
		}


		return true;
	}

}
