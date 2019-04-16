<?php

class DuesController extends AppController {

    public $layout = 'admin';

    public function beforeAction($action) {
        $this->actionAuthorized();
        return true;
    }

    public function actionIndex() {
        $this->checkUserAccess('dues_list');
        $this->setHeadTitle("Dues");
        $this->setPageTitle("Dues List");
        $this->setCurrentPage(AppUrl::URL_DUES);
        $this->addCss('datepicker.css');
        $this->addJs('datepicker.js');
        $this->addJs('views/dues_list.js');

        $_model = new CustomerPayment();
        $criteria = new CDbCriteria();
        //$criteria->condition = "due_amount IS NOT NULL";
        //$criteria->params = array(":type" => AppConstant::TYPE_DUE);
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

    public function actionTest() {
        $_model = new CustomerPayment();
        $criteria = new CDbCriteria();
        $criteria->condition = "type = 'invoice'";
        $_dataset = $_model->findAll($criteria);
        //AppHelper::pr($_dataset);

        foreach ($_dataset as $_data) {
            $cBalance = new CustomerBalance();
            $cBalance->customer_id = $_data->customer_id;
            $cBalance->payment_id = $_data->id;
            $cBalance->invoice_no = $_data->invoice_no;
            $cBalance->created = date("Y-m-d", strtotime($_data->pay_date));
            $cBalance->amount = $_data->net_amount;
            $cBalance->debit = ($_data->invoice_paid + $_data->advance_amount);
            $cBalance->credit = $_data->due_amount;
            if (!empty($_data->due_amount)) {
                $cBalance->balance = -($_data->due_amount);
            } else if (!empty($_data->advance_amount)) {
                $cBalance->balance = $_data->advance_amount;
            } else {
                $cBalance->balance = NULL;
            }
            //$cBalance->balance = ($_data->invoice_paid + $_data->advance_amount) - $_data->due_amount;
            $cBalance->save();
        }
        exit;
    }

}
