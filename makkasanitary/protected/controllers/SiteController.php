<?php

class SiteController extends AppController {

    public $layout = 'home';

    public function actionIndex() {
        $this->redirect(array('site/message'));
    }

    public function actionMessage() {
        $this->setLayout('admin');
        $this->setHeadTitle("Message");
        $this->setPageTitle("Message");
        $this->setCurrentPage(AppUrl::URL_SITE_MESSAGE);
        $this->render('message');
    }

    public function actionContact() {
        $this->setLayout('admin');
        $this->setHeadTitle("Contact");
        $this->setPageTitle("Give Me Feedback");
        $this->setCurrentPage(AppUrl::URL_SITE_CONTACT);
        $model = new Contact();

        if (isset($_POST['submitContact'])) {
            try {
                if (!$model->validate()) {
                    throw new Exception(Yii::t('App', CHtml::errorSummary($model)));
                }

                $to = 'rakibhasan880@gmail.com';
                $subject = $model->subject;
                $message = $model->message;
                $headers = 'From: ' . $model->email . "\r\n" .
                        'Reply-To: ' . $model->email . "\r\n" .
                        'X-Mailer: PHP/' . phpversion();

                mail($to, $subject, $message, $headers);

                /* if () {
                  Yii::app()->user->setFlash('success', 'Mail send successfull!');
                  } else {
                  throw new Exception(Yii::t('App', 'Error while sending mail. Please try again!'));
                  } */

                Yii::app()->user->setFlash('success', 'Mail send successfull!');
            } catch (Exception $e) {
                Yii::app()->user->setFlash('error', $e->getMessage());
            }
        }

        $this->model['model'] = $model;
        $this->render('contact', $this->model);
    }

    public function actionClear_cache() {
        try {
            if (!Yii::app()->cache->flush()) {
                throw new CException(Yii::t("App", "Could not clear all cache data"));
            }

            Yii::app()->user->setFlash("success", "Page refresh successfull");
        } catch (CException $e) {
            Yii::app()->user->setFlash("danger", $e->getMessage());
        }

        if (!empty(Yii::app()->request->urlReferrer)) {
            $this->redirect(Yii::app()->request->urlReferrer);
        } else {
            $this->redirect($this->createUrl(AppUrl::URL_DASHBOARD));
        }
        Yii::app()->end();
    }

}
