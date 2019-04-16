<?php

class Cash_accountController extends AppController {

    public $layout = 'admin';

    public function beforeAction($action) {
        $this->actionAuthorized();
        return true;
    }

    public function actionIndex() {
        $this->checkUserAccess('cash_account_list');
        $this->setHeadTitle("Accounts");
        $this->setPageTitle("Cash Account");
        $this->setCurrentPage(AppUrl::URL_CASH_ACCOUNT);
        $this->addCss('datepicker.css');
        $this->addJs('datepicker.js');

        $_model = new CashAccount();
        $criteria = new CDbCriteria();
        $criteria->condition = "debit is not null or credit is not null";
        $criteria->order = "pay_date DESC";
        $count = $_model->count($criteria);
        $pages = new CPagination($count);
        $pages->pageSize = $this->page_size;
        $pages->applyLimit($criteria);
        $_dataset = $_model->findAll($criteria);

        $this->model['dataset'] = $_dataset;
        $this->model['pages'] = $pages;
        $this->render('index', $this->model);
    }

    public function actionDeposit() {
        $this->checkUserAccess('cash_deposit');
        $this->setHeadTitle("Account");
        $this->setPageTitle("Cash Deposit");
        $this->setCurrentPage(AppUrl::URL_CASH_ACCOUNT);
        $this->addCss('datepicker.css');
        $this->addJs('datepicker.js');

        $_model = new CashAccount('cdw');
        if (isset($_POST['CashAccount'])) {
            $_date = $_POST['CashAccount']['created'];
            $_model->attributes = $_POST['CashAccount'];
            $_model->ledger_head_id = $_POST['CashAccount']['ledger_head_id'];
            $_model->type = 'D';
            $_model->transaction_type = $_POST['CashAccount']['transaction_type'];
            $_model->pay_date = !empty($_date) ? date("Y-m-d", strtotime($_date)) : AppHelper::getDbDate();
            $_model->purpose = $_POST['CashAccount']['purpose'];
            $_model->by_whom = $_POST['CashAccount']['by_whom'];
            $_model->debit = $_POST['CashAccount']['debit'];
            $_model->balance = $_model->debit;
            $_model->note = $_model->purpose;
            $_model->is_editable = 1;
            $_model->created = AppHelper::getDbTimestamp();
            $_model->created_by = Yii::app()->user->id;
            $_model->_key = AppHelper::getUnqiueKey();

            $_transaction = Yii::app()->db->beginTransaction();
            try {
                if (!$_model->validate()) {
                    throw new CException(Yii::t("App", CHtml::errorSummary($_model)));
                }

                if ($_model->transaction_type == "Bank") {
                    if (empty($_POST['CashAccount']['account_id'])) {
                        throw new CException(Yii::t("App", "You must select a account."));
                    }
                    if (empty($_POST['CashAccount']['check_no'])) {
                        throw new CException(Yii::t("App", "You must enter a check number."));
                    }

                    $_model->bank_id = $_POST['CashAccount']['bank_id'];
                    $_model->account_id = $_POST['CashAccount']['account_id'];
                    $_model->check_no = $_POST['CashAccount']['check_no'];
                }

                if (!$_model->save()) {
                    throw new CException(Yii::t("App", "Error while saving data."));
                }

                $_lastID = Yii::app()->db->getLastInsertId();
                if ($_model->transaction_type == "Bank") {
                    $_bankAccountBalance = new AccountBalance();
                    $_bankAccountBalance->account_id = $_model->account_id;
                    $_bankAccountBalance->cash_id = $_lastID;
                    $_bankAccountBalance->category = AppConstant::CASH_IN;
                    $_bankAccountBalance->purpose = $_model->purpose;
                    $_bankAccountBalance->by_whom = $_model->by_whom;
                    $_bankAccountBalance->amount = $_model->debit;
                    $_bankAccountBalance->credit = $_bankAccountBalance->amount;
                    $_bankAccountBalance->created = $_model->created;
                    $_bankAccountBalance->created_by = $_model->created_by;
                    if (!$_bankAccountBalance->save()) {
                        throw new CException(Yii::t("App", "Error while saving data."));
                    }
                }

                $_transaction->commit();
                Yii ::app()->user->setFlash("success", "Record saved successfully.");
                $this->redirect($this->createUrl(AppUrl::URL_CASH_ACCOUNT_VOUCHER, ['id' => $_model->_key]));
            } catch (CException $e) {
                $_transaction->rollback();
                Yii ::app()->user->setFlash("danger", $e->getMessage());
            }
        }

        $this->model['model'] = $_model;
        $this->render('form_add', $this->model);
    }

    public function actionDeposit_edit($id) {
        $this->checkUserAccess('cash_deposit_edit');
        $this->setHeadTitle("Account");
        $this->setPageTitle("Cash Deposit");
        $this->setCurrentPage(AppUrl::URL_CASH_ACCOUNT);
        $this->addCss('datepicker.css');
        $this->addJs('datepicker.js');

        $_model = new CashAccount('cdw');
        $_data = $_model->find('LOWER(_key) = ?', array(strtolower($id)));

        if (isset($_POST['CashAccount'])) {
            $_date = $_POST['CashAccount']['created'];
            $_data->attributes = $_POST['CashAccount'];
            $_data->ledger_head_id = $_POST['CashAccount']['ledger_head_id'];
            $_data->transaction_type = $_POST['CashAccount']['transaction_type'];
            $_data->pay_date = !empty($_date) ? date("Y-m-d", strtotime($_date)) : AppHelper::getDbDate();
            $_data->purpose = $_POST['CashAccount']['purpose'];
            $_data->by_whom = $_POST['CashAccount']['by_whom'];
            $_data->debit = $_POST['CashAccount']['debit'];
            $_data->balance = $_data->debit;
            $_data->note = $_data->purpose;
            $_data->created = !empty($_date) ? date('Y-m-d', strtotime($_date)) : AppHelper::getDbDate();
            $_data->modified = AppHelper::getDbTimestamp();
            $_data->modified_by = Yii::app()->user->id;

            $_transaction = Yii::app()->db->beginTransaction();
            try {
                if (!$_data->validate()) {
                    throw new CException(Yii::t("App", CHtml::errorSummary($_data)));
                }

                if ($_data->transaction_type == "Bank") {
                    if (empty($_POST['CashAccount']['account_id'])) {
                        throw new CException(Yii::t("App", "You must select a account."));
                    }
                    if (empty($_POST['CashAccount']['check_no'])) {
                        throw new CException(Yii::t("App", "You must enter a check number."));
                    }

                    $_data->bank_id = $_POST['CashAccount']['bank_id'];
                    $_data->account_id = $_POST['CashAccount']['account_id'];
                    $_data->check_no = $_POST['CashAccount']['check_no'];

                    $_bankAccountBalance = AccountBalance::model()->find('cash_id=:cid', [':cid' => $_data->id]);
                    $_bankAccountBalance->account_id = $_data->account_id;
                    $_bankAccountBalance->purpose = $_data->purpose;
                    $_bankAccountBalance->by_whom = $_data->by_whom;
                    $_bankAccountBalance->amount = $_data->debit;
                    $_bankAccountBalance->credit = $_bankAccountBalance->amount;
                    $_bankAccountBalance->last_update = $_data->modified;
                    $_bankAccountBalance->modified_by = $_data->modified_by;
                    if (!$_bankAccountBalance->save()) {
                        throw new CException(Yii::t("App", "Error while saving account balance data."));
                    }
                } else {
                    $_data->bank_id = NULL;
                    $_data->account_id = NULL;
                    $_data->check_no = NULL;
                }

                if (!$_data->save()) {
                    throw new CException(Yii::t("App", "Error while saving data."));
                }

                $_transaction->commit();
                Yii ::app()->user->setFlash("success", "Record saved successfully.");
                $this->redirect($this->createUrl(AppUrl::URL_CASH_ACCOUNT));
            } catch (CException $e) {
                $_transaction->rollback();
                Yii ::app()->user->setFlash("danger", $e->getMessage());
            }
        }

        $this->model['model'] = $_data;
        $this->render('form_edit', $this->model);
    }

    public function actionWithdraw() {
        $this->checkUserAccess('cash_withdraw');
        $this->setHeadTitle("Account");
        $this->setPageTitle("Cash Withdraw");
        $this->setCurrentPage(AppUrl::URL_CASH_ACCOUNT);
        $this->addCss('datepicker.css');
        $this->addJs('datepicker.js');

        $_model = new CashAccount('cdw');
        if (isset($_POST['CashAccount'])) {
            $_date = $_POST['CashAccount']['created'];
            $amount = $_POST['CashAccount']['credit'];
            $_model->attributes = $_POST['CashAccount'];
            $_model->ledger_head_id = $_POST['CashAccount']['ledger_head_id'];
            $_model->type = 'W';
            $_model->transaction_type = $_POST['CashAccount']['transaction_type'];
            $_model->pay_date = !empty($_date) ? date("Y-m-d", strtotime($_date)) : AppHelper::getDbDate();
            $_model->purpose = $_POST['CashAccount']['purpose'];
            $_model->by_whom = $_POST['CashAccount']['by_whom'];
            $_model->credit = $amount;
            $_model->balance = -($_model->credit);
            $_model->note = $_model->purpose;
            $_model->is_editable = 1;
            $_model->created = AppHelper::getDbTimestamp();
            $_model->created_by = Yii::app()->user->id;
            $_model->_key = AppHelper::getUnqiueKey();

            $_transaction = Yii::app()->db->beginTransaction();
            try {
                if (!$_model->validate()) {
                    throw new CException(Yii::t("App", CHtml::errorSummary($_model)));
                }

                if ($_model->transaction_type == "Bank") {
                    if (empty($_POST['CashAccount']['account_id'])) {
                        throw new CException(Yii::t("App", "You must select a account."));
                    }
                    if (empty($_POST['CashAccount']['check_no'])) {
                        throw new CException(Yii::t("App", "You must enter a check number."));
                    }

                    $_model->bank_id = $_POST['CashAccount']['bank_id'];
                    $_model->account_id = $_POST['CashAccount']['account_id'];
                    $_model->check_no = $_POST['CashAccount']['check_no'];

                    $balance = AccountBalance::model()->sumBalance($_model->account_id);
                    if ($amount > $balance) {
                        throw new CException(Yii::t("App", "Not enough balance to withdraw."));
                    }
                }

                if (!$_model->save()) {
                    throw new CException(Yii::t("App", "Error while saving data."));
                }

                $_lastID = Yii::app()->db->getLastInsertId();
                if ($_model->transaction_type == "Bank") {
                    $_bankAccountBalance = new AccountBalance();
                    $_bankAccountBalance->account_id = $_model->account_id;
                    $_bankAccountBalance->cash_id = $_lastID;
                    $_bankAccountBalance->category = AppConstant::CASH_OUT;
                    $_bankAccountBalance->purpose = $_model->purpose;
                    $_bankAccountBalance->by_whom = $_model->by_whom;
                    $_bankAccountBalance->amount = $_model->credit;
                    $_bankAccountBalance->debit = $_bankAccountBalance->amount;
                    $_bankAccountBalance->created = $_model->created;
                    $_bankAccountBalance->created_by = $_model->created_by;
                    if (!$_bankAccountBalance->save()) {
                        throw new CException(Yii::t("App", "Error while saving data."));
                    }
                }

                $_transaction->commit();
                Yii ::app()->user->setFlash("success", "Record saved successfully.");
                $this->redirect($this->createUrl(AppUrl::URL_CASH_ACCOUNT));
            } catch (CException $e) {
                $_transaction->rollback();
                Yii ::app()->user->setFlash("danger", $e->getMessage());
            }
        }

        $this->model['model'] = $_model;
        $this->render('form_withdraw', $this->model);
    }

    public function actionWithdraw_edit($id) {
        $this->checkUserAccess('cash_withdraw_edit');
        $this->setHeadTitle("Account");
        $this->setPageTitle("Cash Withdraw");
        $this->setCurrentPage(AppUrl::URL_CASH_ACCOUNT);
        $this->addCss('datepicker.css');
        $this->addJs('datepicker.js');

        $_model = new CashAccount('cdw');
        $_data = $_model->find('LOWER(_key) = ?', array(strtolower($id)));

        if (isset($_POST['CashAccount'])) {
            $_date = $_POST['CashAccount']['created'];
            $amount = $_POST['CashAccount']['credit'];
            $_data->attributes = $_POST['CashAccount'];
            $_data->ledger_head_id = $_POST['CashAccount']['ledger_head_id'];
            $_data->transaction_type = $_POST['CashAccount']['transaction_type'];
            $_data->pay_date = !empty($_date) ? date("Y-m-d", strtotime($_date)) : AppHelper::getDbDate();
            $_data->purpose = $_POST['CashAccount']['purpose'];
            $_data->by_whom = $_POST['CashAccount']['by_whom'];
            $_data->credit = $amount;
            $_data->balance = -($_data->credit);
            $_data->note = $_data->purpose;
            $_data->modified = AppHelper ::getDbTimestamp();
            $_data->modified_by = Yii::app()->user->id;

            $_transaction = Yii::app()->db->beginTransaction();
            try {

                if (!$_data->validate()) {
                    throw new CException(Yii::t("App", CHtml::errorSummary($_data)));
                }

                if ($_data->transaction_type == "Bank") {
                    if (empty($_POST['CashAccount']['account_id'])) {
                        throw new CException(Yii::t("App", "You must select a account."));
                    }
                    if (empty($_POST['CashAccount']['check_no'])) {
                        throw new CException(Yii::t("App", "You must enter a check number."));
                    }

                    $_data->bank_id = $_POST['CashAccount']['bank_id'];
                    $_data->account_id = $_POST['CashAccount']['account_id'];
                    $_data->check_no = $_POST['CashAccount']['check_no'];

                    $_bankAccountBalance = AccountBalance::model()->find('cash_id=:cid', [':cid' => $_data->id]);
                    $_bankAccountBalance->account_id = $_data->account_id;
                    $_bankAccountBalance->purpose = $_data->purpose;
                    $_bankAccountBalance->by_whom = $_data->by_whom;
                    $_bankAccountBalance->amount = $_data->credit;
                    $_bankAccountBalance->debit = $_bankAccountBalance->amount;
                    $_bankAccountBalance->last_update = $_data->modified;
                    $_bankAccountBalance->modified_by = $_data->modified_by;
                    if (!$_bankAccountBalance->save()) {
                        throw new CException(Yii::t("App", "Error while saving account balance data."));
                    }
                } else {
                    $_data->bank_id = NULL;
                    $_data->account_id = NULL;
                    $_data->check_no = NULL;
                }

                if (!$_data->save()) {
                    throw new CException(Yii::t("App", "Error while saving data."));
                }

                $_transaction->commit();
                Yii ::app()->user->setFlash("success", "Record update successfull.");
                $this->redirect($this->createUrl(AppUrl::URL_CASH_ACCOUNT));
            } catch (CException $e) {
                $_transaction->rollback();
                Yii ::app()->user->setFlash("danger", $e->getMessage());
            }
        }

        $this->model['model'] = $_data;
        $this->render('form_withdraw', $this->model);
    }

    public function actionView($id) {
        $this->checkUserAccess('cash_voucher');
        $this->setHeadTitle("Account");
        $this->setPageTitle("Voucher");
        $this->setCurrentPage(AppUrl::URL_CASH_ACCOUNT);

        $_model = new CashAccount();
        $_data = $_model->find('LOWER(_key) = ?', array(strtolower($id)));

        $this->model['model'] = $_data;
        $this->render('voucher', $this->model);
    }

    /* Search and ajax calls */

    public function actionSearch() {
        $this->is_ajax_request();
        $_limit = Yii::app()->request->getPost('itemCount');
        $_ledgerHead = Yii::app()->request->getPost('ledger_head');
        $_type = Yii::app()->request->getPost('type');
        $_from = Yii::app()->request->getPost('from_date');
        $_to = Yii::app()->request->getPost('to_date');
        $dateForm = date("Y-m-d", strtotime($_from));
        $dateTo = !empty($_to) ? date("Y-m-d", strtotime($_to)) : date("Y-m-d");
        $_search = Yii::app()->request->getPost('q');
        $_sortBy = Yii::app()->request->getPost('sort_by');
        $_sortType = Yii::app()->request->getPost('sort_type');

        $_model = new CashAccount();
        $criteria = new CDbCriteria();
        if ($_type != "All") {
            $criteria->condition = "type=:type";
            $criteria->params = array(":type" => $_type);
        } else {
            $criteria->addInCondition("type", array('D', 'W'));
        }
        $criteria->addCondition("debit IS NOT NULL OR credit IS NOT NULL");
        if (!empty($_ledgerHead)) {
            $criteria->addCondition("ledger_head_id={$_ledgerHead
                    }");
        }
        if (!empty($_from) || !empty($_to)) {
            $criteria->addBetweenCondition('pay_date', $dateForm, $dateTo);
        }
        if (!empty($_search)) {
            $criteria->addCondition("purpose like '%" . trim($_search) . "%'");
        }
        if (!empty($_sortBy)) {
            $criteria->order = "{$_sortBy} {$_sortType
                    }";
        } else {
            $criteria->order = "pay_date DESC";
        }
        $count = $_model->count($criteria);
        $pages = new CPagination($count);
        $pages->pageSize = !empty($_limit) ? $_limit : $this->page_size;


        $pages->applyLimit($criteria);
        $_dataset = $_model->findAll($criteria);

        $this->model['dataset'] = $_dataset;
        $this->model['pages'] = $pages;
        $this->renderPartial('_list', $this->model);
    }

    public function actionDeleteall() {
        $this->is_ajax_request();
        $response = array();
        $_data = $_POST['data'];
        $_model = new CashAccount();

        if (isset($_data)) {
            $_transaction = Yii::app()->db->beginTransaction();
            try {
                for ($i = 0; $i < count($_data); $i++) {
                    $_obj = $_model->findByPk($_data[$i]);

                    $_accBalance = AccountBalance::model()->find("cash_id=:cid", [":cid" => $_obj->id]);
                    if (!empty($_accBalance)) {
                        if (!$_accBalance->delete()) {
                            throw new CException(Yii::t("App", "Error while deleting account balance data."));
                        }
                    }

                    if (!$_obj->delete()) {
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

    /* Protected function for check balance */

    protected function checkBalance($accountID, $amount) {
        $balance = AppObject::sumCashBalance($accountID);
        if ($amount > $balance) {
            throw new CException(Yii::t("App", "Not enough balance to withdraw."));
        }
    }

    /* Function for update cash account */

    public function actionFind_extra_data() {
        $_loanItems = LoanItem::model()->findAll();
        foreach ($_loanItems as $_loanItem) {
            $_ids[] = $_loanItem->id;
        }
        $_model = new CashAccount();
        $criteria = new CDbCriteria();
        $criteria->condition = "loan_payment_id IS NOT NULL";
        $criteria->addNotInCondition("loan_payment_id", $_ids);
        $_dataset = $_model->findAll($criteria);
        echo count($_dataset);
        AppHelper::pr($_dataset);
        exit;
    }

}
