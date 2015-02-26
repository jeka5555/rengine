<?php

namespace Mandrill\Modules;

class Mandrill extends \Module {

	// Component registration
	// ----------------------
	public static $component = array('id' => 'mandrill', 'hasSettings' => true, 'title' => 'Рассылки Mandrill');

	// Settings
	// --------
	public static $settings = array(
		'mandrillURI' => 'https://mandrillapp.com/api/1.0/messages/send.json',
		'key' => '',
		'fromName' => '',
		'fromEmail' => ''
	);


	public static $componentSettingsFormat = array(
		'mandrillURI' => array( 'type' => 'text', 'title' => 'Ссылка до API Mandrill'),
		'key' => array('type' => 'text', 'title' => 'Ключ авторизации'),
		'fromName' => array('type' => 'text', 'title' => 'Имя отправителя по-умолчанию'),
		'fromEmail' => array('type' => 'text', 'title' => 'E-mail отправителя по-умолчанию'),
	);

	// Send email
	// ----------
	public function install($args = array()) {
		return true;
	}

	// Send email
	// ----------
	public function send($args = array()) {

		// Query
		// -----
		$postArray = array(
			'key' => static::$settings['key'],
			'message' => array(
				'html' => @ $args['content'],
				'subject' => first_var(@ $args['title'], ''),
				'from_email' => first_var(@ $args['fromEmail'], static::$settings['fromEmail']),
				'from_name' => first_var(@ $args['fromName'], $this->settings['fromName']),
				'to' => array(array('email' => @ $args['to'])),
				'preserve_recipients' => false,
			)
		);

		// Additional tags
		// ---------------
		if (!empty($args['tags'])) $postArray['message']['tags'] = $args['tags'];
		if (!empty($args['merge_vars'])) $postArray['message']['merge_vars'] = $args['merge_vars'];

		// Make string to post
		// -------------------
		$postString = json_encode($postArray);

		// Send mail
		// ---------
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, static::$settings['mandrillURI']);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true );
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postString);

		// Return result
		// -------------
		$result = curl_exec($ch);
		return $result;

	}
}
