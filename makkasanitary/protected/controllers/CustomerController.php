<?php

class CustomerController extends AppController {

    public $layout = 'admin';

    public function beforeAction($action) {
        $this->actionAuthorized();
        return true;
    }

    public function actionIndex() {
        $this->checkUserAccess('customer_list');
        $this->setHeadTitle("Customers");
        $this->setPageTitle("Customer List");
        $this->setCurrentPage(AppUrl::URL_CUSTOMER);
        $this->addJs('views/customer/list.js');

        $_model = new Customer();
        $criteria = new CDbCriteria();
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
        $this->checkUserAccess('customer_create');
        $this->setHeadTitle("Customers");
        $this->setPageTitle("Create New Customers");
        $this->setCurrentPage(AppUrl::URL_CUSTOMER);

        $_model = new Customer();

        if (isset($_POST['Customer'])) {
            $_model->attributes = $_POST['Customer'];
            $_model->name = AppHelper::capFirstWord($_POST['Customer']['name']);
            $_model->company = AppHelper::capFirstWord($_POST['Customer']['company']);
            $_model->email = strtolower($_POST['Customer']['email']);
            $_model->phone = $_POST['Customer']['phone'];
            $_model->address = AppHelper::capFirstWord($_POST['Customer']['address']);
            $_model->_key = AppHelper::getUnqiueKey();

            $_transaction = Yii::app()->db->beginTransaction();
            try {
                if (!$_model->validate()) {
                    throw new CException(Yii::t("App", CHtml::errorSummary($_model)));
                }
                if (!$_model->save()) {
                    throw new CException(Yii::t("App", "Error while saving data."));
                }

                $_transaction->commit();
                Yii::app()->user->setFlash("success", "New record save successfull.");
                $this->refresh();
            } catch (CException $e) {
                $_transaction->rollback();
                Yii::app()->user->setFlash("danger", $e->getMessage());
            }
        }

        $this->model['model'] = $_model;
        $this->render('create', $this->model);
    }

    public function actionEdit() {
        $this->checkUserAccess('customer_edit');
        $this->setHeadTitle("Customers");
        $this->setPageTitle("Edit Customer");
        $this->setCurrentPage(AppUrl::URL_CUSTOMER);

        $_model = new Customer();
        $_key = Yii::app()->request->getParam('id');
        $_data = $_model->find('LOWER(_key) = ?', array(strtolower($_key)));

        if (isset($_POST['Customer'])) {
            $_data->attributes = $_POST['Customer'];
            $_data->name = AppHelper::capFirstWord($_POST['Customer']['name']);
            $_data->company = AppHelper::capFirstWord($_POST['Customer']['company']);
            $_data->email = strtolower($_POST['Customer']['email']);
            $_data->phone = $_POST['Customer']['phone'];
            $_data->address = AppHelper::capFirstWord($_POST['Customer']['address']);

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
                $this->redirect(array(AppUrl::URL_CUSTOMER));
            } catch (CException $e) {
                $_transaction->rollback();
                Yii::app()->user->setFlash("danger", $e->getMessage());
            }
        }

        $this->model['model'] = $_data;
        $this->render('edit', $this->model);
    }

    public function actionPayment() {
        $this->checkUserAccess('customer_payment');
        $this->setHeadTitle("Customer Payments");
        $this->setCurrentPage(AppUrl::URL_CUSTOMER);
        $this->addCss('datepicker.css');
        $this->addJs('datepicker.js');
        $this->addJs('views/customer/payment.js');

        $_modelCustomer = new Customer();
        $_modelCustomerPayments = new CustomerPayment();
        $_key = Yii::app()->request->getParam('id');
        $_data = $_modelCustomer->find("LOWER(_key) = ?", array(strtolower($_key)));

        if (empty($_data->id)) {
            Yii::app()->user->setFlash("warning", "You are trying to access a invalid Url.");
            $this->redirect(array(AppUrl::URL_CUSTOMER));
        }

        $criteria = new CDbCriteria();
        $criteria->condition = "customer_id=:customer_id";
        $criteria->params = array(":customer_id" => $_data->id);
        //$criteria->addCondition("`total_paid` IS NULL");
        //$criteria->addCondition("`invoice_paid` IS NOT NULL");
        $count = $_modelCustomerPayments->count($criteria);
        $pages = new CPagination($count);
        $pages->pageSize = $this->page_size;
        $pages->applyLimit($criteria);
        $criteria->order = "pay_date DESC";
        $_dataset = $_modelCustomerPayments->findAll($criteria);

        $this->setPageTitle("Payments For - <u>" . $_data->name . "</u>");
        $this->model['customer'] = $_data;
        $this->model['dataset'] = $_dataset;
        $this->model['pages'] = $pages;
        $this->render('payment', $this->model);
    }

    public function actionPayment_create() {
        $this->checkUserAccess('customer_payment_create');
        $this->setHeadTitle("Customers");
        $this->setCurrentPage(AppUrl::URL_CUSTOMER);
        $this->addCss('datepicker.css');
        $this->addJs('datepicker.js');

        $_model = new Customer();
        $_modelCustomerPayment = new CustomerPayment();
        $_key = Yii::app()->request->getParam('id');
        $_data = $_model->find('LOWER(_key) = ?', array(strtolower($_key)));

        if (isset($_POST['CustomerPayment'])) {
            $payType = isset($_POST['pay_type']) ? $_POST['pay_type'] : "";
            $paymentMode = isset($_POST['CustomerPayment']['payment_mode']) ? $_POST['CustomerPayment']['payment_mode'] : "";
            $advAmount = $_POST['CustomerPayment']['advance_amount'];
            $payDate = !empty($_POST['pay_date']) ? date("Y-m-d", strtotime($_POST['pay_date'])) : date("Y-m-d");

            $_transaction = Yii::app()->db->beginTransaction();
            try {
                $_modelCustomerPayment->attributes = $_POST['CustomerPayment'];
                $_modelCustomerPayment->customer_id = $_data->id;

                if (empty($payType)) {
                    throw new CException(Yii::t("App", "You must select a payment type."));
                }

                if (empty($advAmount)) {
                    throw new CException(Yii::t("App", "You must enter amount."));
                }

                if (in_array($payType, [AppConstant::TYPE_DUE_PAID, AppConstant::TYPE_ADVANCE])) {
                    if (empty($paymentMode)) {
                        throw new CException(Yii::t("App", "You must select a payment mode."));
                    }

                    if ($paymentMode == AppConstant::PAYMENT_CHECK) {
                        $bank_name = isset($_POST['CustomerPayment']['bank_name']) ? $_POST['CustomerPayment']['bank_name'] : "";
                        $check_no = isset($_POST['CustomerPayment']['check_no']) ? $_POST['CustomerPayment']['check_no'] : "";

                        if (empty($bank_name)) {
                            throw new CException(Yii::t("App", "You must select a bank."));
                        }
                        if (empty($check_no)) {
                            throw new CException(Yii::t("App", "You must provide a cheque number."));
                        }

                        $_modelCustomerPayment->bank_name = $bank_name;
                        $_modelCustomerPayment->check_no = $check_no;
                    } else {
                        $_modelCustomerPayment->bank_name = NULL;
                        $_modelCustomerPayment->check_no = NULL;
                    }

                    $_modelCustomerPayment->sale_id = NULL;
                    $_modelCustomerPayment->due_amount = NULL;
                    $_modelCustomerPayment->advance_amount = $advAmount;
                    $_modelCustomerPayment->balance_amount = $advAmount;
                    $_modelCustomerPayment->payment_mode = $paymentMode;
                } else {
                    $_modelCustomerPayment->sale_id = NULL;
                    $_modelCustomerPayment->due_amount = $advAmount;
                    $_modelCustomerPayment->advance_amount = NULL;
                    $_modelCustomerPayment->balance_amount = -($advAmount);
                    $_modelCustomerPayment->payment_mode = AppConstant::PAYMENT_NO;
                }

                $_modelCustomerPayment->type = $payType;
                $_modelCustomerPayment->pay_date = $payDate;
                $_modelCustomerPayment->created = AppHelper::getDbTimestamp();
                $_modelCustomerPayment->created_by = Yii::app()->user->id;
                $_modelCustomerPayment->_key = AppHelper::getUnqiueKey();
                if (!$_modelCustomerPayment->save()) {
                    throw new CException(Yii::t("App", "Error while saving data."));
                }

                Customer::model()->updateBalance($_data->id);

                $_transaction->commit();
                Yii::app()->user->setFlash("success", "Record has been saved successfully.");
                $this->redirect($this->createUrl(AppUrl::URL_CUSTOMER_PAYMENT, array('id' => $_key)));
            } catch (CException $e) {
                $_transaction->rollback();
                Yii::app()->user->setFlash("danger", $e->getMessage());
            }
        }

        $settings = $this->getSettings();
        $payModes = json_decode($settings->payment_modes);

        $this->setPageTitle("New Payment For - <u>" . $_data->name . "</u>");
        $this->model['model'] = $_modelCustomerPayment;
        $this->model['customer'] = $_data;
        $this->model['payModes'] = $payModes;
        $this->render('create_payment', $this->model);
    }

    public function actionPayment_edit() {
        $this->checkUserAccess('customer_payment_edit');
        $this->setHeadTitle("Customers");
        $this->setCurrentPage(AppUrl::URL_CUSTOMER);
        $this->addCss('datepicker.css');
        $this->addJs('datepicker.js');

        $_model = new CustomerPayment();
        $_key = Yii::app()->request->getParam('id');
        $_data = $_model->find('LOWER(_key) = ?', array(strtolower($_key)));

        if (isset($_POST['CustomerPayment'])) {
            $payType = isset($_POST['pay_type']) ? $_POST['pay_type'] : "";
            $paymentMode = isset($_POST['CustomerPayment']['payment_mode']) ? $_POST['CustomerPayment']['payment_mode'] : "";
            $advAmount = $_POST['CustomerPayment']['advance_amount'];
            $payDate = !empty($_POST['pay_date']) ? date("Y-m-d", strtotime($_POST['pay_date'])) : date("Y-m-d");

            $_transaction = Yii::app()->db->beginTransaction();
            try {
                $_data->attributes = $_POST['CustomerPayment'];

                if (empty($payType)) {
                    throw new CException(Yii::t("App", "You must select a payment type."));
                }

                if (empty($advAmount)) {
                    throw new CException(Yii::t("App", "You must enter advance amount."));
                }

                if (in_array($payType, [AppConstant::TYPE_DUE_PAID, AppConstant::TYPE_ADVANCE])) {
                    if (empty($paymentMode)) {
                        throw new CException(Yii::t("App", "You must select a payment mode."));
                    }

                    if ($paymentMode == AppConstant::PAYMENT_CHECK) {
                        $bank_name = isset($_POST['CustomerPayment']['bank_name']) ? $_POST['CustomerPayment']['bank_name'] : "";
                        $check_no = isset($_POST['CustomerPayment']['check_no']) ? $_POST['CustomerPayment']['check_no'] : "";

                        if (empty($bank_name)) {
                            throw new CException(Yii::t("App", "You must select a bank."));
                        }
                        if (empty($check_no)) {
                            throw new CException(Yii::t("App", "You must provide a cheque number."));
                        }

                        $_data->bank_name = $bank_name;
                        $_data->check_no = $check_no;
                    } else {
                        $_data->bank_name = NULL;
                        $_data->check_no = NULL;
                    }

                    $_data->sale_id = NULL;
                    $_data->due_amount = NULL;
                    $_data->advance_amount = $advAmount;
                    $_data->balance_amount = $advAmount;
                    $_data->payment_mode = $paymentMode;
                } else {
                    $_data->sale_id = NULL;
                    $_data->due_amount = $advAmount;
                    $_data->advance_amount = NULL;
                    $_data->balance_amount = -($advAmount);
                    $_data->payment_mode = AppConstant::TYPE_PREVIOUS_DUE;
                }

                $_data->type = $payType;
                $_data->pay_date = $payDate;
                $_data->modified = AppHelper::getDbTimestamp();
                $_data->modified_by = Yii::app()->user->id;
                if (!$_data->save()) {
                    throw new CException(Yii::t("App", "Error while saving data."));
                }

                Customer::model()->updateBalance($_data->customer_id);

                $_transaction->commit();
                Yii::app()->user->setFlash("success", "Record has been saved successfully!");
                $this->redirect($this->createUrl(AppUrl:: URL_CUSTOMER_PAYMENT, array('id' => $_data->customer->_key)));
            } catch (CException $e) {
                $_transaction->rollback();
                Yii::app()->user->setFlash("danger", $e->getMessage());
            }
        }

        $settings = $this->getSettings();
        $payModes = json_decode($settings->payment_modes);

        $this->setPageTitle("Update Payment For - <u>" . $_data->customer->name . "</u>");
        $this->model['model'] = $_data;
        $this->model['payModes'] = $payModes;
        $this->render('edit_payment', $this->model);
    }

    public function actionBalance($id) {
        //$this->checkUserAccess('customer_payment');
        $this->setHeadTitle("Customer Balance Sheet");
        $this->setCurrentPage(AppUrl::URL_CUSTOMER);

        $_modelCustomerBalance = new CustomerBalance();
        $criteria = new CDbCriteria();
        $criteria->condition = "customer_id=:customer_id";
        $criteria->params = array(":customer_id" => $id);
        $count = $_modelCustomerBalance->count($criteria);
        $pages = new CPagination($count);
        $pages->pageSize = $this->page_size;
        $pages->applyLimit($criteria);
        $criteria->order = "created DESC";
        $_dataset = $_modelCustomerBalance->findAll($criteria);

        $this->setPageTitle(AppObject::customerName($id) . " - Balance Sheet");
        $this->model['dataset'] = $_dataset;
        $this->model['pages'] = $pages;
        $this->render('balance', $this->model);
    }

    // update functions
    public function actionUpdate_type() {
        $_model = new Customer();
        $_dataset = $_model->findAll();
        echo count($_dataset) . "<br>";

        $_counter = 0;
        foreach ($_dataset as $_data) {
            $_counter++;
            $_balance = AppObject::sumBalanceAmount($_data->id);
            if ($_balance > 0) {
                $_data->type = AppConstant::CTYPE_ADVANCE;
            } elseif ($_balance < 0) {
                $_data->type = AppConstant::CTYPE_DUE;
            } else {
                $_data->type = AppConstant::CTYPE_REGULAR;
            }

            if ($_data->save()) {
                echo "{$_counter} = Saved<br>";
            } else {
                echo "{$_counter} = Failed<br>";
            }
        }
        exit;
    }

}
