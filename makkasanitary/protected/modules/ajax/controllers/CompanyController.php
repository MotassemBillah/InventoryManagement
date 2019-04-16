<?php

class CompanyController extends AppController {

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
        $this->renderPartial('index', $this->model);
    }

    public function actionView() {
        $_key = Yii::app()->request->getParam('id');
        $model = new Company();
        $_data = $model->find("LOWER(_key)=?", array(strtolower($_key)));

        $this->model['data'] = $_data;
        $this->renderPartial('view', $this->model);
    }

    public function actionPayment() {
        $_limit = Yii::app()->request->getPost('itemCount');
        $companyID = Yii::app()->request->getPost('companyID');
        $_from = Yii::app()->request->getPost('from_date');
        $_to = Yii::app()->request->getPost('to_date');
        $dateForm = date("Y-m-d", strtotime($_from));
        $dateTo = !empty($_to) ? date("Y-m-d", strtotime($_to)) : date("Y-m-d");
        $_invoice = Yii::app()->request->getPost('invoice');

        $_model = new Payment();
        $criteria = new CDbCriteria();
        $criteria->condition = "company_id=:company_id";
        $criteria->params = array(":company_id" => $companyID);
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
        $_model = new Company();
        $response = array();
        $_data = $_POST['data'];

        if (isset($_data)) {
            $_transaction = Yii::app()->db->beginTransaction();
            try {
                for ($i = 0; $i < count($_data); $i++) {
                    $_obj = $_model->findByPk($_data[$i]);

//                    if (!empty($_obj->heads)) {
//                        foreach ($_obj->heads as $heads) {
//                            if (!$heads->delete()) {
//                                throw new CException(Yii::t("App", "Error while deleting company head data."));
//                            }
//                        }
//                    }

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

    public function actionDeletemeta() {
        $_model = new CompanyHead();
        $response = array();
        $meta_id = Yii::app()->request->getPost('meta_id');
        $_obj = $_model->findByPk($meta_id);

        if (!empty($meta_id)) {
            $_transaction = Yii::app()->db->beginTransaction();
            try {
                if (!$_obj->delete()) {
                    throw new CException(Yii::t("App", "Error while deleting data."));
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
        $_model = new Payment();
        $response = array();
        $_data = $_POST['data'];

        if (isset($_data)) {
            $_transaction = Yii::app()->db->beginTransaction();
            try {
                for ($i = 0; $i < count($_data); $i++) {
                    $_obj = $_model->findByPk($_data[$i]);

                    $modelBalanceSheet = new Balancesheet();
                    $balanceSheet = $modelBalanceSheet->find("payment_id=:payment_id", array(":payment_id" => $_obj->id));
                    if (!empty($balanceSheet)) {
                        if (!$balanceSheet->delete()) {
                            throw new CException(Yii::t("App", "Error while saving leger record."));
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

    public function actionFindmeta() {
        $_model = new CompanyHead();
        $resp = array();
        $companyID = Yii::app()->request->getPost('com_id');
        $cmeta = $_model->findAll('company_id=:company_id', array('company_id' => $companyID));

        $html = "<option value=''>Select</option>";

        if (!empty($cmeta) && count($cmeta) > 0) {
            foreach ($cmeta as $meta) {
                $html .= "<option value='{$meta->id}'>{$meta->value}</option>";
            }

            $resp['success'] = true;
            $resp['html'] = $html;
        } else {
            $resp['success'] = false;
            $resp['html'] = "<option value=''>Not Found!</option>";
        }

        echo json_encode($resp);
        return json_encode($resp);
    }

}
