<?php

class CompanyController extends AppController {

    public $layout = 'admin';

    public function beforeAction($action) {
        $this->actionAuthorized();
        return true;
    }

    public function actionIndex() {
        $this->checkUserAccess('company_list');
        $this->setHeadTitle("Companies");
        $this->setPageTitle("Company List");
        $this->setCurrentPage(AppUrl::URL_COMPANY);
        $this->addJs('views/company.js');

        $_model = new Company();
        $criteria = new CDbCriteria();
        $criteria->condition = "is_deleted = 0";
        $criteria->order = "name ASC";
        $count = $_model->count($criteria);
        $pages = new CPagination($count);
        $pages->pageSize = $this->page_size;
        $pages->applyLimit($criteria);
        $_dataset = $_model->findAll($criteria);

        $this->model['dataset'] = $_dataset;
        $this->model['pages'] = $pages;
        $this->render('index', $this->model);
    }

    public function actionCreate() {
        $this->checkUserAccess('company_create');
        $this->setHeadTitle("Companies");
        $this->setPageTitle("Create Company");
        $this->setCurrentPage(AppUrl::URL_COMPANY);

        $_model = new Company();

        if (isset($_POST['ajax']) && $_POST['ajax'] === 'frmCompany') {
            echo CActiveForm::validate($_model);
            Yii::app()->end();
        }

        if (isset($_POST['Company'])) {
            $_model->attributes = $_POST['Company'];
            $_model->name = AppHelper::capFirstWord($_POST['Company']['name']);
            $_model->email = strtolower($_POST['Company']['email']);
            $_model->phone = $_POST['Company']['phone'];
            $_model->mobile = $_POST['Company']['mobile'];
            $_model->fax = $_POST['Company']['fax'];
            $_model->other_contacts = $_POST['Company']['other_contacts'];
            $_model->address = AppHelper::capFirstWord($_POST['Company']['address']);
            $_model->_key = AppHelper::getUnqiueKey();

            $_transaction = Yii::app()->db->beginTransaction();
            try {
                if (!$_model->validate()) {
                    throw new CException(Yii::t("App", CHtml::errorSummary($_model)));
                }

                if (!$_model->save()) {
                    throw new CException(Yii::t("App", "Error while saving data."));
                }

                $companyID = Yii::app()->db->getLastInsertId();
                $metaOption = Yii::app()->request->getPost('company_meta_option');

                if (!empty($metaOption)) {
                    foreach ($metaOption as $key => $value) {
                        if (!empty($metaOption[$key])) {
                            $companyHead = new CompanyHead();
                            $companyHead->company_id = $companyID;
                            $companyHead->value = $value;
                            if (!$companyHead->save()) {
                                throw new CException(Yii::t("App", "Error while saving metadata."));
                            }
                        }
                    }
                }

                $_transaction->commit();
                Yii::app()->user->setFlash("success", "Record has been saved successfully!");
                $this->redirect(array(AppUrl::URL_COMPANY));
            } catch (CException $e) {
                $_transaction->rollback();
                Yii::app()->user->setFlash("danger", $e->getMessage());
            }
        }

        $this->model['model'] = $_model;
        $this->render('create', $this->model);
    }

    public function actionEdit() {
        $this->checkUserAccess('company_edit');
        $this->setHeadTitle("Companies");
        $this->setPageTitle("Edit Company");
        $this->setCurrentPage(AppUrl::URL_COMPANY);

        $_model = new Company();
        $_key = Yii::app()->request->getParam('id');
        $_data = $_model->find('LOWER(_key) = ?', array(strtolower($_key)));

        if (isset($_POST['Company'])) {
            //$phonePrefixAllow = AppHelper::phonePrefix();
            //$mobilePrefixAllow = AppHelper::mobilePrefix();
            //$phonePrefix = substr($_POST['Company']['phone'], 0, 3);
            //$mobilePrefix = substr($_POST['Company']['mobile'], 0, 3);
            $_data->attributes = $_POST['Company'];
            $_data->name = AppHelper::capFirstWord($_POST['Company']['name']);
            $_data->email = strtolower($_POST['Company']['email']);
            $_data->phone = $_POST['Company']['phone'];
            $_data->mobile = $_POST['Company']['mobile'];
            $_data->fax = $_POST['Company']['fax'];
            $_data->other_contacts = $_POST['Company']['other_contacts'];
            $_data->address = AppHelper::capFirstWord($_POST['Company']['address']);

            $_transaction = Yii::app()->db->beginTransaction();
            try {
                if (!$_data->validate()) {
                    throw new CException(Yii::t("App", CHtml::errorSummary($_data)));
                }

                /* if (!in_array($phonePrefix, $phonePrefixAllow)) {
                  $str = "Invalid phone number format. Start with <b>[";
                  foreach ($phonePrefixAllow as $k => $v) {
                  $str .= $v . ',';
                  }
                  $str .= "]</b>";
                  throw new CException(Yii::t("App", $str));
                  }

                  if (!in_array($mobilePrefix, $mobilePrefixAllow)) {
                  $str = "Invalid mobile number format. Start with <b>[";
                  foreach ($mobilePrefixAllow as $k => $v) {
                  $str .= $v . ',';
                  }
                  $str .= "]</b>";
                  throw new CException(Yii::t("App", $str));
                  }
                 */
                if (!$_data->save()) {
                    throw new CException(Yii::t("App", "Error while saving data."));
                }

                $metaOption = Yii::app()->request->getPost('company_meta_option');
                $acKeys = Yii::app()->request->getPost('acKey');

                if (!empty($metaOption)) {
                    foreach ($metaOption as $key => $value) {
                        if (!empty($metaOption[$key])) {
                            if (!empty($acKeys)) {
                                $companyHead = CompanyHead::model()->findByPk($acKeys[$key]);
                                $companyHead->value = $metaOption[$key];
                                if (!$companyHead->save()) {
                                    throw new CException(Yii::t("App", "Error while saving metadata."));
                                }
                            }
                        }
                    }
                }

                $metaOptionNew = Yii::app()->request->getPost('company_meta_option_new');
                if (!empty($metaOptionNew)) {
                    foreach ($metaOptionNew as $key => $value) {
                        if (!empty($metaOptionNew[$key])) {
                            $companyHeadNew = new CompanyHead();
                            $companyHeadNew->company_id = $_data->id;
                            $companyHeadNew->value = $metaOptionNew[$key];
                            if (!$companyHeadNew->save()) {
                                throw new CException(Yii::t("App", "Error while saving new company head name."));
                            }
                        }
                    }
                }

                $_transaction->commit();
                Yii::app()->user->setFlash("success", "Record updated successfully!");
                $this->redirect(array(AppUrl::URL_COMPANY));
            } catch (CException $e) {
                $_transaction->rollback();
                Yii::app()->user->setFlash("danger", $e->getMessage());
            }
        }

        $this->model['model'] = $_data;
        $this->render('edit', $this->model);
    }

    public function actionPayment() {
        $this->checkUserAccess('company_payment');
        $this->setHeadTitle("Company Payments");
        $this->setCurrentPage(AppUrl::URL_COMPANY);
        $this->addCss('datepicker.css');
        $this->addJs('datepicker.js');
        $this->addJs('views/company/payment.js');

        $_model = new Company();
        $_modelPayment = new Payment();
        $_key = Yii::app()->request->getParam('id');
        $_data = $_model->find("LOWER(_key) = ?", array(strtolower($_key)));

        if (empty($_data->id)) {
            Yii::app()->user->setFlash("warning", "You are trying to access a invalid Url.");
            $this->redirect(array(AppUrl::URL_COMPANY));
        }

        $criteria = new CDbCriteria();
        $criteria->condition = "company_id=:company_id";
        $criteria->params = array(":company_id" => $_data->id);
        $count = $_modelPayment->count($criteria);
        $pages = new CPagination($count);
        $pages->pageSize = $this->page_size;
        $pages->applyLimit($criteria);
        $criteria->order = "pay_date DESC";
        $_dataset = $_modelPayment->findAll($criteria);

        $this->setPageTitle("Payments For - <u>" . $_data->name . "</u>");
        $this->model['model'] = $_data;
        $this->model['dataset'] = $_dataset;
        $this->model['pages'] = $pages;
        $this->render('payment', $this->model);
    }

    public function actionPayment_create() {
        $this->checkUserAccess('company_payment_create');
        $this->setHeadTitle("Customers");
        $this->setCurrentPage(AppUrl::URL_COMPANY);
        $this->addCss('datepicker.css');
        $this->addJs('datepicker.js');

        $_model = new Company();
        $_modelPayment = new Payment();
        $_key = Yii::app()->request->getParam('id');
        $_data = $_model->find('LOWER(_key) = ?', array(strtolower($_key)));

        if (isset($_POST['Payment'])) {
            $payType = isset($_POST['pay_type']) ? $_POST['pay_type'] : "";
            $paymentMode = isset($_POST['Payment']['payment_mode']) ? $_POST['Payment']['payment_mode'] : "";
            $advAmount = $_POST['Payment']['advance_amount'];
            $payDate = !empty($_POST['pay_date']) ? date("Y-m-d", strtotime($_POST['pay_date'])) : date("Y-m-d");

            $_transaction = Yii::app()->db->beginTransaction();
            try {
                $_modelPayment->attributes = $_POST['Payment'];
                $_modelPayment->company_id = $_data->id;

                if (empty($payType)) {
                    throw new CException(Yii::t("App", "You must select a payment type."));
                }

                if (empty($advAmount)) {
                    throw new CException(Yii::t("App", "You must enter amount."));
                }

                if ($payType == AppConstant::TYPE_ADVANCE) {
                    if (empty($paymentMode)) {
                        throw new CException(Yii::t("App", "You must select a payment mode."));
                    }

                    if ($paymentMode == AppConstant::PAYMENT_CHECK) {
                        $accountID = isset($_POST['Payment']['account_id']) ? $_POST['Payment']['account_id'] : "";
                        $check_no = isset($_POST['Payment']['check_no']) ? $_POST['Payment']['check_no'] : "";

                        if (empty($accountID)) {
                            throw new CException(Yii::t("App", "You must select a bank account."));
                        }
                        if (empty($check_no)) {
                            throw new CException(Yii::t("App", "You must provide a cheque number."));
                        }

                        $balanceAmount = AppObject::sumCashBalance($accountID);
                        if ($balanceAmount < $advAmount) {
                            throw new CException(Yii::t("App", "Not enough balance in your account to pay."));
                        }

                        $_modelPayment->bank_name = NULL;
                        $_modelPayment->account_id = $accountID;
                        $_modelPayment->check_no = $check_no;
                    } else {
                        $_modelPayment->account_id = NULL;
                        $_modelPayment->bank_name = NULL;
                        $_modelPayment->check_no = NULL;
                    }

                    $_modelPayment->purchase_id = NULL;
                    $_modelPayment->due_amount = NULL;
                    $_modelPayment->advance_amount = $advAmount;
                    $_modelPayment->balance_amount = $advAmount;
                    $_modelPayment->type = AppConstant::TYPE_ADVANCE;
                    $_modelPayment->payment_mode = $paymentMode;
                } elseif ($payType == AppConstant::TYPE_DUE) {
                    $_modelPayment->purchase_id = NULL;
                    $_modelPayment->due_amount = $advAmount;
                    $_modelPayment->advance_amount = NULL;
                    $_modelPayment->balance_amount = $advAmount;
                    $_modelPayment->type = AppConstant::TYPE_DUE;
                    $_modelPayment->payment_mode = "Due Pay";
                } else {
                    $_modelPayment->purchase_id = NULL;
                    $_modelPayment->due_amount = $advAmount;
                    $_modelPayment->advance_amount = NULL;
                    $_modelPayment->balance_amount = -($advAmount);
                    $_modelPayment->type = AppConstant::TYPE_PREVIOUS_DUE;
                    $_modelPayment->payment_mode = "Previous Due Added";
                }

                $_modelPayment->pay_date = $payDate;
                $_modelPayment->created = AppHelper::getDbTimestamp();
                $_modelPayment->created_by = Yii::app()->user->id;
                $_modelPayment->_key = AppHelper::getUnqiueKey();
                if (!$_modelPayment->save()) {
                    throw new CException(Yii::t("App", "Error while saving data."));
                }

                $paymentID = Yii::app()->db->getLastInsertId();
                $modelBalanceSheet = new Balancesheet();
                $modelBalanceSheet->payment_id = $paymentID;
                $modelBalanceSheet->pay_date = $_modelPayment->pay_date;
                $modelBalanceSheet->credit = AppHelper::getFloat($advAmount);
                $modelBalanceSheet->balance = -($modelBalanceSheet->credit);
                if (!$modelBalanceSheet->save()) {
                    throw new CException(Yii::t("App", "Error while saving leger record."));
                }

                $_transaction->commit();
                Yii::app()->user->setFlash("success", "Record has been saved successfully.");
                $this->redirect($this->createUrl(AppUrl::URL_COMPANY_PAYMENT, array('id' => $_key)));
            } catch (CException $e) {
                $_transaction->rollback();
                Yii::app()->user->setFlash("danger", $e->getMessage());
            }
        }

        $settings = $this->getSettings();
        $payModes = json_decode($settings->payment_modes);

        $this->setPageTitle("New Payment For - <u>" . $_data->name . "</u>");
        $this->model['model'] = $_modelPayment;
        $this->model['company'] = $_data;
        $this->model['payModes'] = $payModes;
        $this->render('create_payment', $this->model);
    }

    public function actionPayment_edit() {
        $this->checkUserAccess('customer_payment_edit');
        $this->setHeadTitle("Company");
        $this->setCurrentPage(AppUrl::URL_COMPANY);
        $this->addCss('datepicker.css');
        $this->addJs('datepicker.js');

        $_model = new Payment();
        $_key = Yii::app()->request->getParam('id');
        $_data = $_model->find('LOWER(_key) = ?', array(strtolower($_key)));

        if (isset($_POST['Payment'])) {
            $payType = isset($_POST['pay_type']) ? $_POST['pay_type'] : "";
            $paymentMode = isset($_POST['Payment']['payment_mode']) ? $_POST['Payment']['payment_mode'] : "";
            $advAmount = $_POST['Payment']['advance_amount'];
            $payDate = !empty($_POST['pay_date']) ? date("Y-m-d", strtotime($_POST['pay_date'])) : date("Y-m-d");

            $_transaction = Yii::app()->db->beginTransaction();
            try {
                $_data->attributes = $_POST['Payment'];

                if (empty($payType)) {
                    throw new CException(Yii::t("App", "You must select a payment type."));
                }

                if (empty($advAmount)) {
                    throw new CException(Yii::t("App", "You must enter advance amount."));
                }

                if ($payType == AppConstant::TYPE_ADVANCE) {
                    if (empty($paymentMode)) {
                        throw new CException(Yii::t("App", "You must select a payment mode."));
                    }

                    if ($paymentMode == AppConstant::PAYMENT_CHECK) {
                        $accountID = isset($_POST['Payment']['account_id']) ? $_POST['Payment']['account_id'] : "";
                        $check_no = isset($_POST['Payment']['check_no']) ? $_POST['Payment']['check_no'] : "";

                        if (empty($accountID)) {
                            throw new CException(Yii::t("App", "You must select a bank account."));
                        }
                        if (empty($check_no)) {
                            throw new CException(Yii::t("App", "You must provide a cheque number."));
                        }

                        $balanceAmount = AppObject::sumCashBalance($accountID);
                        if ($balanceAmount < $advAmount) {
                            throw new CException(Yii::t("App", "Not enough balance in your account to pay."));
                        }

                        $_data->bank_name = NULL;
                        $_data->account_id = $accountID;
                        $_data->check_no = $check_no;
                    } else {
                        $_data->bank_name = NULL;
                        $_data->account_id = NULL;
                        $_data->check_no = NULL;
                    }

                    $_data->purchase_id = NULL;
                    $_data->due_amount = NULL;
                    $_data->advance_amount = $advAmount;
                    $_data->balance_amount = $advAmount;
                    $_data->type = AppConstant::TYPE_ADVANCE;
                    $_data->payment_mode = $paymentMode;
                } elseif ($payType == AppConstant::TYPE_DUE) {
                    $_data->purchase_id = NULL;
                    $_data->due_amount = $advAmount;
                    $_data->advance_amount = NULL;
                    $_data->balance_amount = $advAmount;
                    $_data->type = AppConstant::TYPE_DUE;
                    $_data->payment_mode = "Due Pay";
                } else {
                    $_data->purchase_id = NULL;
                    $_data->due_amount = $advAmount;
                    $_data->advance_amount = NULL;
                    $_data->balance_amount = -($advAmount);
                    $_data->type = AppConstant::TYPE_PREVIOUS_DUE;
                    $_data->payment_mode = "Previous Due Added";
                }

                $_data->pay_date = $payDate;
                $_data->modified = AppHelper::getDbTimestamp();
                $_data->modified_by = Yii::app()->user->id;
                if (!$_data->save()) {
                    throw new CException(Yii::t("App", "Error while saving data."));
                }

                $modelBalanceSheet = new Balancesheet();
                $balanceSheet = $modelBalanceSheet->find("payment_id=:payment_id", array(":payment_id" => $_data->id));
                if (!empty($balanceSheet)) {
                    $balanceSheet->credit = AppHelper::getFloat($advAmount);
                    $balanceSheet->balance = -($balanceSheet->credit);
                    if (!$balanceSheet->save()) {
                        throw new CException(Yii::t("App", "Error while saving leger record."));
                    }
                } else {
                    $modelBalanceSheet->payment_id = $_data->id;
                    $modelBalanceSheet->pay_date = $_data->pay_date;
                    $modelBalanceSheet->credit = AppHelper::getFloat($advAmount);
                    $modelBalanceSheet->balance = -($modelBalanceSheet->credit);
                    if (!$modelBalanceSheet->save()) {
                        throw new CException(Yii::t("App", "Error while saving leger record."));
                    }
                }

                $_transaction->commit();
                Yii::app()->user->setFlash("success", "Record has been saved successfully!");
                $this->redirect($this->createUrl(AppUrl:: URL_COMPANY_PAYMENT, array('id' => $_data->company->_key)));
            } catch (CException $e) {
                $_transaction->rollback();
                Yii::app()->user->setFlash("danger", $e->getMessage());
            }
        }

        $settings = $this->getSettings();
        $payModes = json_decode($settings->payment_modes);

        $this->setPageTitle("Update Payment For - <u>" . $_data->company->name . "</u>");
        $this->model['model'] = $_data;
        $this->model['payModes'] = $payModes;
        $this->render('edit_payment', $this->model);
    }

    /* Deleted List */

    public function actionDeleted_list() {
        $this->checkUserAccess('company_list');
        $this->setHeadTitle("Companies");
        $this->setPageTitle("Deleted Company List");
        $this->setCurrentPage(AppUrl::URL_COMPANY);

        $_model = new Company();
        $criteria = new CDbCriteria();
        $criteria->condition = "is_deleted = 1";
        $criteria->order = "name ASC";
        $count = $_model->count($criteria);
        $pages = new CPagination($count);
        $pages->pageSize = $this->page_size;
        $pages->applyLimit($criteria);
        $_dataset = $_model->findAll($criteria);

        $this->model['dataset'] = $_dataset;
        $this->model['pages'] = $pages;
        $this->render('deleted_list', $this->model);
    }

    /* Search and ajax calls */

    public function actionSearch() {
        $this->is_ajax_request();
        $_limit = Yii::app()->request->getPost('itemCount');
        $_sort = Yii::app()->request->getPost('itemSort');
        $_search = Yii::app()->request->getPost('q');

        $_model = new Company();
        $criteria = new CDbCriteria();
        $criteria->condition = "is_deleted = 0";
        if (!empty($_search)) {
            $criteria->addCondition("name LIKE :match OR mobile LIKE :match OR phone LIKE :match");
            $criteria->params = array(':match' => "%$_search%");
        }
        if (!empty($_sort) && $_sort != "ALL") {
            $criteria->addCondition("name LIKE '$_sort%'");
        }
        $count = $_model->count($criteria);
        $pages = new CPagination($count);
        $pages->pageSize = !empty($_limit) ? $_limit : $this->page_size;
        $pages->applyLimit($criteria);
        $criteria->order = "name ASC";
        $_dataset = $_model->findAll($criteria);

        $this->model['dataset'] = $_dataset;
        $this->model['pages'] = $pages;
        $this->renderPartial('_list', $this->model);
    }

    public function actionSearch_deleted_list() {
        $this->is_ajax_request();
        $_limit = Yii::app()->request->getPost('itemCount');
        $_sort = Yii::app()->request->getPost('itemSort');
        $_search = Yii::app()->request->getPost('q');

        $_model = new Company();
        $criteria = new CDbCriteria();
        $criteria->condition = "is_deleted = 1";
        if (!empty($_search)) {
            $criteria->addCondition("name LIKE :match OR mobile LIKE :match OR phone LIKE :match");
            $criteria->params = array(':match' => "%$_search%");
        }
        if (!empty($_sort) && $_sort != "ALL") {
            $criteria->addCondition("name LIKE '$_sort%'");
        }
        $count = $_model->count($criteria);
        $pages = new CPagination($count);
        $pages->pageSize = !empty($_limit) ? $_limit : $this->page_size;
        $pages->applyLimit($criteria);
        $criteria->order = "name ASC";
        $_dataset = $_model->findAll($criteria);

        $this->model['dataset'] = $_dataset;
        $this->model['pages'] = $pages;
        $this->renderPartial('_deleted_list', $this->model);
    }

    public function actionDeleteall() {
        $this->is_ajax_request();
        $response = array();
        $_data = $_POST['data'];
        $_model = new Company();

        if (isset($_data)) {
            $_transaction = Yii::app()->db->beginTransaction();
            try {
                for ($i = 0; $i < count($_data); $i++) {
                    $_obj = $_model->findByPk($_data[$i]);

                    if (!empty($_obj->products)) {
                        foreach ($_obj->products as $products) {
                            $products->is_deleted = 1;
                            if (!$products->save()) {
                                throw new CException(Yii::t("App", "Error while deleting product data."));
                            }
                        }
                    }

                    $_obj->is_deleted = 1;
                    if (!$_obj->save()) {
                        throw new CException(Yii::t("App", "Error while deleting data."));
                    }
                }

                $_transaction->commit();
                $response['success'] = true;
                $response['message'] = "Records deleted successfully!";
            } catch (CException $e) {
                $_transaction->rollback();
                $response['success'] = false;
                $response['message'] = $e->getMessage();
            }
        } else {
            $response['success'] = false;
            $response['message'] = "No record found to delete!";
        }

        echo json_encode($response);
        return json_encode($response);
    }

    public function actionRestore() {
        $this->is_ajax_request();
        $response = array();
        $_data = $_POST['data'];
        $_model = new Company();

        if (isset($_data)) {
            $_transaction = Yii::app()->db->beginTransaction();
            try {
                for ($i = 0; $i < count($_data); $i++) {
                    $_obj = $_model->findByPk($_data[$i]);

                    if (!empty($_obj->products)) {
                        foreach ($_obj->products as $products) {
                            $products->is_deleted = 0;
                            if (!$products->save()) {
                                throw new CException(Yii::t("App", "Error while deleting product data."));
                            }
                        }
                    }

                    $_obj->is_deleted = 0;
                    if (!$_obj->save()) {
                        throw new CException(Yii::t("App", "Error while restoring data."));
                    }
                }

                $_transaction->commit();
                $response['success'] = true;
                $response['message'] = "Records restored successfully.";
            } catch (CException $e) {
                $_transaction->rollback();
                $response['success'] = false;
                $response['message'] = $e->getMessage();
            }
        } else {
            $response['success'] = false;
            $response['message'] = "No record found to delete!";
        }

        echo json_encode($response);
        return json_encode($response);
    }

}
