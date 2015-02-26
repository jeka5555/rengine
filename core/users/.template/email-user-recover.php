<?php

namespace RAN\Users\Templates;

class EmailUserRecover extends \Template {

	// Component registration
	// ----------------------
	public static $component = array('id' => 'email-user-recover');

	// Render
	// ------
	public function render() {

		$result ='<!DOCTYPE html>
		<body>
			<p>Ключ для восстановления пароля: <b>'.@ $this->key.'</b></p>
			<p>Либо перейдите по ссылке <a href="'.@ $this->link.'">'.@ $this->link.'</a></p>
		</body>';

		return $result;
	}


}