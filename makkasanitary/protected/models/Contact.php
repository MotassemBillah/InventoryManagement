<?php

class Contact extends CFormModel {

    public $username;
    public $email;
    public $subject;
    public $message;

    public function rules() {
        return array(
            array('username, email, subject, message', 'required'),
            array('email', 'email'),
        );
    }

}
