<?php

class Sales_returnController extends AppController {

    public $layout = 'admin';

    public function beforeAction($action) {
        $this->actionAuthorized();
        return true;
    }

    public function actionIndex() {
        $this->checkUserAccess('sale_return_list');
        $this->setHeadTitle("Sale Return");
        $this->setPageTitle("Sale Return");
        $this->setCurrentPage(AppUrl::URL_SALERETURN);
        $this->addCss('datepicker.css');
        $this->addJs('datepicker.js');
        $this->addJs('views/sale_return.js');

        $_model = new SaleReturn();
        $criteria = new CDbCriteria();

        $count = $_model->count($criteria);
        $pages = new CPagination($count);
        $pages->pageSize = $this->page_size;
        $pages->applyLimit($criteria);
        $criteria->order = "created DESC";
        $_dataset = $_model->findAll($criteria);

        $this->model['dataset'] = $_dataset;
        $this->model['pages'] = $pages;
        $this->render('index', $this->model);
    }

    public function actionCreate() {
        $this->checkUserAccess('sale_return_create');
        $this->setHeadTitle("Sale Return");
        $this->setPageTitle("Sale Return Form");
        $this->setCurrentPage(AppUrl::URL_SALERETURN);
        $this->addJs('views/sale_return.js');
        $this->render('create');
    }

    public function actionEdit() {
        $this->checkUserAccess('sale_return_edit');
        $this->setHeadTitle("Sale Return");
        $this->setPageTitle("Sale Return");
        $this->setCurrentPage(AppUrl::URL_SALERETURN);
        $this->addJs('views/sales.js');

        $_key = Yii::app()->request->getParam('id');
        $_model = new SaleReturn();
        $_data = $_model->find('LOWER(_key) = ?', array(strtolower($_key)));
        $_isPosted = Yii::app()->request->getPost('btnSale');

        if (empty($_data->id)) {
            Yii::app()->user->setFlash("danger", "Trying to access invalid Url!");
            $this->redirect(array(AppUrl::URL_SALERETURN));
        }

        if ($_data->status == AppConstant::ORDER_COMPLETE) {
            Yii::app()->user->setFlash("danger", "Trying to access invalid Url!");
            $this->redirect(array(AppUrl::URL_SALERETURN));
        }

        if (!empty($_isPosted)) {
            $_data->customer_id = $_POST['customer_id'];
            $_data->return_invoice = $_POST['SaleReturn']['return_invoice'];
            $_data->has_transport = isset($_POST['SaleReturn']['has_transport']) ? $_POST['SaleReturn']['has_transport'] : 0;
            $_data->created_by = $_POST['created_by'];
            $_data->modified = AppHelper::getDbTimestamp();
            $_data->modified_by = Yii::app()->user->id;

            $_transaction = Yii::app()->db->beginTransaction();
            try {
                if (!$_data->validate()) {
                    throw new CException(Yii::t("App", CHtml::errorSummary($_data)));
                }

                if (!$_data->save()) {
                    throw new CException(Yii::t("App", "Error while saving data."));
                }

                $arrayProducts = Yii::app()->request->getPost('products');
                $arrayPID = Yii::app()->request->getPost('pid');
                $arrayQuantity = Yii::app()->request->getPost('quantity');
                $arrayPrices = Yii::app()->request->getPost('prices');

                if (empty($arrayProducts)) {
                    throw new CException(Yii::t('App', 'No product is selected!'));
                }

                $_orderItem = new SaleReturnItem();
                $_items = $_orderItem->findAll('sale_return_id=:sale_return_id', array(':sale_return_id' => $_data->id));

                if (!empty($arrayProducts)) {
                    foreach ($_items as $i => $_item) {
                        $productID = $arrayPID[$i];

                        $_item->product_id = $productID;
                        if (!empty($arrayQuantity[$productID])) {
                            $_item->quantity = $arrayQuantity[$productID];
                        }

                        if (!empty($arrayPrices[$productID])) {
                            $_item->price = $arrayPrices[$productID];
                        }

                        $_item->total = ($_item->quantity * $_item->price);

                        if (!$_item->save()) {
                            throw new CException(Yii::t('App', 'Error while saving order Items'));
                        }
                    }
                }

                $_transaction->commit();
                Yii::app()->user->setFlash("success", "Record updated successfully!");
                $this->redirect(array(AppUrl::URL_SALERETURN));
            } catch (CException $e) {
                $_transaction->rollback();
                Yii::app()->user->setFlash("danger", $e->getMessage());
            }
        }

        $this->model['model'] = $_data;
        $this->render('edit', $this->model);
    }

    public function actionView() {
        $this->checkUserAccess('sale_return_view');
        $this->setHeadTitle("Sale Detail");
        $this->setPageTitle("Sale Detail");
        $this->setCurrentPage(AppUrl::URL_SALERETURN);

        $_key = Yii::app()->request->getParam('id');
        $_model = new SaleReturn();
        $_data = $_model->find('LOWER(return_invoice) = ?', array(strtolower($_key)));

        if (empty($_data->sale_id)) {
            Yii::app()->user->setFlash("danger", "Trying to access invalid Url!");
            $this->redirect(Yii::app()->request->urlReferrer);
        }

        $_payModel = new CustomerPayment();
        $_payment = $_payModel->find("sale_id=:sale_id", array(":sale_id" => $_data->sale_id));

        $this->model['model'] = $_data;
        $this->model['payment'] = $_payment;
        $this->render('detail', $this->model);
    }

}
