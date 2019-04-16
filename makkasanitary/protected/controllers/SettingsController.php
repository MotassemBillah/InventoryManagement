<?php

class SettingsController extends AppController {

    //public $layout = 'admin';

    public function beforeAction($action) {
        $this->actionAuthorized();
        return true;
    }

    public function actionIndex() {
        $this->checkUserAccess('settings');
        $this->setHeadTitle("Settings");
        $this->setPageTitle("General Settings");
        $this->setCurrentPage(AppUrl::URL_SETTINGS);

        $model = new Settings();
        $data = $model->findByPk(1);

        if (isset($_POST['Settings'])) {
            $paymentModes = Yii::app()->request->getPost("payment_mode");
            $data->attributes = $_POST['Settings'];
            $data->site_name = !empty($_POST['Settings']['site_name']) ? $_POST['Settings']['site_name'] : NULL;
            $data->title = !empty($_POST['Settings']['title']) ? $_POST['Settings']['title'] : NULL;
            $data->description = !empty($_POST['Settings']['description']) ? $_POST['Settings']['description'] : NULL;
            $data->author = !empty($_POST['Settings']['author']) ? $_POST['Settings']['author'] : NULL;
            $data->author_email = !empty($_POST['Settings']['author_email']) ? $_POST['Settings']['author_email'] : NULL;
            $data->author_phone = !empty($_POST['Settings']['author_phone']) ? $_POST['Settings']['author_phone'] : NULL;
            $data->author_mobile = !empty($_POST['Settings']['author_mobile']) ? $_POST['Settings']['author_mobile'] : NULL;
            $data->other_contacts = !empty($_POST['Settings']['other_contacts']) ? $_POST['Settings']['other_contacts'] : NULL;
            $data->author_address = !empty($_POST['Settings']['author_address']) ? AppHelper::capFirstWord($_POST['Settings']['author_address']) : NULL;
            $data->auto_pricing = isset($_POST['Settings']['auto_pricing']) ? $_POST['Settings']['auto_pricing'] : 0;
            $data->sendmail = isset($_POST['Settings']['sendmail']) ? $_POST['Settings']['sendmail'] : 0;
            $data->sendsms = isset($_POST['Settings']['sendsms']) ? $_POST['Settings']['sendsms'] : 0;
            $data->payment_modes = !empty($paymentModes) ? json_encode($paymentModes) : NULL;
            $data->vat = !empty($_POST['Settings']['vat']) ? $_POST['Settings']['vat'] : NULL;
            $data->profit_count = !empty($_POST['Settings']['profit_count']) ? $_POST['Settings']['profit_count'] : NULL;
            $data->page_size = !empty($_POST['Settings']['page_size']) ? $_POST['Settings']['page_size'] : NULL;
            $data->currency = !empty($_POST['Settings']['currency']) ? $_POST['Settings']['currency'] : NULL;
            $data->theme = !empty($_POST['Settings']['theme']) ? $_POST['Settings']['theme'] : NULL;
            $data->language = !empty($_POST['Settings']['language']) ? $_POST['Settings']['language'] : NULL;
            //$data->timezone = !empty($_POST['Settings']['timezone']) ? $_POST['Settings']['timezone'] : NULL;
            //$data->datetime_format = !empty($_POST['date_format']) ? $_POST['date_format'] : NULL;

            $_transaction = Yii::app()->db->beginTransaction();
            try {
                if (!$data->validate()) {
                    throw new CException(Yii::t('App', CHtml::errorSummary($data)));
                }

                if (!empty($_FILES['Settings']['name']['favicon'])) {
                    $_filename = $_FILES['Settings']['name']['favicon'];
                    $_tmpfilename = $_FILES['Settings']['tmp_name']['favicon'];
                    $_filetype = $_FILES['Settings']['type']['favicon'];
                    $_filesize = $_FILES['Settings']['size']['favicon'];
                    $_size = ($_filesize / 1024) . " KB";
                    $_fileerror = $_FILES['Settings']["error"]['favicon'];

                    $_savepath = Yii::getPathOfAlias('webroot') . '/uploads/';
                    $allowedExts = array("jpg", "jpeg", "png", "gif");
                    $temp = explode(".", $_filename);
                    $extension = end($temp);
                    $_newfilename = 'fav_' . AppHelper::getUnqiueKey() . '.' . $extension;

                    if (!empty($data->favicon)) {
                        unlink($_savepath . $data->favicon);
                    }
                    $data->favicon = $_newfilename;
                    move_uploaded_file($_tmpfilename, $_savepath . $_newfilename);
                }

                if (!empty($_FILES['Settings']['name']['logo'])) {
                    $_filename = $_FILES['Settings']['name']['logo'];
                    $_tmpfilename = $_FILES['Settings']["tmp_name"]['logo'];
                    $_filetype = $_FILES['Settings']["type"]['logo'];
                    $_filesize = $_FILES['Settings']["size"]['logo'];
                    $_size = ($_filesize / 1024) . " KB";
                    $_fileerror = $_FILES['Settings']["error"]['logo'];

                    $_savepath = Yii::getPathOfAlias('webroot') . '/uploads/';
                    $allowedExts = array("jpg", "jpeg", "png", "gif");
                    $temp = explode(".", $_filename);
                    $extension = end($temp);
                    $_newfilename = 'logo_' . AppHelper::getUnqiueKey() . '.' . $extension;

                    if (!empty($data->logo)) {
                        unlink($_savepath . $data->logo);
                    }
                    $data->logo = $_newfilename;
                    move_uploaded_file($_tmpfilename, $_savepath . $_newfilename);
                }

                if (!$data->save()) {
                    throw new CException(Yii::t("App", "Error while saving data."));
                }

                $_transaction->commit();
                Yii::app()->user->setFlash('success', 'Settings saved successfully.');
                $this->refresh();
            } catch (CException $e) {
                $_transaction->rollback();
                Yii::app()->user->setFlash('error', $e->getMessage());
            }
        }

        $this->model['model'] = $data;
        $this->render('index', $this->model);
    }

    public function actionRemove_image() {
        $this->is_ajax_request();
        $response = array();
        $rel = Yii::app()->request->getPost('rel');
        $imgInfo = Yii::app()->request->getPost('info');

        $model = new Settings();
        $data = $model->findByPk(1);

        $_transaction = Yii::app()->db->beginTransaction();
        try {
            if ($rel == "logo") {
                $data->logo = NULL;
            } else {
                $data->favicon = NULL;
            }

            if (!$data->save()) {
                throw new CException(Yii::t("App", "Error while saving data."));
            }

            $image = Yii::getPathOfAlias('webroot') . '/uploads/' . $imgInfo;
            if (file_exists($image)) {
                if (!unlink($image)) {
                    throw new CException(Yii::t("App", "Error while removing " . ucfirst($rel)));
                }
            }

            $_transaction->commit();
            $response['success'] = true;
            $response['message'] = ucfirst($rel) . " remove successfull.";
        } catch (CException $e) {
            $_transaction->rollback();
            $response['success'] = false;
            $response['message'] = $e->getMessage();
        }

        echo json_encode($response);
        return json_encode($response);
    }

}
