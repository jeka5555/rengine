<?php

namespace Core\Forms;

class UserRecoverPassword extends \Form {


	public static $component = array('id'  => 'user-recover-password');


    public $format = array(
        'email' => array('title' => 'Адрес электронной почты', 'type' => 'text', 'validator' => array('email', 'emailIsExists'))
    );
    public $buttons = array(array('id' => 'submit', 'type' => 'submit', 'title' => 'Отправить'));

    // Email must not exists
    // ---------------------
    public function validatePropertyEmail($args) {
        $usersClass = \Core::getComponent('class', 'user');
        $usersWithEmail = $usersClass::findOne(array('query' => array('primaryEmail' => @ $args['value'])));
        if (empty($usersWithEmail)) return array('Не найден указанный e-mail');
        return true;
    }


    // Submit form
    // -----------
    public function submit() {

        // Validate
        // --------
        $result = parent::submit();
        if ($result !== true) return $result;

        // Need user
        // ---------
        $usersClass = \Core::getClass('user');
        $user = $usersClass::findOne(array('query' => array('primaryEmail' => $this->value['email'])));

        // Wrong
        // -----
        if (empty($user)) {
            \Core::getModule('flash-messages')->add(array('text' => 'Указанный адрес электронной почты не зарегистрирован', 'type' => 'error'));
            return false;
        }

        // Create the key
        // --------------
        $recoverKey = substr(md5(uniqid()), 0, 12);

        // Modify an user
        // -------------
        $user->recoverKey = $recoverKey;
        $user->save();


        // Отправляем e-mail c кодом восстановления пароля
        // ------------------

        $emailContent = \Templates::get('email-user-recover', array(
            'link' => 'http://'.$_SERVER['HTTP_HOST'].'/users/new-password?key='.$recoverKey,
            'key' => $recoverKey
        ));

        $emailModule = \Core::getModule('email');
        $result = $emailModule->send(array(
            'to' => $user->primaryEmail,
            'from' => 'no-reply@'.$_SERVER['HTTP_HOST'],
            'message' => $emailContent,
            'task' => 'Запрос на восстановление пароля на сайте '.$_SERVER['HTTP_HOST']
        ));

        // Notify and go
        // -------------
        \Core::getModule('flash-messages')->add(array('text' => 'Сообщение содержащее ключ для восстановления пароля выслано Вам', 'type' => 'error'));

        \Events::send('setLocation', '/users/new-password');

    }

}