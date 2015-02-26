<?php

namespace RAN\Users\Templates;

class EmailUserRegistration extends \Template {

	// Component registration
	// ----------------------
	public static $component = array('id' => 'email-user-registration');

	// Render
	// ------
	public function render() {

		$result ='<!DOCTYPE html>
		<body>
			<p>Ключ для активации аккаунта <b>'.@ $this->key.'</b></p>
			<p>Либо перейдите по ссылке <a href="'.@ $this->link.'">'.@ $this->link.'</a></p>
		</body>';

		return $result;
	}


}