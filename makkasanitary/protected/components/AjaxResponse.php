<?php

class AjaxResponse {

    public $requestFor = '';
    public $requestBy = null;
    public $responseType = 'json';
    public $success = false;
    public $message = '';
    public $title = '';
    public $error = true;
    public $exception = '';
    public $authorized = true;
    public $redirectTo = '';
    public $dataSet = false;
    public $dataObj = false;

    function __construct() {
        $this->requestFor = Yii::app()->request->requestUri;
        
        if(Yii::app()->user->isGuest)
            $this->requestBy = Yii::app()->user->guestName;
        else
            $this->requestBy = Yii::app()->user->name;
    }

}