<?php

class ErrorController extends AppController {

    public $layout = 'home';

    public function actionIndex() {
        $this->setLayout("error");
        $this->setHeadTitle("Error");

        $error = Yii::app()->errorHandler->error;

        switch ($error['code']) {
            case 404:
                $error['message'] = "Page not found.";
                break;
            case 500:
                $error['message'] = "Internal server error.";
                break;
            default :
                break;
        }

        $this->render('index', array('error' => $error));
    }

    public function actionMessage() {
        $this->setLayout('admin');
        $this->setHeadTitle("Error Message");
        $this->setPageTitle("Message");
        $this->setCurrentPage(AppUrl::URL_ERROR_MESSAGE);
        $this->render('message');
    }

}
