<?php

class LoginForm extends CFormModel {

    public $username;
    public $password;
    public $rememberMe;
    private $_identity;
    private $verified;

    public function rules() {
        return array(
            array('username, password', 'required'),
            array('rememberMe', 'boolean'),
            array('password', 'authenticate'),
        );
    }

    public function attributeLabels() {
        return array(
            'rememberMe' => 'Remember me',
        );
    }

    public function validateEmail($attribute, $params) {
        if (!User::model()->exists('email=:email', array(':email' => $this->username)))
            $this->addError('email', 'No user found with this email');
    }

    public function authenticate($attribute, $params) {
        if (!$this->hasErrors()) {
            $this->_identity = new UserIdentity($this->username, $this->password);
            if (!$this->_identity->authenticate())
                $this->addError('password', 'Incorrect email or password');
        }
    }

    public function login() {
        if ($this->_identity === null) {
            $this->_identity = new UserIdentity($this->username, $this->password);
            $this->_identity->authenticate();
        }

        if ($this->_identity->errorCode === UserIdentity::ERROR_NONE) {
            $duration = $this->rememberMe ? 3600 * 24 * 7 : 0; // 7 days
            Yii::app()->user->login($this->_identity, $duration, true);
            return true;
        } else
            return false;
    }

}
