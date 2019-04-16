<?php

class PaymentsController extends AppController {

    public $layout = 'admin';

    public function beforeAction($action) {
        $this->actionAuthorized();
        Yii::app()->user->setFlash("success", "Please manage your payments with company from here.");
        $this->redirect($this->createUrl(AppUrl::URL_COMPANY));
        return true;
    }

    public function actionIndex() {
        $this->checkUserAccess("payment_list");
        $this->setHeadTitle("Payments");
        $this->setPageTitle("Payments");
        $this->setCurrentPage(AppUrl::URL_PAYMENT);
        $this->addCss('datepicker.css');
        $this->addJs('datepicker.js');
        $this->addJs('views/payment.js');

        $_model = new Payment();
        $criteria = new CDbCriteria();
        $count = $_model->count($criteria);
        $pages = new CPagination($count);
        $pages->pageSize = $this->page_size;
        $pages->applyLimit($criteria);
        $criteria->order = "pay_date DESC";
        $_dataset = $_model->findAll($criteria);

        $this->model['dataset'] = $_dataset;
        $this->model['pages'] = $pages;
        $this->render('index', $this->model);
    }

    public function actionCreate() {
        $this->checkUserAccess("payment_create");
        $this->setHeadTitle("Payments");
        $this->setPageTitle("Create Payment");
        $this->setCurrentPage(AppUrl::URL_PAYMENT);
        $this->addCss('datepicker.css');
        $this->addJs('datepicker.js');
        $this->addJs('views/payment.js');

        $_model = new Payment();

        if (isset($_POST['Payment'])) {
            $paymentMode = isset($_POST['Payment']['payment_mode']) ? $_POST['Payment']['payment_mode'] : "";
            $advAmount = $_POST['Payment']['advance_amount'];
            $payDate = !empty($_POST['pay_date']) ? date("Y-m-d", strtotime($_POST['pay_date'])) : date("Y-m-d");

            $_model->attributes = $_POST['Payment'];
            $_model->purchase_id = NULL;
            $_model->company_id = $_POST['Payment']['company_id'];
            $_model->payment_mode = $paymentMode;
            $_model->type = $_POST['pay_type'];
            $_model->advance_amount = $advAmount;
            $_model->balance_amount = $advAmount;
            $_model->pay_date = $payDate;
            $_model->created = AppHelper::getDbTimestamp();
            $_model->created_by = Yii::app()->user->id;
            $_model->_key = AppHelper::getUnqiueKey();

            $_transaction = Yii::app()->db->beginTransaction();
            try {
                if (empty($_POST['Payment']['company_id'])) {
                    throw new CException(Yii::t("App", "You must select a company."));
                }

                if (empty($paymentMode)) {
                    throw new CException(Yii::t("App", "You must select a payment mode."));
                }

                if ($paymentMode == "Cheque Payment") {
                    //$bank_name = isset($_POST['Payment']['bank_name']) ? $_POST['Payment']['bank_name'] : "";
                    $accountID = isset($_POST['Payment']['account_id']) ? $_POST['Payment']['account_id'] : "";
                    $check_no = isset($_POST['Payment']['check_no']) ? $_POST['Payment']['check_no'] : "";

                    if (empty($accountID)) {
                        throw new CException(Yii::t("App", "You must select an account."));
                    }
                    if (empty($check_no)) {
                        throw new CException(Yii::t("App", "You must provide a cheque number."));
                    }

                    if (empty($advAmount)) {
                        throw new CException(Yii::t("App", "You must enter advance amount."));
                    }

                    $balanceAmount = AppObject::sumCashBalance($accountID);
                    if ($balanceAmount < $advAmount) {
                        throw new CException(Yii::t("App", "Not enough balance in your account to pay."));
                    }

                    //$_model->bank_name = $bank_name;
                    $_model->account_id = $accountID;
                    $_model->check_no = $check_no;

                    $accountBalance = new AccountBalance();
                    $accountBalance->account_id = $accountID;
                    $accountBalance->category = AppConstant::CASH_OUT;
                    $accountBalance->purpose = "Advance Payment";
                    $accountBalance->by_whom = AppObject::displayNameByUser(Yii::app()->user->id);
                    $accountBalance->amount = $advAmount;
                    if (!$accountBalance->save()) {
                        throw new CException(Yii::t("App", "Error while saving account balance data."));
                    }
                } else if ($paymentMode == "Cash Payment") {
                    if (empty($advAmount)) {
                        throw new CException(Yii::t("App", "You must enter advance amount."));
                    }

                    $payment->account_id = NULL;
                    $payment->check_no = NULL;
                } else {
                    $payment->account_id = NULL;
                    $payment->check_no = NULL;
                }

                if (!$_model->validate()) {
                    throw new CException(Yii::t("App", CHtml::errorSummary($_model)));
                }

                if (!$_model->save()) {
                    throw new CException(Yii::t("App", "Error while saving data."));
                }

                $_transaction->commit();
                Yii::app()->user->setFlash("success", "Record has been saved successfully!");
                $this->redirect($this->createUrl(AppUrl::URL_PAYMENT));
            } catch (CException $e) {
                $_transaction->rollback();
                Yii::app()->user->setFlash("danger", $e->getMessage());
            }
        }

        $settings = $this->getSettings();
        $payModes = json_decode($settings->payment_modes);

        $this->model['model'] = $_model;
        $this->model['payModes'] = $payModes;
        $this->render('create', $this->model);
    }

    public function actionEdit($ab) {
        $this->checkUserAccess("payment_edit");
        $this->setHeadTitle("Payments");
        $this->setPageTitle("Edit Payment");
        $this->setCurrentPage(AppUrl::URL_PAYMENT);
        $this->addCss('datepicker.css');
        $this->addJs('datepicker.js');
        $this->addJs('views/payment.js');

        $_model = new Payment();
        $_key = Yii::app()->request->getParam('id');
        $_data = $_model->find('LOWER(_key) = ?', array(strtolower($_key)));

        if (empty($_data->id)) {
            Yii::app()->user->setFlash("danger", "Trying to access invalid Url!");
            $this->redirect($this->createUrl(AppUrl::URL_PAYMENT));
        }

        if (isset($_POST['Payment'])) {
            $paymentMode = isset($_POST['Payment']['payment_mode']) ? $_POST['Payment']['payment_mode'] : "";
            $advAmount = $_POST['Payment']['advance_amount'];
            $payDate = !empty($_POST['pay_date']) ? date("Y-m-d", strtotime($_POST['pay_date'])) : date("Y-m-d");

            $_data->attributes = $_POST['Payment'];
            $_data->purchase_id = NULL;
            $_data->company_id = $_POST['Payment']['company_id'];
            $_data->payment_mode = $paymentMode;
            $_data->type = $_POST['pay_type'];
            $_data->advance_amount = $advAmount;
            $_data->balance_amount = $advAmount;
            $_data->pay_date = $payDate;
            $_data->modified = AppHelper::getDbTimestamp();
            $_data->modified_by = Yii::app()->user->id;

            $_transaction = Yii::app()->db->beginTransaction();
            try {
                if (empty($_POST['Payment']['company_id'])) {
                    throw new CException(Yii::t("App", "You must select a company."));
                }

                if (empty($paymentMode)) {
                    throw new CException(Yii::t("App", "You must select a payment mode."));
                }

                if ($paymentMode == "Cheque Payment") {
                    //$bank_name = isset($_POST['Payment']['bank_name']) ? $_POST['Payment']['bank_name'] : "";
                    $accountID = isset($_POST['Payment']['account_id']) ? $_POST['Payment']['account_id'] : "";
                    $check_no = isset($_POST['Payment']['check_no']) ? $_POST['Payment']['check_no'] : "";

                    if (empty($accountID)) {
                        throw new CException(Yii::t("App", "You must select an account."));
                    }
                    if (empty($check_no)) {
                        throw new CException(Yii::t("App", "You must provide a cheque number."));
                    }
                    if (empty($advAmount)) {
                        throw new CException(Yii::t("App", "You must enter advance amount."));
                    }

                    $balanceAmount = AppObject::sumCashBalance($accountID);
                    if ($balanceAmount < $advAmount) {
                        throw new CException(Yii::t("App", "Not enough balance in your account to pay."));
                    }

                    //$_data->bank_name = $bank_name;
                    $_data->account_id = $accountID;
                    $_data->check_no = $check_no;

                    $_modelAccountBalance = new AccountBalance();
                    $dataBalance = $_modelAccountBalance->findByPk($ab);
                    $dataBalance->account_id = $accountID;
                    $dataBalance->category = AppConstant::CASH_OUT;
                    $dataBalance->purpose = "Advance Payment";
                    $dataBalance->by_whom = AppObject::displayNameByUser(Yii::app()->user->id);
                    $dataBalance->amount = $advAmount;
                    if (!$dataBalance->save()) {
                        throw new CException(Yii::t("App", "Error while saving account balance data."));
                    }
                } else if ($paymentMode == "Cash Payment") {
                    if (empty($advAmount)) {
                        throw new CException(Yii::t("App", "You must enter advance amount."));
                    }

                    $_data->account_id = NULL;
                    $_data->check_no = NULL;
                } else {
                    $_data->account_id = NULL;
                    $_data->check_no = NULL;
                }

                if (!$_data->validate()) {
                    throw new CException(Yii::t("App", CHtml::errorSummary($_data)));
                }

                if (!$_data->save()) {
                    throw new CException(Yii::t("App", "Error while saving data."));
                }

                $_transaction->commit();
                Yii::app()->user->setFlash("success", "Record has been saved successfully!");
                $this->redirect($this->createUrl(AppUrl::URL_PAYMENT));
            } catch (CException $e) {
                $_transaction->rollback();
                Yii::app()->user->setFlash("danger", $e->getMessage());
            }
        }

        $settings = $this->getSettings();
        $payModes = json_decode($settings->payment_modes);

        $this->model['model'] = $_data;
        $this->model['payModes'] = $payModes;
        $this->render('edit', $this->model);
    }

    public function actionDeleteall() {
        $this->checkUserAccess('payment_delete');
        $_model = new Payment();
        $_data = $_POST['data'];

        if (isset($_data)) {
            $_transaction = Yii::app()->db->beginTransaction();
            try {
                for ($i = 0; $i < count($_data); $i++) {
                    $_obj = $_model->findByPk($_data[$i]);

                    if (!$_obj->delete()) {
                        throw new CException(Yii::t('App', "Error while deleting record"));
                    }
                }

                $_transaction->commit();
                Yii::app()->user->setFlash('success', "Records deleted successfully!");
            } catch (CException $e) {
                $_transaction->rollback();
                Yii::app()->user->setFlash('error', $e->getMessage());
            }
        } else {
            Yii::app()->user->setFlash('warning', "No record found to delete!");
        }
        $this->redirect(Yii::app()->request->urlReferrer);
    }

}
