<?php

class CustomerController extends AppController {

    public function beginRequest() {
        if (Yii::app()->request->isAjaxRequest) {
            return true;
        }
        return false;
    }

    public function beforeAction($action) {
        $this->actionAuthorized();
        $this->is_ajax_request();
        return true;
    }

    public function actionIndex() {
        $_limit = Yii::app()->request->getPost('itemCount');
        $_sort = Yii::app()->request->getPost('itemSort');
        $_type = Yii::app()->request->getPost('itemType');
        $_search = Yii::app()->request->getPost('search');
        $_sortBy = Yii::app()->request->getPost('sort_by');
        $_sortType = Yii::app()->request->getPost('sort_type');

        $_model = new Customer();
        $criteria = new CDbCriteria();
        if (!empty($_search)) {
            $criteria->condition = "name LIKE :match OR company LIKE :match OR phone LIKE :match";
            $criteria->params = array(':match' => "%$_search%");
        }
        if (!empty($_sort) && $_sort != "ALL") {
            $criteria->addCondition("name LIKE '$_sort%'");
        }
        if (!empty($_type)) {
            $criteria->addCondition("type='{$_type}'");
        }
        if (!empty($_sortBy)) {
            $criteria->order = "{$_sortBy} {$_sortType}";
        } else {
            $criteria->order = "name ASC";
        }
        $count = $_model->count($criteria);
        $pages = new CPagination($count);
        $pages->pageSize = !empty($_limit) ? $_limit : $this->page_size;
        $pages->applyLimit($criteria);
        $_dataset = $_model->findAll($criteria);

        $this->model['dataset'] = $_dataset;
        $this->model['pages'] = $pages;
        $this->renderPartial('index', $this->model);
    }

    public function actionPayment() {
        $_limit = Yii::app()->request->getPost('itemCount');
        $customerID = Yii::app()->request->getPost('customerID');
        $_from = Yii::app()->request->getPost('from_date');
        $_to = Yii::app()->request->getPost('to_date');
        $dateForm = date("Y-m-d", strtotime($_from));
        $dateTo = !empty($_to) ? date("Y-m-d", strtotime($_to)) : date("Y-m-d");
        $_invoice = Yii::app()->request->getPost('invoice');

        $_model = new CustomerPayment();
        $criteria = new CDbCriteria();
        $criteria->condition = "customer_id=:customer_id";
        $criteria->params = array(":customer_id" => $customerID);
        $criteria->order = "pay_date DESC";
        if (!empty($_from) || !empty($_to)) {
            $criteria->addBetweenCondition('pay_date', $dateForm, $dateTo);
        }
        if (!empty($_invoice)) {
            $criteria->addCondition("invoice_no = " . $_invoice);
        }
        $count = $_model->count($criteria);
        $pages = new CPagination($count);
        $pages->pageSize = !empty($_limit) ? $_limit : $this->page_size;
        $pages->applyLimit($criteria);
        $_dataset = $_model->findAll($criteria);

        $this->model['dataset'] = $_dataset;
        $this->model['pages'] = $pages;
        $this->renderPartial('payment', $this->model);
    }

    public function actionDeleteall() {
        $_model = new Customer();
        $response = array();
        $_data = $_POST['data'];

        if (isset($_data)) {
            $_transaction = Yii::app()->db->beginTransaction();
            try {
                for ($i = 0; $i < count($_data); $i++) {
                    $_obj = $_model->with('sales', 'payments')->findByPk($_data[$i]);

                    if (!empty($_obj->sales)) {
                        foreach ($_obj->sales as $sale) {
                            foreach ($sale->items as $item) {
                                if (!$item->delete()) {
                                    throw new CException(Yii::t('App', "Error while deleting record {saleitems}"));
                                }
                            }

                            if (!empty($sale->stocks)) {
                                foreach ($sale->stocks as $stock) {
                                    if (!$stock->delete()) {
                                        throw new CException(Yii::t('App', "Error while deleting stock record"));
                                    }
                                }
                            }

                            if (!empty($sale->info)) {
                                if (!$sale->info->delete()) {
                                    throw new CException(Yii::t('App', "Error while deleting record {saleinfo}"));
                                }
                            }

                            if (!empty($sale->invoice)) {
                                if (!$sale->invoice->delete()) {
                                    throw new CException(Yii::t('App', "Error while deleting record {saleinvoice}"));
                                }
                            }

                            if (!$sale->delete()) {
                                throw new CException(Yii::t('App', "Error while deleting record {sales}"));
                            }
                        }
                    }

                    if (!empty($_obj->payments)) {
                        foreach ($_obj->payments as $payment) {
                            if (!$payment->delete()) {
                                throw new CException(Yii::t('App', "Error while deleting record {payments}"));
                            }
                        }
                    }

                    if (!$_obj->delete()) {
                        throw new CException(Yii::t('App', "Error while deleting record"));
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

    public function actionDeleteall_payment() {
        $response = array();
        $_data = $_POST['data'];
        $_model = new CustomerPayment();

        if (isset($_data)) {
            $_transaction = Yii::app()->db->beginTransaction();
            try {
                for ($i = 0; $i < count($_data); $i++) {
                    $_obj = $_model->findByPk($_data[$i]);
                    $_customerID = $_obj->customer_id;

                    $modelBalanceSheet = new Balancesheet();
                    $balanceSheet = $modelBalanceSheet->find("customer_payment_id=:customer_payment_id", array(":customer_payment_id" => $_obj->id));
                    if (!empty($balanceSheet)) {
                        if (!$balanceSheet->delete()) {
                            throw new CException(Yii::t("App", "Error while saving leger record."));
                        }
                    }

                    if (!$_obj->delete()) {
                        throw new CException(Yii::t('App', "Error while deleting record"));
                    }
                    Customer::model()->updateBalance($_customerID);
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

    /* Search Customer By Name */

    public function actionSearch() {
        $name = Yii::app()->request->getPost('customer');
        $retVal = array();
        $_model = new Customer();
        $criteria = new CDbCriteria();
        $criteria->select = "id, name";
        $criteria->condition = "name LIKE '%$name%'";
        //$criteria->params = array(":match" => "");
        $_dataset = $_model->findAll($criteria);

        $retVal['html'] = "";

        if (!empty($_dataset)) {
            $retVal['success'] = true;
            foreach ($_dataset as $_data) {
                $retVal['html'] .= "<option value='$_data->id'>$_data->name</option>";
            }
        } else {
            $retVal['success'] = false;
            $retVal['html'] = '';
        }

        echo json_encode($retVal);
        return json_encode($retVal);
    }

}
