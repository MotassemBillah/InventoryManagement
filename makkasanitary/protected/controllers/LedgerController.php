<?php

class LedgerController extends AppController {

    public $layout = 'admin';

    public function beforeAction($action) {
        $this->actionAuthorized();
        return true;
    }

    public function actionIndex() {
        //$this->checkUserAccess('settings');
        $this->setHeadTitle("Ledger");
        $this->setPageTitle("Ledger");
        $this->setCurrentPage(AppUrl::URL_LEDGER);
        $this->addCss('datepicker.css');
        $this->addJs('datepicker.js');
        $this->addJs('views/ledger/list.js');

        $_model = new LedgerPayment();
        $criteria = new CDbCriteria();
        $count = $_model->count($criteria);
        $pages = new CPagination($count);
        $pages->pageSize = $this->page_size;
        $pages->applyLimit($criteria);
        $_dataset = $_model->with('items')->findAll();
        //AppHelper::pr($_dataset);

        $this->model['dataset'] = $_dataset;
        $this->model['pages'] = $pages;
        $this->render('index', $this->model);
    }

    public function actionCreate() {
        //$this->checkUserAccess('settings');
        $this->setHeadTitle("Ledger Create");
        $this->setPageTitle("Journal Create");
        $this->setCurrentPage(AppUrl::URL_LEDGER);
        $this->addCss('datepicker.css');
        $this->addJs('datepicker.js');
        $this->addJs('views/ledger/create.js');
        $this->render('create', $this->model);
    }

    public function actionEdit($id) {
        $this->setHeadTitle("Ledger Edit");
        $this->setPageTitle("Ledger Edit");
        $this->setCurrentPage(AppUrl::URL_LEDGER);
        $this->addCss('datepicker.css');
        $this->addJs('datepicker.js');
        $this->addJs('views/ledger/edit.js');

        $model = new LedgerPayment();
        $data = $model->with('items')->findByPk($id);

        $modelSubHead = new LedgerSubHead();
        $_dataset = $modelSubHead->with('particulers')->findAll("ledger_head_id=:ledger_head_id", array(":ledger_head_id" => $data->head_id));

        if (isset($_POST['btnLedgerPayment'])) {
            AppHelper::pr($_POST);
        }

        $this->model['model'] = $data;
        $this->model['dataset'] = $_dataset;
        $this->render('edit', $this->model);
    }

    public function actionPayment() {
        //$this->checkUserAccess('settings');
        $this->setHeadTitle("Ledger Payment");
        $this->setPageTitle("Ledger Payment");
        $this->setCurrentPage(AppUrl::URL_LEDGER_PAYMENT);
        $this->addCss('datepicker.css');
        $this->addJs('datepicker.js');

        $_model = new Payment();

        $settings = $this->getSettings();
        $payModes = json_decode($settings->payment_modes);

        $this->model['model'] = $_model;
        $this->model['payModes'] = $payModes;
        $this->render('payment', $this->model);
    }

    public function actionView($id) {
        //$this->checkUserAccess('settings');
        $this->setHeadTitle("Ledger");
        $this->setCurrentPage(AppUrl::URL_LEDGER_VIEW);

        $_model = new LedgerHead();
        $_data = $_model->with('sub_head', 'particulers')->findByPk($id);

        $_modelPayment = new LedgerPayment();
        $criteria = new CDbCriteria();
        $criteria->condition = "head_id='$id'";
        $criteria->params = array(":head_id" => $id);
        $_dataset = $_modelPayment->findAll($criteria);

        $this->setPageTitle("Ledger - " . $_data->name);
        $this->model['model'] = $_data;
        $this->model['dataset'] = $_dataset;
        $this->render('view', $this->model);
    }

    public function actionView_detail() {
        //$this->checkUserAccess('settings');
        $this->setHeadTitle("Ledger Head");
        $this->setPageTitle("Ledger Heads");
        $this->setCurrentPage(AppUrl::URL_LEDGER_VIEW_DETAIL);

        $_model = new LedgerHead();
        $_dataset = $_model->findAll();

        $this->model['dataset'] = $_dataset;
        $this->render('view_detail', $this->model);
    }

    public function actionDetail() {
        //$this->checkUserAccess('settings');
        $this->setHeadTitle("Ledger Detail");
        $this->setPageTitle("Ledger Detail");
        $this->setCurrentPage(AppUrl::URL_LEDGER_DETAIL);

        $_model = new LedgerPayment();
        $criteria = new CDbCriteria();
        $count = $_model->count($criteria);
        $pages = new CPagination($count);
        $pages->pageSize = $this->page_size;
        $pages->applyLimit($criteria);
        $criteria->order = "pay_date ASC";
        $_dataset = $_model->with('items')->findAll($criteria);

        $this->model['dataset'] = $_dataset;
        $this->model['pages'] = $pages;
        $this->render('detail', $this->model);
    }

    public function actionHead() {
        //$this->checkUserAccess('settings');
        $this->setHeadTitle("Ledger Head");
        $this->setPageTitle("Ledger Heads");
        $this->setCurrentPage(AppUrl::URL_LEDGER_HEAD);

        $_model = new LedgerHead();
        $criteria = new CDbCriteria();
        $count = $_model->count($criteria);
        $pages = new CPagination($count);
        $pages->pageSize = $this->page_size;
        $pages->applyLimit($criteria);
        $criteria->order = "name ASC";
        $_dataset = $_model->findAll($criteria);
        //AppHelper::pr($_dataset[0]->sub_head[0]->particulers[0]->descp);

        $this->model['dataset'] = $_dataset;
        $this->model['pages'] = $pages;
        $this->render('head', $this->model);
    }

    public function actionHead_create() {
        //$this->checkUserAccess('settings');
        $this->setHeadTitle("Ledger Head");
        $this->setPageTitle("New Ledger Head");
        $this->setCurrentPage(AppUrl::URL_LEDGER_HEAD);

        $_model = new LedgerHead();

        if (isset($_POST['ajax']) && $_POST['ajax'] === 'frmLedgerHead') {
            echo CActiveForm::validate($_model);
            Yii::app()->end();
        }

        if (isset($_POST['LedgerHead'])) {
            $_model->attributes = $_POST['LedgerHead'];
            $_model->name = ucfirst($_POST['LedgerHead']['name']);
            //$_model->serial_no = $_POST['LedgerHead']['serial_no'];

            $_transaction = Yii::app()->db->beginTransaction();
            try {
                if (!$_model->validate()) {
                    throw new CException(Yii::t("App", CHtml::errorSummary($_model)));
                }

                if (!$_model->save()) {
                    throw new CException(Yii::t("App", "Error while saving data."));
                }

                $_transaction->commit();
                Yii::app()->user->setFlash("success", "New Record has been saved successfully.");
                $this->redirect(array(AppUrl::URL_LEDGER_HEAD));
            } catch (CException $e) {
                $_transaction->rollback();
                Yii::app()->user->setFlash("danger", $e->getMessage());
            }
        }

        $this->model['model'] = $_model;
        $this->render('head_create', $this->model);
    }

    public function actionHead_edit($id) {
        //$this->checkUserAccess('settings');
        $this->setHeadTitle("Ledger Head");
        $this->setPageTitle("New Ledger Head");
        $this->setCurrentPage(AppUrl::URL_LEDGER_HEAD);

        $_model = new LedgerHead();
        $_data = $_model->findByPk($id);

        if (isset($_POST['LedgerHead'])) {
            $_data->attributes = $_POST['LedgerHead'];
            $_data->name = ucfirst($_POST['LedgerHead']['name']);
            //$_data->serial_no = $_POST['LedgerHead']['serial_no'];

            $_transaction = Yii::app()->db->beginTransaction();
            try {
                if (!$_data->validate()) {
                    throw new CException(Yii::t("App", CHtml::errorSummary($_data)));
                }

                if (!$_data->save()) {
                    throw new CException(Yii::t("App", "Error while saving data."));
                }

                $_transaction->commit();
                Yii::app()->user->setFlash("success", "Record update successfull.");
                $this->redirect(array(AppUrl::URL_LEDGER_HEAD));
            } catch (CException $e) {
                $_transaction->rollback();
                Yii::app()->user->setFlash("danger", $e->getMessage());
            }
        }

        $this->model['model'] = $_data;
        $this->render('head_edit', $this->model);
    }

    public function actionSub_head($id = NULL) {
        //$this->checkUserAccess('settings');
        $this->setHeadTitle("Ledger Sub Head");
        $this->setPageTitle("Sub Heads");
        $this->setCurrentPage(AppUrl::URL_LEDGER_SUB_HEAD);

        $_model = new LedgerSubHead();
        $criteria = new CDbCriteria();
        if ($id !== NULL) {
            $criteria->condition = "ledger_head_id=:ledger_head_id";
            $criteria->params = array(":ledger_head_id" => $id);
        }
        $count = $_model->count($criteria);
        $pages = new CPagination($count);
        $pages->pageSize = $this->page_size;
        $pages->applyLimit($criteria);
        $criteria->order = "id ASC";
        $_dataset = $_model->findAll($criteria);

        $this->model['dataset'] = $_dataset;
        $this->model['pages'] = $pages;
        $this->render('sub_head', $this->model);
    }

    public function actionSub_head_create() {
        //$this->checkUserAccess('settings');
        $this->setHeadTitle("Ledger Sub Head");
        $this->setPageTitle("New Sub Head");
        $this->setCurrentPage(AppUrl::URL_LEDGER_SUB_HEAD);

        $_model = new LedgerSubHead();

        if (isset($_POST['ajax']) && $_POST['ajax'] === 'frmLedgerSubHead') {
            echo CActiveForm::validate($_model);
            Yii::app()->end();
        }

        if (isset($_POST['LedgerSubHead'])) {
            $_model->attributes = $_POST['LedgerSubHead'];
            $_model->ledger_head_id = $_POST['LedgerSubHead']['ledger_head_id'];
            $_model->name = ucfirst($_POST['LedgerSubHead']['name']);
            //$_model->serial_no = $_POST['LedgerSubHead']['serial_no'];

            $_transaction = Yii::app()->db->beginTransaction();
            try {
                if (empty($_POST['LedgerSubHead']['ledger_head_id'])) {
                    throw new CException(Yii::t("App", "You must select a head name."));
                }

                if (!$_model->validate()) {
                    throw new CException(Yii::t("App", CHtml::errorSummary($_model)));
                }

                if (!$_model->save()) {
                    throw new CException(Yii::t("App", "Error while saving data."));
                }

                $_transaction->commit();
                Yii::app()->user->setFlash("success", "New Record has been saved successfully.");
                $this->redirect($this->createUrl(AppUrl::URL_LEDGER_SUB_HEAD));
            } catch (CException $e) {
                $_transaction->rollback();
                Yii::app()->user->setFlash("danger", $e->getMessage());
            }
        }

        $this->model['model'] = $_model;
        $this->render('sub_head_create', $this->model);
    }

    public function actionSub_head_edit($id) {
        //$this->checkUserAccess('settings');
        $this->setHeadTitle("Ledger Sub Head");
        $this->setPageTitle("Edit Sub Head");
        $this->setCurrentPage(AppUrl::URL_LEDGER_SUB_HEAD);

        $_model = new LedgerSubHead();
        $_data = $_model->findByPk($id);

        if (isset($_POST['LedgerSubHead'])) {
            $_data->attributes = $_POST['LedgerSubHead'];
            $_data->ledger_head_id = $_POST['LedgerSubHead']['ledger_head_id'];
            $_data->name = ucfirst($_POST['LedgerSubHead']['name']);
            //$_data->serial_no = $_POST['LedgerSubHead']['serial_no'];

            $_transaction = Yii::app()->db->beginTransaction();
            try {
                if (empty($_POST['LedgerSubHead']['ledger_head_id'])) {
                    throw new CException(Yii::t("App", "You must select a head name."));
                }

                if (!$_data->validate()) {
                    throw new CException(Yii::t("App", CHtml::errorSummary($_data)));
                }

                if (!$_data->save()) {
                    throw new CException(Yii::t("App", "Error while saving data."));
                }

                $_transaction->commit();
                Yii::app()->user->setFlash("success", "New Record has been saved successfully.");
                $this->redirect($this->createUrl(AppUrl::URL_LEDGER_SUB_HEAD));
            } catch (CException $e) {
                $_transaction->rollback();
                Yii::app()->user->setFlash("danger", $e->getMessage());
            }
        }


        $this->model['model'] = $_data;
        $this->render('sub_head_edit', $this->model);
    }

    public function actionHead_particuler($id = NULL) {
        $this->setHeadTitle("Ledger Head Particulers");
        $this->setPageTitle("Ledger Head Particulers");
        $this->setCurrentPage(AppUrl::URL_LEDGER_HEAD_PARTICULER);

        $_model = new LedgerParticuler();
        $criteria = new CDbCriteria();
        if ($id != NULL) {
            $criteria->condition = "sub_head_id=:sub_head_id";
            $criteria->params = array(":sub_head_id" => $id);
        }
        $count = $_model->count($criteria);
        $pages = new CPagination($count);
        $pages->pageSize = $this->page_size;
        $pages->applyLimit($criteria);
        $criteria->order = "id ASC";
        $_dataset = $_model->findAll($criteria);

        $this->model['dataset'] = $_dataset;
        $this->model['pages'] = $pages;
        $this->render('particulers', $this->model);
    }

    public function actionHead_particuler_create() {
        $this->setHeadTitle("Ledger Head Particuler");
        $this->setPageTitle("New Head Particuler");
        $this->setCurrentPage(AppUrl::URL_LEDGER_HEAD_PARTICULER);

        $_model = new LedgerParticuler();

        if (isset($_POST['ajax']) && $_POST['ajax'] === 'frmLedgerHeadParticuler') {
            echo CActiveForm::validate($_model);
            Yii::app()->end();
        }

        if (isset($_POST['LedgerParticuler'])) {
            $_model->attributes = $_POST['LedgerParticuler'];
            $_model->head_id = $_POST['LedgerParticuler']['head_id'];
            $_model->sub_head_id = $_POST['sub_head_id'];
            $_model->particuler = ucfirst($_POST['LedgerParticuler']['particuler']);

            $_transaction = Yii::app()->db->beginTransaction();
            try {
                if (empty($_POST['LedgerParticuler']['head_id'])) {
                    throw new CException(Yii::t("App", "You must select a head name."));
                }

                if (empty($_POST['sub_head_id'])) {
                    throw new CException(Yii::t("App", "You must select a sub head name."));
                }

                if (!$_model->validate()) {
                    throw new CException(Yii::t("App", CHtml::errorSummary($_model)));
                }

                if (!$_model->save()) {
                    throw new CException(Yii::t("App", "Error while saving data."));
                }

                $_transaction->commit();
                Yii::app()->user->setFlash("success", "New Record has been saved successfully.");
                //$this->refresh();
                $this->redirect($this->createUrl(AppUrl::URL_LEDGER_HEAD_PARTICULER));
            } catch (CException $e) {
                $_transaction->rollback();
                Yii::app()->user->setFlash("danger", $e->getMessage());
            }
        }

        $this->model['model'] = $_model;
        $this->render('head_particuler_create', $this->model);
    }

    public function actionHead_particuler_edit($id) {
        $this->setHeadTitle("Ledger Head Particuler");
        $this->setPageTitle("Edit Head Particuler");
        $this->setCurrentPage(AppUrl::URL_LEDGER_HEAD_PARTICULER);

        $_model = new LedgerParticuler();
        $_data = $_model->findByPk($id);

        if (isset($_POST['LedgerParticuler'])) {
            $_data->attributes = $_POST['LedgerParticuler'];
            $_data->head_id = $_POST['LedgerParticuler']['head_id'];
            $_data->sub_head_id = $_POST['LedgerParticuler']['sub_head_id'];
            $_data->particuler = ucfirst($_POST['LedgerParticuler']['particuler']);

            $_transaction = Yii::app()->db->beginTransaction();
            try {
                if (empty($_POST['LedgerParticuler']['head_id'])) {
                    throw new CException(Yii::t("App", "You must select a head name."));
                }

                if (empty($_POST['LedgerParticuler']['sub_head_id'])) {
                    throw new CException(Yii::t("App", "You must select a sub head name."));
                }

                if (!$_data->validate()) {
                    throw new CException(Yii::t("App", CHtml::errorSummary($_data)));
                }

                if (!$_data->save()) {
                    throw new CException(Yii::t("App", "Error while saving data."));
                }

                $_transaction->commit();
                Yii::app()->user->setFlash("success", "Record update successfull.");
                $this->redirect($this->createUrl(AppUrl::URL_LEDGER_HEAD_PARTICULER));
            } catch (CException $e) {
                $_transaction->rollback();
                Yii::app()->user->setFlash("danger", $e->getMessage());
            }
        }

        $this->model['model'] = $_data;
        $this->render('head_particuler_edit', $this->model);
    }

    public function actionHead_particuler_description($id = NULL) {
        $this->setHeadTitle("Ledger Head Particuler Descriptions");
        $this->setPageTitle("Ledger Head Particuler Descriptions");
        $this->setCurrentPage(AppUrl::URL_LEDGER_HEAD_PARTICULER_DESCRIPTION);

        $_model = new LedgerParticulerDescription();
        $criteria = new CDbCriteria();
        if ($id !== NULL) {
            $criteria->condition = "particuler_id=:particuler_id";
            $criteria->params = array(":particuler_id" => $id);
        }
        $count = $_model->count($criteria);
        $pages = new CPagination($count);
        $pages->pageSize = $this->page_size;
        $pages->applyLimit($criteria);
        $criteria->order = "id ASC";
        $_dataset = $_model->findAll($criteria);

        $this->model['dataset'] = $_dataset;
        $this->model['pages'] = $pages;
        $this->render('particuler_description', $this->model);
    }

    public function actionHead_particuler_description_create() {
        $this->setHeadTitle("Ledger Head Particuler Description");
        $this->setPageTitle("New Head Particuler Description");
        $this->setCurrentPage(AppUrl::URL_LEDGER_HEAD_PARTICULER_DESCRIPTION);

        $_model = new LedgerParticulerDescription();

        if (isset($_POST['ajax']) && $_POST['ajax'] === 'frmLedgerHeadParticulerDescription') {
            echo CActiveForm::validate($_model);
            Yii::app()->end();
        }

        if (isset($_POST['LedgerParticulerDescription'])) {
            $_model->attributes = $_POST['LedgerParticulerDescription'];
            $_model->head_id = $_POST['LedgerParticulerDescription']['head_id'];
            $_model->sub_head_id = $_POST['LedgerParticulerDescription']['sub_head_id'];
            $_model->particuler_id = $_POST['LedgerParticulerDescription']['particuler_id'];
            $_model->description = ucfirst($_POST['LedgerParticulerDescription']['description']);

            $_transaction = Yii::app()->db->beginTransaction();
            try {
                if (empty($_POST['LedgerParticulerDescription']['head_id'])) {
                    throw new CException(Yii::t("App", "You must select a head name."));
                }
                if (empty($_POST['LedgerParticulerDescription']['sub_head_id'])) {
                    throw new CException(Yii::t("App", "You must select a sub head name."));
                }
                if (empty($_POST['LedgerParticulerDescription']['particuler_id'])) {
                    throw new CException(Yii::t("App", "You must select a particuler name."));
                }

                if (!$_model->validate()) {
                    throw new CException(Yii::t("App", CHtml::errorSummary($_model)));
                }

                if (!$_model->save()) {
                    throw new CException(Yii::t("App", "Error while saving data."));
                }

                $_transaction->commit();
                Yii::app()->user->setFlash("success", "New Record has been saved successfully.");
                //$this->refresh();
                $this->redirect($this->createUrl(AppUrl::URL_LEDGER_HEAD_PARTICULER_DESCRIPTION));
            } catch (CException $e) {
                $_transaction->rollback();
                Yii::app()->user->setFlash("danger", $e->getMessage());
            }
        }

        $this->model['model'] = $_model;
        $this->render('head_particuler_description_create', $this->model);
    }

    public function actionHead_particuler_description_edit($id) {
        $this->setHeadTitle("Ledger Head Particuler Description");
        $this->setPageTitle("Edit Head Particuler Description");
        $this->setCurrentPage(AppUrl::URL_LEDGER_HEAD_PARTICULER_DESCRIPTION);

        $_model = new LedgerParticulerDescription();
        $_data = $_model->findByPk($id);

        if (isset($_POST['LedgerParticulerDescription'])) {
            $_data->attributes = $_POST['LedgerParticulerDescription'];
            $_data->head_id = $_POST['LedgerParticulerDescription']['head_id'];
            $_data->sub_head_id = $_POST['LedgerParticulerDescription']['sub_head_id'];
            $_data->particuler_id = $_POST['LedgerParticulerDescription']['particuler_id'];
            $_data->description = ucfirst($_POST['LedgerParticulerDescription']['description']);

            $_transaction = Yii::app()->db->beginTransaction();
            try {
                if (empty($_POST['LedgerParticulerDescription']['head_id'])) {
                    throw new CException(Yii::t("App", "You must select a head name."));
                }
                if (empty($_POST['LedgerParticulerDescription']['sub_head_id'])) {
                    throw new CException(Yii::t("App", "You must select a sub head name."));
                }
                if (empty($_POST['LedgerParticulerDescription']['particuler_id'])) {
                    throw new CException(Yii::t("App", "You must select a particuler name."));
                }

                if (!$_data->validate()) {
                    throw new CException(Yii::t("App", CHtml::errorSummary($_data)));
                }

                if (!$_data->save()) {
                    throw new CException(Yii::t("App", "Error while saving data."));
                }

                $_transaction->commit();
                Yii::app()->user->setFlash("success", "Record update successfull.");
                $this->redirect($this->createUrl(AppUrl::URL_LEDGER_HEAD_PARTICULER_DESCRIPTION));
            } catch (CException $e) {
                $_transaction->rollback();
                Yii::app()->user->setFlash("danger", $e->getMessage());
            }
        }

        $this->model['model'] = $_data;
        $this->render('head_particuler_description_edit', $this->model);
    }

    public function actionHead_delete($id) {
        //$this->checkUserAccess('category_delete');
        $_model = new LedgerHead();
        $_data = $_model->findByPk($id);
        //$_data = $_model->find('LOWER(key) = ?', array(strtolower($_key)));

        $_transaction = Yii::app()->db->beginTransaction();
        try {
            if (empty($id)) {
                throw new CException(Yii::t("App", "You are trying to access invalid Url."));
            }

            if (!empty($_data->sub_head)) {
                foreach ($_data->sub_head as $sub_head) {
                    if (!empty($sub_head->particulers)) {
                        foreach ($sub_head->particulers as $particuler) {
                            if (!empty($particuler->descp)) {
                                foreach ($particuler->descp as $descp) {
                                    if (!$descp->delete()) {
                                        throw new CException(Yii::t("App", "Error while deleting sub head particuler description data."));
                                    }
                                }
                            }

                            if (!$particuler->delete()) {
                                throw new CException(Yii::t("App", "Error while deleting sub head particuler data."));
                            }
                        }
                    }

                    if (!$sub_head->delete()) {
                        throw new CException(Yii::t("App", "Error while deleting sub head data."));
                    }
                }
            }

            if (!$_data->delete()) {
                throw new CException(Yii::t("App", "Error while deleting data."));
            }

            $_transaction->commit();
            Yii::app()->user->setFlash("success", 'Record deleted successfully.');
        } catch (CException $e) {
            $_transaction->rollback();
            Yii::app()->user->setFlash("danger", $e->getMessage());
        }
        $this->redirect(Yii::app()->request->urlReferrer);
    }

    public function actionSub_head_delete($id) {
        //$this->checkUserAccess('category_delete');
        $_model = new LedgerSubHead();
        $_data = $_model->findByPk($id);
        //$_data = $_model->find('LOWER(key) = ?', array(strtolower($_key)));

        $_transaction = Yii::app()->db->beginTransaction();
        try {
            if (empty($id)) {
                throw new CException(Yii::t("App", "You are trying to access invalid Url."));
            }

            if (!$_data->delete()) {
                throw new CException(Yii::t("App", "Error while deleting data."));
            }

            $_transaction->commit();
            Yii::app()->user->setFlash("success", 'Record deleted successfully.');
        } catch (CException $e) {
            $_transaction->rollback();
            Yii::app()->user->setFlash("danger", $e->getMessage());
        }
        $this->redirect(Yii::app()->request->urlReferrer);
    }

    public function actionHead_particuler_delete($id) {
        //$this->checkUserAccess('category_delete');
        $_model = new LedgerParticuler();
        $_data = $_model->findByPk($id);
        //$_data = $_model->find('LOWER(key) = ?', array(strtolower($_key)));

        $_transaction = Yii::app()->db->beginTransaction();
        try {
            if (empty($id)) {
                throw new CException(Yii::t("App", "You are trying to access invalid Url."));
            }

            if (!$_data->delete()) {
                throw new CException(Yii::t("App", "Error while deleting data."));
            }

            $_transaction->commit();
            Yii::app()->user->setFlash("success", 'Record deleted successfully.');
        } catch (CException $e) {
            $_transaction->rollback();
            Yii::app()->user->setFlash("danger", $e->getMessage());
        }
        $this->redirect(Yii::app()->request->urlReferrer);
    }

    public function actionHead_particuler_description_delete($id) {
        //$this->checkUserAccess('category_delete');
        $_model = new LedgerParticulerDescription();
        $_data = $_model->findByPk($id);
        //$_data = $_model->find('LOWER(key) = ?', array(strtolower($_key)));

        $_transaction = Yii::app()->db->beginTransaction();
        try {
            if (empty($id)) {
                throw new CException(Yii::t("App", "You are trying to access invalid Url."));
            }

            if (!$_data->delete()) {
                throw new CException(Yii::t("App", "Error while deleting data."));
            }

            $_transaction->commit();
            Yii::app()->user->setFlash("success", 'Record deleted successfully.');
        } catch (CException $e) {
            $_transaction->rollback();
            Yii::app()->user->setFlash("danger", $e->getMessage());
        }
        $this->redirect(Yii::app()->request->urlReferrer);
    }

    public function actionSettings() {
        //$this->checkUserAccess('settings');
        $this->setHeadTitle("Ledger Settings");
        $this->setPageTitle("Ledger Settings");
        $this->setCurrentPage(AppUrl::URL_LEDGER_SETTINGS);

        //$this->model['model'] = $data;
        $this->render('settings', $this->model);
    }

}
