<?php

class LoginController extends AppController {

    public $layout = 'home';

    public function beforeAction($action) {
        return true;
    }

    public function actionIndex() {
        if (Yii::app()->user->isGuest) {
            $this->setHeadTitle("Login");
            $this->setCurrentPage(AppUrl::URL_LOGIN);
            //$this->addJs('views/save_visitor.js');
            $this->addJs('views/user/login.js');

            $_model = new LoginForm();
            $this->render('index', array('model' => $_model));
        } else {
            $this->redirect($this->createUrl(AppUrl::URL_USER_PROFILE));
            Yii::app()->end();
        }
    }

}
