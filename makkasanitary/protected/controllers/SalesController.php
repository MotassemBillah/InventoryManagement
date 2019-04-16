<?php

class SalesController extends AppController {

    public $layout = 'admin';

    public function beforeAction($action) {
        $this->actionAuthorized();
        return true;
    }

    public function actionIndex() {
        $this->checkUserAccess('sale_list');
        $this->setHeadTitle("Sales");
        $this->setPageTitle("Sales");
        $this->setCurrentPage(AppUrl::URL_SALE);
        $this->addCss('datepicker.css');
        $this->addJs('datepicker.js');
        $this->addJs('views/sale/list.js');

        $_model = new Sale();
        $criteria = new CDbCriteria();
        if (Yii::app()->user->role != AppConstant::ROLE_SUPERADMIN) {
            $criteria->condition = "created_by = " . Yii::app()->user->id;
        }
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
        $this->checkUserAccess('sale_create');
        $this->setHeadTitle("Sales");
        $this->setPageTitle("Sale Order Form");
        $this->setCurrentPage(AppUrl::URL_SALE);
        $this->addJs('views/sale/create.js');

        $_product = new Product();
        $_order = new Sale();
        $_customer = new Customer();
        $criteria = new CDbCriteria();
        $criteria->condition = "is_deleted=0";
        $count = $_product->count($criteria);
        $pages = new CPagination($count);
        $pages->pageSize = $this->page_size;
        $pages->applyLimit($criteria);
        $dataset = $_product->findAll($criteria);

        $this->model['dataset'] = $dataset;
        $this->model['model'] = $_order;
        $this->model['customer'] = $_customer;
        $this->model['pages'] = $pages;
        $this->render('create', $this->model);
    }

    public function actionEdit() {
        $this->checkUserAccess('sale_edit');
        $this->setHeadTitle("Sales");
        $this->setPageTitle("Sales");
        $this->setCurrentPage(AppUrl::URL_SALE);

        $_key = Yii::app()->request->getParam('id');
        $_model = new Sale();
        $_data = $_model->find('LOWER(_key) = ?', array(strtolower($_key)));
        $_isPosted = Yii::app()->request->getPost('btnSale');

        if (empty($_data->id)) {
            Yii::app()->user->setFlash("danger", "Trying to access invalid Url!");
            $this->redirect(array(AppUrl::URL_SALE));
        }

        if ($_data->status == AppConstant::ORDER_COMPLETE) {
            Yii::app()->user->setFlash("danger", "Trying to access invalid Url!");
            $this->redirect(array(AppUrl::URL_SALE));
        }

        if (!empty($_isPosted)) {
            $_data->invoice_no = $_POST['Sale']['invoice_no'];
            $_data->customer_id = $_POST['customer_id'];
            $_data->has_transport = isset($_POST['Sale']['has_transport']) ? $_POST['Sale']['has_transport'] : 0;
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
                $arrayFrees = Yii::app()->request->getPost('frees');
                $arrayPrices = Yii::app()->request->getPost('prices');

                if (empty($arrayProducts)) {
                    throw new CException(Yii::t('App', 'No product is selected!'));
                }

                $_orderItem = new SaleItem();
                $_items = $_orderItem->findAll('sale_id=:sale_id', array(':sale_id' => $_data->id));

                if (!empty($arrayProducts)) {
                    foreach ($_items as $i => $_item) {
                        $productID = $arrayPID[$i];

                        $_item->product_id = $productID;
                        if (!empty($arrayQuantity[$productID])) {
                            $_item->quantity = $arrayQuantity[$productID];
                        }

                        if (!empty($arrayFrees[$productID])) {
                            $_item->free = $arrayFrees[$productID];
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
                $this->redirect(array(AppUrl::URL_SALE));
            } catch (CException $e) {
                $_transaction->rollback();
                Yii::app()->user->setFlash("danger", $e->getMessage());
            }
        }

        $this->model['model'] = $_data;
        $this->render('edit', $this->model);
    }

    public function actionView() {
        $this->checkUserAccess('sale_view');
        $this->setHeadTitle("Sale Detail");
        $this->setPageTitle("Sale Detail");
        $this->setCurrentPage(AppUrl::URL_SALE);

        $_key = Yii::app()->request->getParam('id');
        $_model = new Sale();
        $_data = $_model->find('LOWER(invoice_no) = ?', array(strtolower($_key)));

        if (empty($_data->id)) {
            Yii::app()->user->setFlash("danger", "Trying to access invalid Url!");
            $this->redirect(Yii::app()->request->urlReferrer);
        }

        $_payModel = new CustomerPayment();
        $_payment = $_payModel->find("sale_id=:sale_id", array(":sale_id" => $_data->id));

        $this->model['model'] = $_data;
        $this->model['payment'] = $_payment;
        $this->render('detail', $this->model);
    }

    public function actionCart() {
        $this->checkUserAccess('view_cart');
        $this->setHeadTitle("Sale Cart");
        $this->setPageTitle("Sale Cart");
        $this->setCurrentPage(AppUrl::URL_SALE_CART);
        $this->addJs('views/sale/cart.js');

        $sum = array();
        $_customer = new Customer();
        $_model = new Cart();
        $criteria = new CDbCriteria();

        if (Yii::app()->user->role != AppConstant::ROLE_SUPERADMIN) {
            $criteria->condition = "created_by=:created_by";
            $criteria->params = array(":created_by" => Yii::app()->user->id);
        }
        $criteria->order = "id ASC";
        $_dataset = $_model->findAll($criteria);

        if (isset($_POST['create_invoice'])) {
            $customerType = Yii::app()->request->getPost('customer_type');
            $customer = Yii::app()->request->getPost('customer_id');
            $invoiceNo = Yii::app()->request->getPost('invoice_no');

            $_transaction = Yii::app()->db->beginTransaction();
            try {
                if (empty($customerType)) {
                    throw new CException(Yii::t("App", "Customer type required!"));
                }

                if ($customerType == "exist") {
                    if (empty($customer) || !is_numeric($customer)) {
                        throw new CException(Yii::t("App", "Please select a customer."));
                    }
                    $customerID = $customer;
                } else {
                    $_customer->attributes = $_POST['Customer'];
                    $_customer->name = isset($_POST['Customer']['name']) ? ucfirst($_POST['Customer']['name']) : "";
                    $_customer->phone = isset($_POST['Customer']['phone']) ? $_POST['Customer']['phone'] : "";
                    $_customer->address = isset($_POST['Customer']['address']) ? ucfirst($_POST['Customer']['address']) : "";
                    $_customer->_key = AppHelper::getUnqiueKey();

                    if (!$_customer->validate()) {
                        throw new CException(Yii::t("App", CHtml::errorSummary($_customer)));
                    }

                    if (!$_customer->save()) {
                        throw new CException(Yii::t("App", "Error while saving customer data."));
                    }
                    $customerID = Yii::app()->db->getLastInsertId();
                }

                $_order = new Sale();
                $_order->customer_id = $customerID;
                $_order->invoice_no = !empty($invoiceNo) ? $invoiceNo : date("dHis");
                $_order->has_transport = isset($_POST['transport']) ? $_POST['transport'] : 0;
                $_order->status = AppConstant::ORDER_PENDING;
                $_order->created = AppHelper::getDbTimestamp();
                $_order->created_by = Yii::app()->user->id;
                $_order->on_time = date('Y-m-d H:i:s');
                $_order->_key = AppHelper::getUnqiueKey();

                if (!$_order->validate()) {
                    throw new CException(Yii::t("App", CHtml::errorSummary($_order)));
                }

                if (!$_order->save()) {
                    throw new CException(Yii::t("App", "Error while saving data."));
                }

                $saleID = Yii::app()->db->getLastInsertID();
                $arrayProducts = Yii::app()->request->getPost('products');
                $arrayQuantity = Yii::app()->request->getPost('quantity');
                $arrayPrices = Yii::app()->request->getPost('prices');

                if (empty($arrayProducts)) {
                    throw new CException(Yii::t("App", "Please select at least 1 product."));
                }

                foreach ($arrayProducts as $key => $val) {
                    $productID = $val;
                    $productName = AppObject::productName($productID);

                    if (!empty($arrayProducts[$key])) {
                        $_orderItem = new SaleItem();
                        $_orderItem->sale_id = $saleID;
                        $_orderItem->product_id = $productID;
                        $_orderItem->quantity = $arrayQuantity[$productID];
                        $_orderItem->price = $arrayPrices[$productID];
                        $_orderItem->total = ($_orderItem->quantity * $_orderItem->price);

                        if (!$_orderItem->save()) {
                            throw new CException(Yii::t('App', 'Error while saving order items.'));
                        }
                    }
                    $sum['s'][] = ($_orderItem->quantity * $_orderItem->price);
                    $productPrice = AppObject::purchasePrice($productID);
                    $sum['p'][] = ($_orderItem->quantity * $productPrice);
                }

                $totalSumSale = array_sum($sum['s']);
                $modelPayment = new CustomerPayment();
                $modelPayment->customer_id = $customerID;
                $modelPayment->sale_id = $saleID;
                $modelPayment->invoice_no = $_order->invoice_no;
                $modelPayment->type = AppConstant::TYPE_INVOICE;
                $modelPayment->pay_date = date("Y-m-d", strtotime($_order->created));
                $modelPayment->payment_mode = AppConstant::PAYMENT_NO;
                $modelPayment->invoice_amount = AppHelper::getFloat($totalSumSale);
                $modelPayment->invoice_total = AppHelper::getFloat($totalSumSale);
                $modelPayment->net_amount = AppHelper::getFloat($totalSumSale);
                $modelPayment->due_amount = AppHelper::getFloat($totalSumSale);
                $modelPayment->balance_amount = -($modelPayment->due_amount);
                $modelPayment->created = AppHelper::getDbTimestamp();
                $modelPayment->created_by = Yii::app()->user->id;
                $modelPayment->_key = AppHelper::getUnqiueKey();
                if (!$modelPayment->save()) {
                    throw new CException(Yii::t("App", "Error while saving payment data."));
                }

                $paymentID = Yii::app()->db->getLastInsertId();
                $modelBalanceSheet = new Balancesheet();
                $modelBalanceSheet->customer_payment_id = $paymentID;
                $modelBalanceSheet->pay_date = $modelPayment->pay_date;
                if (!$modelBalanceSheet->save()) {
                    throw new CException(Yii::t("App", "Error while saving leger record."));
                }

                $totalSumPurchase = array_sum($sum['p']);
                $invoice = new Invoice();
                $invoice->sale_id = $saleID;
                $invoice->invoice_no = $_order->invoice_no;
                $invoice->invoice_date = date("Y-m-d", strtotime($_order->created));
                $invoice->invoice_amount = AppHelper::getFloat($modelPayment->net_amount);
                $invoice->purchase_amount = AppHelper::getFloat($totalSumPurchase);
                $invoice->profit = AppHelper::getFloat($invoice->invoice_amount - $invoice->purchase_amount);
                $invoice->_key = AppHelper::getUnqiueKey();
                if (!$invoice->save()) {
                    throw new CException(Yii::t("App", "Error while saving invoice record."));
                }

                foreach ($_dataset as $data) {
                    $cart = Cart::model()->findByPk($data->id);
                    if (!$cart->delete()) {
                        throw new CException(Yii::t("App", "Error while clearing cart."));
                    }
                }

                Customer::model()->updateBalance($customerID);

                $_transaction->commit();
                Yii::app()->user->setFlash("success", "New invoice create successfull.");
                $this->redirect(array(AppUrl::URL_SALE));
            } catch (CException $e) {
                $_transaction->rollback();
                Yii::app()->user->setFlash("danger", $e->getMessage());
            }
        }

        $this->model['dataset'] = $_dataset;
        $this->model['customer'] = $_customer;
        $this->render('cart', $this->model);
    }

    public function actionReset($id) {
        $_model = new Sale();
        $_data = $_model->with('stocks')->findByPk($id);

        $_transaction = Yii::app()->db->beginTransaction();
        try {
            foreach ($_data->stocks as $stocks) {
                if (!$stocks->delete()) {
                    throw new CException(Yii::t('App', 'Error while resetting stocks'));
                }
            }

            $_data->status = AppConstant::ORDER_PENDING;
            if (!$_data->save()) {
                throw new CException(Yii::t("App", "Error while resetting data."));
            }

            $_transaction->commit();
            Yii::app()->user->setFlash("success", "Data reset successfull!");
        } catch (CException $e) {
            $_transaction->rollback();
            Yii::app()->user->setFlash("danger", $e->getMessage());
        }

        if (!empty(Yii::app()->request->urlReferrer)) {
            $this->redirect(Yii::app()->request->urlReferrer);
        } else {
            $this->redirect($this->createUrl(AppUrl::URL_SALE));
        }
    }

    public function actionEmpty_cart() {
        $_model = new Cart();
        $_dataset = $_model->findAll();

        $_transaction = Yii::app()->db->beginTransaction();
        try {
            foreach ($_dataset as $data) {
                $cart = Cart::model()->findByPk($data->id);
                if (!$cart->delete()) {
                    throw new CException(Yii::t("App", "Error while clearing cart."));
                }
            }

            $_transaction->commit();
            Yii::app()->user->setFlash("success", "Cart clear successfull!");
        } catch (CException $e) {
            $_transaction->rollback();
            Yii::app()->user->setFlash("danger", $e->getMessage());
        }

        if (!empty(Yii::app()->request->urlReferrer)) {
            $this->redirect(Yii::app()->request->urlReferrer);
        } else {
            $this->redirect($this->createUrl(AppUrl::URL_SALE_CART));
        }
    }

    protected function clear_cart($dataset) {
        foreach ($dataset as $data) {
            $cart = Cart::model()->findByPk($data->id);
            if (!$cart->delete()) {
                throw new CException(Yii::t("App", "Error while clearing cart."));
            }
        }
    }

    // update functions
    public function actionUpdate_company() {
        $_dataset = SaleItem::model()->findAll();
        echo count($_dataset) . "<br>";

        $_counter = 0;
        foreach ($_dataset as $_data) {
            $_counter++;
            $_data->company_id = Product::model()->find('id=:id', [':id' => $_data->product_id])->company_id;
            if ($_data->save()) {
                echo "{$_counter} = Saved<br>";
            } else {
                echo "{$_counter} = Failed<br>";
            }
        }
        exit;
    }

}
