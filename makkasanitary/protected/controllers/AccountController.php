<?php

class AccountController extends AppController {

    public $layout = 'admin';

    public function beforeAction($action) {
        $this->actionAuthorized();
        return true;
    }

    public function actionIndex() {
        $this->checkUserAccess('account_list');
        $this->setHeadTitle("Accounts");
        $this->setPageTitle("Account");
        $this->setCurrentPage(AppUrl::URL_ACCOUNT);
        $this->addJs('views/account.js');

        $_model = new Account();
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
        $this->checkUserAccess('account_create');
        $this->setHeadTitle("Accounts");
        $this->setPageTitle("Create New Account");
        $this->setCurrentPage(AppUrl::URL_ACCOUNT);

        $_model = new Account();
        if (isset($_POST['Account'])) {
            $_model->attributes = $_POST['Account'];
            $_model->bank_id = $_POST['Account']['bank_id'];
            $_model->account_name = $_POST['Account']['account_name'];
            $_model->account_number = $_POST['Account']['account_number'];
            $_model->account_type = $_POST['Account']['account_type'];
            $_model->address = AppHelper::capFirstWord($_POST['Account']['address']);
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

                $_transaction->commit();
                Yii::app()->user->setFlash("success", "New record has been saved successfully!");
                $this->redirect(array(AppUrl::URL_ACCOUNT));
            } catch (CException $e) {
                $_transaction->rollback();
                Yii::app()->user->setFlash("danger", $e->getMessage());
            }
        }

        $this->model['model'] = $_model;
        $this->render('create', $this->model);
    }

    public function actionEdit() {
        $this->checkUserAccess('account_edit');
        $this->setHeadTitle("Accounts");
        $this->setPageTitle("Edit Account");
        $this->setCurrentPage(AppUrl::URL_ACCOUNT);

        $_model = new Account();
        $_key = Yii::app()->request->getParam('id');
        $_data = $_model->find('LOWER(_key) = ?', array(strtolower($_key)));

        if (isset($_POST['Account'])) {
            $_data->attributes = $_POST['Account'];
            $_data->bank_id = $_POST['Account']['bank_id'];
            $_data->account_name = $_POST['Account']['account_name'];
            $_data->account_number = $_POST['Account']['account_number'];
            $_data->account_type = $_POST['Account']['account_type'];
            $_data->address = AppHelper::capFirstWord($_POST['Account']['address']);
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
                $this->redirect(array(AppUrl::URL_ACCOUNT));
            } catch (CException $e) {
                $_transaction->rollback();
                Yii::app()->user->setFlash("danger", $e->getMessage());
            }
        }

        $this->model['model'] = $_data;
        $this->render('edit', $this->model);
    }

    public function actionBalance($id) {
        $this->checkUserAccess('account_balance');
        $this->setHeadTitle("Account");
        $this->setPageTitle("Account Balance");
        $this->setCurrentPage(AppUrl::URL_ACCOUNT);
        $this->addCss('datepicker.css');
        $this->addJs('datepicker.js');
        $sum = [];

        if (empty($id)) {
            Yii::app()->user->setFlash("danger", "Trying to access invalid Url!");
            $this->redirect(array(AppUrl::URL_ACCOUNT));
        }

        $_model = new Account();
        $_data = $_model->find("LOWER(_key) = ?", [strtolower($id)]);
        $this->model['account'] = $_data;

        $_accBalance = new AccountBalance();
        $criteria = new CDbCriteria();
        $criteria->condition = "account_id={$_data->id}";
        $count = $_accBalance->count($criteria);
        $pages = new CPagination($count);
        $pages->pageSize = $this->page_size;
        $pages->applyLimit($criteria);
        $_dataset = $_accBalance->findAll($criteria);

        foreach ($_dataset as $_data) {
            $sum[] = $_data->amount;
        }
        $totalBalanceAmount = array_sum($sum);

        $this->model['dataset'] = $_dataset;
        $this->model['pages'] = $pages;
        $this->model['totalBalanceAmount'] = $totalBalanceAmount;
        $this->render('balance', $this->model);
    }

    public function actionBalance_add($id, $type) {
        $this->checkUserAccess('account_balance_add');
        $this->setHeadTitle("Account");
        $this->setPageTitle("Account Balance");
        $this->setCurrentPage(AppUrl::URL_ACCOUNT);

        $_model = new AccountBalance ();
        if (isset($_POST['AccountBalance'])) {
            $_acc = Account::model()->findByPk($id);
            $_model->attributes = $_POST['AccountBalance'];
            $_model->account_id = $id;
            $_model->category = $_POST['AccountBalance']['category'];
            $_model->purpose = $_POST['AccountBalance']['purpose'];
            $_model->by_whom = $_POST['AccountBalance']['by_whom'];
            $_model->amount = $_POST['AccountBalance']['amount'];

            $_transaction = Yii::app()->db->beginTransaction();
            try {
                if (!$_model->validate()) {
                    throw new CException(Yii::t("App", CHtml::errorSummary($_model)));
                }

                if ($_model->category == AppConstant:: CASH_OUT) {
                    $_model->credit = $_model->amount;
                    $this->checkBalance($id, $_model->amount);
                } else {
                    $_model->debit = $_model->amount;
                }

                if (!$_model->save()) {
                    throw new CException(Yii::t("App", "Error while saving data."));
                } $_transaction->commit();
                Yii::app()->user->setFlash("success", "Record saved successfully.");
                $this->redirect($this->createUrl(AppUrl::URL_ACCOUNT_BALANCE, array('id' => $_acc->_key)));
            } catch (CException $e) {
                $_transaction->rollback();
                Yii::app()->user->setFlash("danger", $e->getMessage());
            }
        }

        $this->model['model'] = $_model;
        $this->model['type'] = $type;
        $this->model['account'] = Account::model()->findByPk($id);
        $this->render('balance_add', $this->model);
    }

    public function actionBalance_edit($id) {
        $this->checkUserAccess('account_balance_edit');
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

                if ($_data->category == AppConstant:: CASH_OUT) {
                    $_data->credit = $_data->amount;
                } else {
                    $_data->debit = $_data->amount;
                }

                if (!$_data->save()) {
                    throw new CException(Yii::t("App", "Error while saving data."));
                } $_transaction->commit();
                Yii::app()->user->setFlash("success", "Record saved successfully.");
                $this->redirect($this->createUrl(AppUrl::URL_ACCOUNT_BALANCE, array('id' => $_data->account->_key)));
            } catch (CException $e) {
                $_transaction->rollback();
                Yii::app()->user->setFlash("danger", $e->getMessage());
            }
        }

        $this->model['model'] = $_data;
        $this->model['account'] = Account::model()->findByPk($_data->account_id);
        $this->render('balance_edit', $this->model);
    }

    // ajax functions
    public function actionSave_balance() {
        $this->is_ajax_request();
        $accountID = Yii::app()->request->getPost('accountID');
        $accountBalance = Yii::app()->request->getPost('account_balance');
        $model = new AccountBalance();

        if (!empty($accountBalance)) {
            $_transaction = Yii::app()->db->beginTransaction();
            try {
                $model->account_id = $accountID;
                $model->balance_amount = $accountBalance;
                if (!$model->save()) {
                    throw new CException(Yii::t("App", "Error while saving data."));
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
            $response['message'] = "Balance amount required.";
        }

        echo json_encode($response);
        return json_encode($response);
    }

    public function actionAdd_balance() {
        $this->is_ajax_request();
        $_key = Yii::app()->request->getParam('id');
        $model = new Account();
        $_data = $model->find("LOWER(_key)=?", array(strtolower($_key)));

        $this->model['model'] = $_data;
        $this->renderPartial('_add', $this->model);
    }

    public function actionSearch() {
        $this->is_ajax_request();
        $_limit = Yii::app()->request->getPost('itemCount');
        $_q = Yii::app()->request->getPost('q');

        $_model = new Account();
        $criteria = new CDbCriteria();
        if (!empty($_q)) {
            $criteria->condition = "account_name like '%" . $_q . "%'";
        }
        $criteria->order = "account_name ASC";
        $count = $_model->count($criteria);
        $pages = new CPagination($count);
        $pages->pageSize = !empty($_limit) ? $_limit : $this->page_size;
        $pages->applyLimit($criteria);
        $_dataset = $_model->findAll($criteria);

        $this->model['dataset'] = $_dataset;
        $this->model['pages'] = $pages;
        $this->renderPartial('_list', $this->model);
    }

    public function actionSearch_balance() {
        $this->is_ajax_request();
        $accountID = Yii::app()->request->getPost('accountID');
        $_limit = Yii::app()->request->getPost('itemCount');
        $_category = Yii::app()->request->getPost('type');
        $_from = Yii::app()->request->getPost('from_date');
        $_to = Yii::app()->request->getPost('to_date');
        $dateForm = date("Y-m-d", strtotime($_from));
        $dateTo = !empty($_to) ? date("Y-m-d", strtotime($_to)) : date("Y-m-d");

        $_model = new AccountBalance();
        $criteria = new CDbCriteria();
        $criteria->condition = "account_id={$accountID}";
        if (!empty($_category)) {
            $criteria->addCondition("category='{$_category}'");
        }
        if (!empty($_from) || !empty($_to)) {
            $criteria->addBetweenCondition('last_update', $dateForm, $dateTo);
        }
        $count = $_model->count($criteria);
        $pages = new CPagination($count);
        $pages->pageSize = !empty($_limit) ? $_limit : $this->page_size;
        $pages->applyLimit($criteria);
        $_dataset = $_model->findAll($criteria);

        $accBalance = '';
        if ($_category == AppConstant::CASH_IN) {
            $accBalance = AppObject::sumCashIn($accountID);
        } else if ($_category == AppConstant::CASH_OUT) {
            $accBalance = AppObject::sumCashOut($accountID);
        } else {
            $accBalance = AppObject::sumCashBalance($accountID);
        }

        $this->model['dataset'] = $_dataset;
        $this->model['pages'] = $pages;
        $this->model['account'] = Account::model()->findByPk($accountID);
        $this->model['totalAmount'] = $accBalance;
        $this->renderPartial('_list_balance', $this->model);
    }

    public function actionDeleteall() {
        $this->is_ajax_request();
        $response = array();
        $_data = $_POST['data'];
        $_model = new Account();

        if (isset($_data)) {
            $_transaction = Yii::app()->db->beginTransaction();
            try {
                for ($i = 0; $i < count($_data); $i++) {
                    $_obj = $_model->findByPk($_data[$i]);

                    if (!empty($_obj->balance)) {
                        foreach ($_obj->balance as $balance) {
                            if (!$balance->delete()) {
                                throw new CException(Yii::t("App", "Error while deleting account balance data."));
                            }
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

    public function actionFind_list() {
        $resp = array();
        $_model = new Account();
        $_bid = Yii::app()->request->getPost('bid');

        $criteria = new CDbCriteria();
        $criteria->condition = "bank_id=:bid";
        $criteria->params = [":bid" => $_bid];
        $criteria->order = "account_name ASC";
        $dataset = $_model->findAll($criteria);

        if (!empty($dataset) && count($dataset) > 0) {
            $html = "<option value=''>Select</option>";
            foreach ($dataset as $data) {
                $html .= "<option value='{$data->id}'>{$data->name}</option>";
            }
            $resp['success'] = true;
            $resp['html'] = $html;
        } else {
            $resp['success'] = false;
            $resp['html'] = "<option value=''>No account found.</option>";
        }

        echo json_encode($resp);
        return json_encode($resp);
    }

    //Protected function for check balance
    protected function checkBalance($accountID, $amount) {
        $balance = AccountBalance::model()->sumBalance($accountID);
        if ($amount > $balance) {
            throw new CException(Yii::t("App", "Not enough balance to withdraw."));
        }
    }

}
