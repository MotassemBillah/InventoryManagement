<?php

class StatementController extends AppController {

    public $layout = 'admin';

    public function beforeAction($action) {
        $this->actionAuthorized();
        return true;
    }

    public function actionIndex() {
        $this->setHeadTitle("Financial Statement");
        $this->setPageTitle("Financial Statement");
        $this->setCurrentPage(AppUrl::URL_LEDGER_FINANCE_STATEMENT);
        $this->addCss('datepicker.css');
        $this->addJs('datepicker.js');

        $_model = new LedgerHead();
        $criteria = new CDbCriteria();
        $criteria->order = "code ASC";
        $_debitDataset = $_model->findAll($criteria);

        $criteriac = new CDbCriteria();
        $criteriac->order = "code ASC";
        $_creditDataset = $_model->findAll($criteriac);

        $criteria_cash = new CDbCriteria();
        $criteria_cash->order = "created ASC";
        $_dataset = CashAccount::model()->findAll($criteria_cash);

        $this->model['debitDataset'] = $_debitDataset;
        $this->model['creditDataset'] = $_creditDataset;
        $this->model['dataset'] = $_dataset;
        $this->render('index', $this->model);
    }

    /*
     * Ajax search and other responses
     */

    public function actionSearch() {
        $this->is_ajax_request();
        $_from = Yii::app()->request->getPost('from_date');
        $_to = Yii::app()->request->getPost('to_date');
        $dateForm = date("Y-m-d", strtotime($_from));
        $dateTo = !empty($_to) ? date("Y-m-d", strtotime($_to)) : date("Y-m-d");

        $_model = new CashAccount();
        $criteria = new CDbCriteria();
        $criteria->addcondition("type='D'");
        $criteria->addcondition("debit IS NOT NULL");
        $criteria->addcondition("ledger_head_id IS NOT NULL");
        if (!empty($_from) || !empty($_to)) {
            $criteria->addBetweenCondition('created', $dateForm, $dateTo);
        }
        $criteria->order = "created ASC";
        $criteria->group = "ledger_head_id";
        $_debitDataset = $_model->findAll($criteria);

        $criteriac = new CDbCriteria();
        $criteriac->addcondition("type='W'");
        $criteriac->addcondition("credit IS NOT NULL");
        $criteriac->addcondition("ledger_head_id IS NOT NULL");
        if (!empty($_from) || !empty($_to)) {
            $criteriac->addBetweenCondition('created', $dateForm, $dateTo);
        }
        $criteriac->order = "created ASC";
        $criteriac->group = "ledger_head_id";
        $_creditDataset = $_model->findAll($criteriac);

        $_date1 = '1970-01-01';
        $_date2 = date('Y-m-d', strtotime($_from . ' -1 day'));
        $sumDebit = $_model->sumDebitBetweenDate($_date1, $_date2);
        $sumCredit = $_model->sumCreditBetweenDate($_date1, $_date2);
        $_openingBalance = ($sumDebit - $sumCredit);

        $this->model['openingBalance'] = $_openingBalance;
        $this->model['debitDataset'] = $_debitDataset;
        $this->model['creditDataset'] = $_creditDataset;
        $this->model['dateForm'] = !empty($_from) ? $_from : '';
        $this->model['dateTo'] = !empty($_to) ? $_to : '';
        $this->renderPartial('_search', $this->model);
    }

}
