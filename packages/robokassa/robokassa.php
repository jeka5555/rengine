<?php

namespace Modules;

class Robokassa extends \Module {

    public static $component = array(
        'id' => 'robokassa',
        'title' => 'On-line платежи через систему Robokassa',

        // Module settings
        // ---------------
        'hasSettings' => true,
    );

    // Settings format
    // ---------------
    public static $componentSettingsFormat = array(
        'login' => array('type' => 'text', 'title' => 'Логин Robokassa'),
        'password-1' => array('type' => 'text', 'title' => 'Пароль 1'),
        'password-2' => array('type' => 'text', 'title' => 'Пароль 2'),
        'serviceURI' => array('type' => 'text', 'title' => 'Путь до API сервиса'),
    );

    // Settings
    // --------
    public static $settings = array(

        // Authenticale settings
        // ---------------------
        'login' => '',
        'password-1' => '',
        'password-2' => '',

        // Robokassa API URI (http://test.robokassa.ru/Index.aspx)
        // -----------------
        'serviceURI' => 'https://merchant.roboxchange.com/Index.aspx',

    );


    private function checkSignature() {
        // Get sent parameters
        // -------------------
        $result['out_summ'] = first_var(@$_POST["OutSum"], @$_GET["OutSum"]);
        $result['inv_id'] = first_var(@$_POST["InvId"], @$_GET["InvId"]);
        $result['shpPaymentCode'] = first_var(@$_POST['ShpPaymentCode'], @$_GET["ShpPaymentCode"]);
        $result['crc'] = strtoupper(first_var(@$_POST["SignatureValue"], @$_GET["SignatureValue"]));

        // Check CRC for request
        // ---------------------
        $my_crc = strtoupper(md5($result['out_summ'].':'.$result['inv_id'].':'.static::$settings['password-2'].':ShpPaymentCode='.$result['shpPaymentCode']));

        // Chech sign
        // ----------
        if($my_crc != $result['crc']) die("wrong key");

        return $result;
    }

    // Process payment
    // ---------------
    public function actionProcess($args = array()) {


        // Get correct numerical value
        // ---------------------------
        $value = (int) $args['value'];
        $productID = 0;

        // Require user
        // ------------
        $userID = @ \Core::getModule('users')->user->_id;
        if (empty($userID)) {
            \FlashMessages::add(array('type' => 'error', 'text' => 'Вы не можете осуществить платежную операцию, так как вы не вошли на сайт'));
            return false;
        }

        // Require correct not zero value
        // ------------------------------
        if ($value == 0) {
            \FlashMessages::add(array('type' => 'error', 'text' => 'Указана неверная сумма для оплаты. Она должна быть целыми положительным числом'));
            return false;
        }

        // Create new payment
        // ------------------
        $paymentClass = \Core::getComponent('class', 'user-payment');
        $payment = $paymentClass::getInstance(array(
            'user' => $userID,
            'value' => $value,
            'status' => 'waiting',
            'type' => 'income',
            'time' => time(),
            'description' => 'Зачисление на счет'
        ));

        // Save payment
        // ------------
        $payment->save();

        // Calculate CRC
        // -------------
        $crc = md5(
            static::$settings['login'].':'.
            $value.':'.
            $productID.':'.
            static::$settings['password-1'].':'.
            'ShpPaymentCode='.$payment->_id
        );

        // Fill request
        // -----------
        $data = array(
            'MrchLogin' => static::$settings['login'],
            'OutSum' => $value,
            'InvId' => $productID,
            'Desc' => 'Внесение денег на личный счет портала oil-traders.ru',
            'SignatureValue' => $crc,
            'Culture' => 'ru',
            'ShpPaymentCode' => $payment->_id
        );

        // Goto robokassa
        // --------------
        \Events::send('setLocation', static::$settings['serviceURI'].'?'.http_build_query($data));
    }


    // Result
    // ---------------
    public function actionResult($args = array()) {

        // Проверка подписи
        // -------------------
        $result = $this->checkSignature();

        // Get payment data with this code
        // -------------------------------
        $paymentClass = \Core::getComponent('class', 'user-payment');
        $payment = $paymentClass::findPK($result['shpPaymentCode']);

        if (empty($payment)) die("activation request is not found");

        // Update object, set activation flag to paid
        // ----------------------------------------------
        $payment->status = 'paid';
        $payment->time = time();
        $payment->save();

        // Load user
        // ---------
        $userModule = \Core::getComponent('class','user');
        $user = $userModule::findPK($payment->user);
        if (empty($user))
            die("user is not found");

        // Save user's balance
        // -------------------
        $user->balance = $user->balance + $payment->value;
        $user->save();

        die('OK'.@$result['inv_id'].'\n');
    }


    // Success
    // -------
    public function actionSuccess($args = array()) {
        \Events::send('setLocation', '/profile/'.@\Core::getModule('users')->user->_id.'/payments');
    }

    // Fail
    // ----
    public function actionFail($args = array()) {

        // Get payment data with this code
        // -------------------------------
        $paymentClass = \Core::getComponent('class', 'user-payment');
        $payment = $paymentClass::findPK($_POST['ShpPaymentCode']);

        if (empty($payment)) die("activation request is not found");

        // Update object, set activation flag to paid
        // ----------------------------------------------
        $payment->status = 'rejected';
        $payment->time = time();
        $payment->save();

        \Events::send('setLocation', '/profile/'.@\Core::getModule('users')->user->_id.'/payments');
    }

}