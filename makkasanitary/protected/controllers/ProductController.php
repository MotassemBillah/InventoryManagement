<?php

class ProductController extends AppController {

    public $layout = 'admin';

    public function beforeAction($action) {
        $this->actionAuthorized();
        return true;
    }

    public function actionIndex() {
        $this->checkUserAccess('product_stock');
        $this->setHeadTitle("Product Stocks");
        $this->setPageTitle("Product Stocks");
        $this->setCurrentPage(AppUrl::URL_PRODUCT);
        $this->addJs('views/product/stock.js');

        $_model = new Product();
        $criteria = new CDbCriteria();
        $criteria->condition = "is_deleted = 0";
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

    public function actionList() {
        $this->checkUserAccess('product_list');
        $this->setHeadTitle("Product List");
        $this->setPageTitle("Product List");
        $this->setCurrentPage(AppUrl::URL_PRODUCT_LIST);
        $this->addJs('views/product/list.js');

        $_model = new Product();
        $criteria = new CDbCriteria();
        $criteria->condition = "is_deleted = 0";
        $criteria->addCondition("is_damaged = 0");
        $criteria->order = "name ASC";
        $count = $_model->count($criteria);
        $pages = new CPagination($count);
        $pages->pageSize = $this->page_size;
        $pages->applyLimit($criteria);
        $_dataset = $_model->findAll($criteria);

        $this->model['dataset'] = $_dataset;
        $this->model['pages'] = $pages;
        $this->render('list', $this->model);
    }

    public function actionCreate() {
        $this->checkUserAccess('product_create');
        $this->setHeadTitle("Products");
        $this->setPageTitle("Products");
        $this->setCurrentPage(AppUrl::URL_PRODUCT_LIST);
        $this->addJs('views/product/create.js');

        //$sz = Yii::app()->request->getParam('sz');
        $_model = new Product('create');

        if (isset($_POST['ajax']) && $_POST['ajax'] === 'frmProduct') {
            echo CActiveForm::validate($_model);
            Yii::app()->end();
        }

        if (isset($_POST['Product'])) {
            $_model->attributes = $_POST['Product'];
            $_model->company_id = $_POST['Product']['company_id'];
            $_model->category_id = $_POST['Product']['category_id'];
            $_model->name = AppHelper::capFirstWord($_POST['Product']['name']);
            $_model->size = !empty($_POST['Product']['size']) ? $_POST['Product']['size'] : NULL;
            $_model->description = !empty($_POST['Product']['description']) ? $_POST['Product']['description'] : NULL;
            $_model->is_damaged = isset($_POST['Product']['is_damaged']) ? $_POST['Product']['is_damaged'] : 0;
            $_model->has_size = 0;
            $_model->type = isset($_POST['Product']['type']) ? $_POST['Product']['type'] : NULL;
            $_model->unit = isset($_POST['Product']['unit']) ? $_POST['Product']['unit'] : NULL;
            $_model->unitsize = isset($_POST['Product']['unitsize']) ? $_POST['Product']['unitsize'] : NULL;
            $_model->code = strtoupper($_POST['Product']['code']);
            $_model->color = strtolower($_POST['Product']['color']);
            $_model->grade = strtoupper($_POST['Product']['grade']);
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
                Yii::app()->user->setFlash("success", "New record save successfull!");
                $this->redirect(array(AppUrl::URL_PRODUCT_LIST));
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
        $this->checkUserAccess('product_edit');
        $this->setHeadTitle("Products");
        $this->setPageTitle("Products");
        $this->setCurrentPage(AppUrl::URL_PRODUCT_LIST);

        $_model = new Product();
        $_key = Yii::app()->request->getParam('id');
        $_data = $_model->find('LOWER(_key) = ?', array(strtolower($_key)));

        if (isset($_POST['Product'])) {
            $_data->attributes = $_POST['Product'];
            $_data->company_id = $_POST['Product']['company_id'];
            $_data->category_id = $_POST['Product']['category_id'];
            $_data->name = AppHelper::capFirstWord($_POST['Product']['name']);
            $_data->size = !empty($_POST['Product']['size']) ? $_POST['Product']['size'] : NULL;
            $_data->description = !empty($_POST['Product']['description']) ? $_POST['Product']['description'] : NULL;
            $_data->type = isset($_POST['Product']['type']) ? $_POST['Product']['type'] : NULL;
            $_data->unit = isset($_POST['Product']['unit']) ? $_POST['Product']['unit'] : NULL;
            $_data->unitsize = isset($_POST['Product']['unitsize']) ? $_POST['Product']['unitsize'] : NULL;
            $_data->code = strtoupper($_POST['Product']['code']);
            $_data->color = strtolower($_POST['Product']['color']);
            $_data->grade = strtoupper($_POST['Product']['grade']);
            $_data->is_damaged = isset($_POST['Product']['is_damaged']) ? $_POST['Product']['is_damaged'] : 0;
            $_data->has_size = 0;

            $_transaction = Yii::app()->db->beginTransaction();
            try {
                if (!$_data->validate()) {
                    throw new CException(Yii::t("App", CHtml::errorSummary($_data)));
                }

                if (!empty($_FILES['Product']['name']['picture'])) {
                    $_filename = $_FILES['Product']['name']['picture'];
                    $_tmpfilename = $_FILES['Product']['tmp_name']['picture'];
                    $_filetype = $_FILES['Product']['type']['picture'];
                    $_filesize = $_FILES['Product']['size']['picture'];
                    $_size = ($_filesize / 1024) . " KB";
                    echo $_fileerror = $_FILES['Product']["error"]['picture'];

                    if ($_fileerror !== 0) {
                        throw new CException(Yii::t("App", "File Upload Error : " . $_fileerror));
                    }

                    $_savepath = Yii::getPathOfAlias('webroot') . '/uploads/products/';
                    $allowedExts = array("jpg", "jpeg", "png", "gif");
                    $temp = explode(".", $_filename);
                    $ext = end($temp);
                    $_newfilename = AppHelper::getUnqiueKey() . '.' . $ext;

                    if (!empty($data->picture)) {
                        unlink($_savepath . $data->picture);
                    }
                    $data->picture = $_newfilename;
                    move_uploaded_file($_tmpfilename, $_savepath . $_newfilename);
                }

//                AppHelper::pr($_FILES);
//                exit;

                if (!$_data->save()) {
                    throw new CException(Yii::t("App", "Error while saving data."));
                }

                $damegedModel = new DamegedProduct();
                $dameged = $damegedModel->find("product_id=:product_id", array(":product_id" => $_data->id));

                if ($_data->is_damaged == 1) {
                    if (empty($dameged)) {
                        $damegedModel->product_id = $_data->id;
                        $damegedModel->company_id = $_data->company_id;
                        $damegedModel->category_id = $_data->category_id;
                        if (!$damegedModel->save()) {
                            throw new CException(Yii::t("App", "Error while saving damaged info."));
                        }
                    }
                } else {
                    if (!empty($dameged)) {
                        if (!$dameged->delete()) {
                            throw new CException(Yii::t("App", "Error while removing damaged info."));
                        }
                    }
                }


                $_transaction->commit();
                Yii::app()->user->setFlash("success", "Record update successfull.");
                $this->redirect(array(AppUrl::URL_PRODUCT_LIST));
            } catch (CException $e) {
                $_transaction->rollback();
                Yii::app()->user->setFlash("danger", $e->getMessage());
            }
        }

        $this->model['model'] = $_data;
        $this->render('edit', $this->model);
    }

    public function actionDelete() {
        $this->checkUserAccess('product_delete');
        $_key = Yii::app()->request->getParam('id');

        $_model = new Product();
        $_product = $_model->find('LOWER(_key) = ?', array(strtolower($_key)));

        $_transaction = Yii::app()->db->beginTransaction();
        try {
            if (empty($_key)) {
                throw new CException(Yii::t("App", "You are trying to get invalid Url."));
            }

            if (empty($_product->id)) {
                throw new Exception(Yii::t("App", "No record found!"));
            }

            foreach ($_product->sizes as $size) {
                if (!$size->delete()) {
                    throw new CException(Yii::t("App", "Error while fetching internal data (sizes)."));
                }
            }

            if (!$_product->delete()) {
                throw new CException(Yii::t("App", "Error while deleting record."));
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
        $this->checkUserAccess('product_delete');
        $_model = new Product();
        $_data = $_POST['data'];

        if (isset($_data)) {
            $_transaction = Yii::app()->db->beginTransaction();
            try {
                for ($i = 0; $i < count($_data); $i++) {
                    $_obj = $_model->findByPk($_data[$i]);

                    foreach ($_obj->purchase as $purchase) {
                        if (!$purchase->delete()) {
                            throw new CException(Yii::t("App", "Error while deleting purchase data."));
                        }
                    }

                    foreach ($_obj->sale as $sale) {
                        if (!$sale->delete()) {
                            throw new CException(Yii::t("App", "Error while deleting sales data."));
                        }
                    }

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
            Yii::app()->user->setFlash('warning', "No record found to delete!");
        }
        $this->redirect(Yii::app()->request->urlReferrer);
    }

}
