<?php

namespace Core\Forms;

class UserNewPassword extends \Form {

	// Component
	// ---------
	public static $component = array('id' => 'user-new-password');

	// Get format
	// ----------
	public function getFormat() {

		$format = array();

		// If we have an key, just hold it
		// -------------------------------
		$format['key'] = array('type' => 'text', 'title' => 'Ключ для восстановления', 'hint' => 'Ключ. отправленный вам на email, который гарантирует что восстановление пароля осуществляется для вашего профиля.');

		// Rest of form
		// ------------
		$format = array_merge($format, array(
			'email' => array('type' => 'text', 'title' => 'Ваш email или телефон', 'validator' => 'required'),
            'password' => array('type' => 'password', 'title' => 'Пароль', 'hint' => 'Пароль должен содержать от 6 до 16 символов'),
		));

		return $format;
	}

    public $buttons =  array(array('id' => 'submit', 'type' => 'submit', 'title' => 'Отправить'));

    // Validate password
    // --------------
    public function validatePropertyPassword($args) {
        if ($args['value']['passwordtype'] == 'manual') {
            if ($args['value']['password'] != $args['value']['repassword']) {
                return array('Введенные пароли не совпадают');
            }
            if (mb_strlen($args['value']['password']) < 6) return array('Слишком короткий пароль. Необходимо не менее 6 символов');
            if (mb_strlen($args['value']['password']) > 16) return array('Слишком длинный пароль. Необходимо не более 16 символов');
        } else if ($args['value']['passwordtype'] == 'generate') {
            return true;
        } else return array('Неверный формат данных');

        return true;
    }

	
	// Key is real
	// -----------
	public function validatePropertyKey($args) {

        if (empty($args['value'])) return array('Ключ для восстановления не должен быть пуст');

		$usersClass = \Core::getComponent('class', 'user');
		$usersWithEmail = $usersClass::findOne(array('query' => array('recoverKey' => @ $args['value'])));
		if (empty($usersWithEmail)) return array('Ключ для восстановления не существует. Попробуйте повторить процедуру восстановлеиня');
	}


    public function validatePropertyEmail($args) {

        $usersClass = \Core::getComponent('class', 'user');
        $usersWithEmail = $usersClass::findOne(array('query' => array(
            '$or' => array(
                array('primaryEmail' => @ $args['value']),
                array('phoneNumber' => @ $args['value']),
            )
        )));
        if (empty($usersWithEmail)) return array('Пользователь не найден');
    }



    // Submit form
    // -----------
    public function submit() {

        // Validate
        // --------
        $result = parent::submit();
        if ($result !== true) return $result;

        $userClass = \Core::getClass('user');
        $user = $userClass::findOne(array('query' => array(
            'recoverKey' => $this->value['key'],
            '$or' => array(
                array('primaryEmail' => @ $this->value['email']),
                array('phoneNumber' => @ $this->value['email']),
            )
        )));

        if ($this->value['password']['passwordtype'] == 'generate') {
            $this->value['password']['password'] = substr(md5(uniqid()), 0, 12);
        }

        $user->recoverKey = null;

        $user->password = $this->value['password']['password'];
        $user->save();

        // Отправляем уведомление с новым паролем
        // --------
        $noticeClass = \Core::getClass('notice');
        $notice = new $noticeClass(array(
            'text' => 'новый пароль на портале BOOKLYA: '.$this->value['password']['password'],
            'date' => time(),
            'user' => $user->_id,
        ));
        $notice->send();

        \Events::send('setLocation', '/users/login');

    }
}