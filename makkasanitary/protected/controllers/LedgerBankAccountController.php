<?php

class LedgerBankAccountController extends AppController {

    public $layout = 'admin';

    public function beforeAction($action) {
        $this->actionAuthorized();
        return true;
    }

    public function actionIndex() {
        //$this->checkUserAccess('account_list');
        $this->setHeadTitle("Accounts");
        $this->setPageTitle("Account");
        $this->setCurrentPage(AppUrl::URL_LEDGER_ACCOUNT);
        $this->addJs('views/ledger/account.js');

        $_model = new LedgerBankAccount();
        $criteria = new CDbCriteria();
        $count = $_model->count($criteria);
        $pages = new CPagination($count);
        $pages->pageSize = $this->page_size;
        $pages->applyLimit($criteria);
        $criteria->order = "account_name ASC";
        $_dataset = $_model->findAll($criteria);

        $this->model['dataset'] = $_dataset;
        $this->model['pages'] = $pages;
        $this->render('index', $this->model);
    }

    public function actionCreate() {
        //$this->checkUserAccess('account_create');
        $this->setHeadTitle("Accounts");
        $this->setPageTitle("Create New Account");
        $this->setCurrentPage(AppUrl::URL_LEDGER_ACCOUNT);

        $_model = new LedgerBankAccount();

        if (isset($_POST['ajax']) && $_POST['ajax'] === 'frmLedgerBankAccount') {
            echo CActiveForm::validate($_model);
            Yii::app()->end();
        }

        if (isset($_POST['LedgerBankAccount'])) {
            $_model->attributes = $_POST['LedgerBankAccount'];
            $_model->bank_name = $_POST['LedgerBankAccount']['bank_name'];
            $_model->account_name = $_POST['LedgerBankAccount']['account_name'];
            $_model->account_number = $_POST['LedgerBankAccount']['account_number'];
            $_model->account_type = $_POST['LedgerBankAccount']['account_type'];
            $_model->address = AppHelper::capFirstWord($_POST['LedgerBankAccount']['address']);
            $_model->created = AppHelper::getDbTimestamp();
            $_model->created_by = Yii::app()->user->id;
            $_model->_key = AppHelper::getUnqiueKey();

            $_transaction = Yii::app()->db->beginTransaction();
            try {
                if (!$_model->validate()) {
                    throw new CException(Yii::t("App", CHtml::errorSummary($_model)));
                }

                if (!$_model->save()) {
                    throw new CException(Yii::t("App", "Error while saving data."));
                }

                //$lastInsertId = Yii::app()->db->getLastInsertID();
                //$accountBalance = new LedgerBankAccountBalance();
                //$accountBalance->ledger_bank_account_id = $lastInsertId;
                //$accountBalance->description = AppConstant::INITIAL_BALANCE;
                //$accountBalance->debit = AppConstant::INITIAL_AMOUNT;
                //$accountBalance->credit = AppConstant::INITIAL_AMOUNT;
                //$accountBalance->balance = AppConstant::INITIAL_AMOUNT;
                //if (!$accountBalance->save()) {
                //throw new CException(Yii::t("App", "Error while saving account balance data."));
                //}

                $_transaction->commit();
                Yii::app()->user->setFlash("success", "New record has been saved successfully!");
                $this->redirect(array(AppUrl::URL_LEDGER_ACCOUNT));
            } catch (CException $e) {
                $_transaction->rollback();
                Yii::app()->user->setFlash("danger", $e->getMessage());
            }
        }

        $this->model['model'] = $_model;
        $this->render('create', $this->model);
    }

    public function actionEdit() {
        //$this->checkUserAccess('account_edit');
        $this->setHeadTitle("Accounts");
        $this->setPageTitle("Edit Account");
        $this->setCurrentPage(AppUrl::URL_ACCOUNT);

        $_model = new LedgerBankAccount();
        $_key = Yii::app()->request->getParam('id');
        $_data = $_model->find('LOWER(_key) = ?', array(strtolower($_key)));

        if (isset($_POST['LedgerBankAccount'])) {
            $_data->attributes = $_POST['LedgerBankAccount'];
            $_data->bank_name = $_POST['LedgerBankAccount']['bank_name'];
            $_data->account_name = $_POST['LedgerBankAccount']['account_name'];
            $_data->account_number = $_POST['LedgerBankAccount']['account_number'];
            $_data->account_type = $_POST['LedgerBankAccount']['account_type'];
            $_data->address = AppHelper::capFirstWord($_POST['LedgerBankAccount']['address']);
            $_data->modified_by = Yii::app()->user->id;

            $_transaction = Yii::app()->db->beginTransaction();
            try {
                if (!$_data->validate()) {
                    throw new CException(Yii::t("App", CHtml::errorSummary($_data)));
                }

                if (!$_data->save()) {
                    throw new CException(Yii::t("App", "Error while saving data."));
                }

                $_transaction->commit();
                Yii::app()->user->setFlash("success", "Record updated successfully!");
                $this->redirect(array(AppUrl::URL_LEDGER_ACCOUNT));
            } catch (CException $e) {
                $_transaction->rollback();
                Yii::app()->user->setFlash("danger", $e->getMessage());
            }
        }

        $this->model['model'] = $_data;
        $this->render('edit', $this->model);
    }

    public function actionBalance($id) {
        //$this->checkUserAccess('account_balance');
        $this->setHeadTitle("Account");
        $this->setPageTitle("Account Balance");
        $this->setCurrentPage(AppUrl::URL_LEDGER_ACCOUNT_BALANCE);
        $this->addCss('datepicker.css');
        $this->addJs('datepicker.js');
        //$this->addJs('views/balance.js');
        $sum = [];

        $_model = new LedgerBankAccountBalance();
        $criteria = new CDbCriteria();
        $count = $_model->count($criteria);
        $pages = new CPagination($count);
        $pages->pageSize = $this->page_size;
        $pages->applyLimit($criteria);
        $criteria->condition = "ledger_bank_account_id=$id";
        $_dataset = $_model->findAll($criteria);

        if (empty($_dataset)) {
            Yii::app()->user->setFlash("danger", "Trying to access invalid Url!");
            $this->redirect(array(AppUrl::URL_LEDGER_ACCOUNT));
        }

        foreach ($_dataset as $_data) {
            $sum[] = $_data->balance;
        }
        $totalBalanceAmount = array_sum($sum);

        $this->model['dataset'] = $_dataset;
        $this->model['pages'] = $pages;
        $this->model['totalBalanceAmount'] = $totalBalanceAmount;
        $this->model['account'] = LedgerBankAccount::model()->findByPk($id);
        $this->render('balance', $this->model);
    }

    public function actionBalance_add($id, $type) {
        //$this->checkUserAccess('account_balance_add');
        $this->setHeadTitle("Account");
        $this->setPageTitle("Account Balance");
        $this->setCurrentPage(AppUrl::URL_LEDGER_ACCOUNT);

        $_model = new LedgerBankAccountBalance();

        if (isset($_POST['ajax']) && $_POST['ajax'] === 'frmLedgerBankAccountBalance') {
            echo CActiveForm::validate($_model);
            Yii::app()->end();
        }

        if (isset($_POST['LedgerBankAccountBalance'])) {
            $_model->attributes = $_POST['LedgerBankAccountBalance'];
            $_model->account_id = $id;
            $_model->description = $_POST['LedgerBankAccountBalance']['description'];
            $_model->debit = $_POST['LedgerBankAccountBalance']['debit'];
            $_model->credit = $_POST['LedgerBankAccountBalance']['credit'];
            $_model->balance = $_POST['LedgerBankAccountBalance']['balance'];

            $_transaction = Yii::app()->db->beginTransaction();
            try {
                if (!$_model->validate()) {
                    throw new CException(Yii::t("App", CHtml::errorSummary($_model)));
                }

                if ($_model->description == AppConstant::CASH_OUT) {
                    $this->checkBalance($id, $_model->balance);
                }

                if (!$_model->save()) {
                    throw new CException(Yii::t("App", "Error while saving data."));
                } $_transaction->commit();
                Yii::app()->user->setFlash("success", "Record saved successfully.");
                $this->redirect($this->createUrl(AppUrl::URL_LEDGER_ACCOUNT_BALANCE, array('id' => $id)));
            } catch (CException $e) {
                $_transaction->rollback();
                Yii::app()->user->setFlash("danger", $e->getMessage());
            }
        }

        $this->model['model'] = $_model;
        $this->model['type'] = $type;
        $this->model['account'] = LedgerBankAccount::model()->findByPk($id);
        $this->render('balance_add', $this->model);
    }

    public function actionBalance_edit($id) {
        //$this->checkUserAccess('account_balance_edit');
        $this->setHeadTitle("Account");
        $this->setPageTitle("Account Balance");
        $this->setCurrentPage(AppUrl::URL_ACCOUNT);

        $_model = new AccountBalance();
        $_data = $_model->findByPk($id);

        if (isset($_POST['AccountBalance'])) {
            $_data->attributes = $_POST['AccountBalance'];
            $_data->account_id = $_data->account_id;
            $_data->category = $_POST['AccountBalance']['category'];
            $_data->purpose = $_POST['AccountBalance']['purpose'];
            $_data->by_whom = $_POST['AccountBalance']['by_whom'];
            $_data->amount = $_POST['AccountBalance']['amount'];

            $_transaction = Yii::app()->db->beginTransaction();
            try {
                if (!$_data->validate()) {
                    throw new CException(Yii::t("App", CHtml::errorSummary($_data)));
                }

                if (!$_data->save()) {
                    throw new CException(Yii::t("App", "Error while saving data."));
                } $_transaction->commit();
                Yii::app()->user->setFlash("success", "Record saved successfully.");
                $this->redirect($this->createUrl(AppUrl::URL_ACCOUNT_BALANCE, array('id' => $_data->account_id)));
            } catch (CException $e) {
                $_transaction->rollback();
                Yii::app()->user->setFlash("danger", $e->getMessage());
            }
        }

        $this->model['model'] = $_data;
        $this->model['account'] = Account::model()->findByPk($_data->account_id);
        $this->render('balance_edit', $this->model);
    }

    public function actionDelete() {
        //$this->checkUserAccess('account_delete');
        $_key = Yii::app()->request->getParam('id');

        $_model = new Account();
        $_data = $_model->find('LOWER(_key) = ?', array(strtolower($_key)));

        $_transaction = Yii::app()->db->beginTransaction();
        try {
            if (empty($_key)) {
                throw new CException(Yii::t("App", "You are trying to get invalid Url."));
            }

            if (empty($_data->id)) {
                throw new Exception(Yii::t("App", "No record found to delete!"));
            }

            if (!$_data->delete()) {
                throw new CException(Yii::t("App", "Error while deleting data."));
            }

            $_transaction->commit();
            Yii::app()->user->setFlash("success", 'Record deleted successfully!');
        } catch (CException $e) {

            $_transaction->rollback();
            Yii::app()->user->setFlash("danger", $e->getMessage());
        }
        $this->redirect(Yii::app()->request->urlReferrer);
    }

    public function actionDeleteall() {
        //$this->checkUserAccess('account_delete');
        $_model = new Account ( );
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
                Yii::app()->user->setFlash('success', "Record deleted successfully!");
            } catch (CException $e) {
                $_transaction->rollback();
                Yii::app()->user->setFlash('error', $e->getMessage());
            }
        } else {
            Yii::app(
            )->user->setFlash('warning', "No record found to delete!");
        }
        $this->redirect(Yii::app()->request->urlReferrer);
    }

    //Protected function for check balance
    protected function checkBalance($accountID, $amount) {
        $balance = AppObject::sumLedgerCashBalance($accountID);
        if ($amount > $balance) {
            throw new CException(Yii::t("App", "Not enough balance to withdraw."));
        }
    }

}
