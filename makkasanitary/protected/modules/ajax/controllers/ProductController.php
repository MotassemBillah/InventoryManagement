<?php

class ProductController extends AppController {

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
        $_company = Yii::app()->request->getPost('company_id');
        $_category = Yii::app()->request->getPost('category_id');
        $_type = Yii::app()->request->getPost('type');
        $_q = Yii::app()->request->getPost('q');
        $_damaged = Yii::app()->request->getPost('damaged');

        $_model = new Product();
        $criteria = new CDbCriteria();
        $criteria->condition = "is_deleted = 0";

        if (!empty($_company)) {
            $criteria->addCondition("company_id =" . $_company);
        }
        if (!empty($_category)) {
            $criteria->addCondition("category_id =" . $_category);
        }
        if (!empty($_type)) {
            $criteria->addCondition("type =" . $_type);
        }
        if (!empty($_q)) {
            $criteria->addCondition("name like '%" . $_q . "%'");
            $this->model['highlight'] = $_q;
        }
        if (!empty($_damaged)) {
            $criteria->addCondition("is_damaged = 1");
        } else {
            $criteria->addCondition("is_damaged = 0");
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

    public function actionList() {
        $_column = Yii::app()->request->getPost('column');
        $_order = Yii::app()->request->getPost('order');
        $_limit = Yii::app()->request->getPost('itemCount');
        $_company = Yii::app()->request->getPost('company_id');
        $_category = Yii::app()->request->getPost('category_id');
        $_type = Yii::app()->request->getPost('type');
        $_q = Yii::app()->request->getPost('q');
        $_damaged = Yii::app()->request->getPost('damaged');

        $_model = new Product();
        $criteria = new CDbCriteria();
        $criteria->condition = "is_deleted = 0";
        if (!empty($_company)) {
            $criteria->addCondition("company_id =" . $_company);
        }
        if (!empty($_category)) {
            $criteria->addCondition("category_id =" . $_category);
        }
        if (!empty($_type)) {
            $criteria->addCondition("type =" . $_type);
        }
        if (!empty($_q)) {
            $criteria->addCondition("name like '%" . $_q . "%'");
            $this->model['highlight'] = $_q;
        }
        if (!empty($_damaged)) {
            $criteria->addCondition("is_damaged = 1");
        } else {
            $criteria->addCondition("is_damaged = 0");
        }
        if (!empty($_order)) {
            $criteria->order = "{$_column} {$_order}";
        } else {
            $criteria->order = "name ASC";
        }
        $count = $_model->count($criteria);
        $pages = new CPagination($count);
        $pages->pageSize = !empty($_limit) ? $_limit : $this->page_size;
        $pages->applyLimit($criteria);
        $_dataset = $_model->findAll($criteria);
        $_filter = ($_order == "DESC") ? "ASC" : "DESC";

        $this->model['dataset'] = $_dataset;
        $this->model['pages'] = $pages;
        $this->model['filter'] = $_filter;
        $this->renderPartial('list', $this->model);
    }

    public function actionDamage_stock() {
        $_limit = Yii::app()->request->getPost('itemCount');
        $_company = Yii::app()->request->getPost('company_id');
        $_category = Yii::app()->request->getPost('category_id');

        $_model = new DamegedProduct();
        $criteria = new CDbCriteria();
        //$criteria->condition = "is_damaged = 1";

        if (!empty($_company)) {
            $criteria->addCondition("company_id =" . $_company);
        }
        if (!empty($_category)) {
            $criteria->addCondition("category_id =" . $_category);
        }

        $count = $_model->count($criteria);
        $pages = new CPagination($count);
        $pages->pageSize = !empty($_limit) ? $_limit : $this->page_size;
        $pages->applyLimit($criteria);
        $_dataset = $_model->findAll($criteria);

        $this->model['dataset'] = $_dataset;
        $this->model['pages'] = $pages;
        $this->renderPartial('damage_stock', $this->model);
    }

    public function actionDamage_list() {
        $_limit = Yii::app()->request->getPost('itemCount');
        $_company = Yii::app()->request->getPost('company_id');
        $_category = Yii::app()->request->getPost('category_id');

        $_model = new DamegedProduct();
        $criteria = new CDbCriteria();

        if (!empty($_company)) {
            $criteria->addCondition("company_id =" . $_company);
        }
        if (!empty($_category)) {
            $criteria->addCondition("category_id =" . $_category);
        }

        $count = $_model->count($criteria);
        $pages = new CPagination($count);
        $pages->pageSize = !empty($_limit) ? $_limit : $this->page_size;
        $pages->applyLimit($criteria);
        $_dataset = $_model->findAll($criteria);

        $this->model['dataset'] = $_dataset;
        $this->model['pages'] = $pages;
        $this->renderPartial('damage_list', $this->model);
    }

    public function actionDeleteall() {
        $_model = new Product();
        $response = array();
        $_data = $_POST['data'];

        if (isset($_data)) {
            $_transaction = Yii::app()->db->beginTransaction();
            try {
                for ($i = 0; $i < count($_data); $i++) {
                    $_obj = $_model->findByPk($_data[$i]);

                    //$this->delete_purchase($_obj);
                    //$this->delete_sale($_obj);
                    $this->delete_stock($_obj);
                    $this->delete_size($_obj);

                    $_obj->is_deleted = 1;
                    if (!$_obj->save()) {
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

    public function actionRemove_size() {
        $_model = new Size();
        $response = array();
        $ID = Yii::app()->request->getParam('size');
        $_data = $_model->findByPk($ID);

        if (!empty($_data)) {
            $_transaction = Yii::app()->db->beginTransaction();
            try {
                if (!$_data->delete()) {
                    throw new CException(Yii::t('App', "Error while deleting record"));
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

        echo CJSON::encode($response);
        return CJSON::encode($response);
    }

    public function actionSearch() {
        $_company = Yii::app()->request->getPost('com');
        $_category = Yii::app()->request->getPost('cat');
        $_type = Yii::app()->request->getPost('type');

        $_model = new Product();
        $criteria = new CDbCriteria();
        $criteria->condition = "is_deleted = 0";

        if (!empty($_company)) {
            $criteria->addCondition("company_id ='" . $_company . "'");
        }
        if (!empty($_category)) {
            $criteria->addCondition("category_id ='" . $_category . "'");
        }
        if (!empty($_type)) {
            $criteria->addCondition("type ='" . $_type . "'");
        }
        $criteria->order = "name ASC";

        $dataset = $_model->findAll($criteria);
        $this->model['dataset'] = $dataset;
        $this->renderPartial('search', $this->model);
    }

    public function actionHistory() {
        $productID = Yii::app()->request->getParam('productID');
        $_model = new Product();
        $_data = $_model->with('purchase_items', 'sale_items')->findByPk($productID);

        $this->model['model'] = $_data;
        $this->renderPartial('history', $this->model);
    }

    public function actionFind() {
        $response = array();
        $saleId = Yii::app()->request->getPost('saleId');
        $_name = Yii::app()->request->getPost('name');

        $_model = new Product();
        $criteria = new CDbCriteria();
        $criteria->condition = "is_deleted = 0";
        if (!empty($_name)) {
            $criteria->addCondition("name like '%" . $_name . "%'");
        }
        $criteria->order = "name ASC";
        $dataset = $_model->findAll($criteria);

        $_html = '<ul class="pro_list">';
        if (!empty($dataset)) {
            foreach ($dataset as $data) {
                $catName = "<u>" . AppObject::categoryName($data->category_id) . "</u>";
                $typeName = !empty($data->type) ? " - <u>" . AppObject::companyHeadName($data->type) . "</u>" : " - <u>n/a</u>";
                $colorName = !empty($data->color) ? " - <u>" . strtolower($data->color) . "</u>" : " - <u>n/a</u>";
                $gradeName = !empty($data->grade) ? " - <u>" . strtoupper($data->grade) . "</u>" : " - <u>n/a</u>";
                $_html .= "<li>";
                $_html .= "<label class='txt_np' for='product_{$data->id}'>";
                $_html .= "<input type='checkbox' id='product_{$data->id}' class='include_product' name='productID' value='{$data->id}'>&nbsp;{$data->name} [{$catName}{$typeName}{$colorName}{$gradeName}]";
                $_html .= "</label>";
                $_html .= "</li>";
            }
            $response['success'] = true;
        } else {
            $response['success'] = false;
            $_html .= '<li>Not Found</li>';
        }
        $_html .= '</ul>';
        $response['html'] = $_html;

        echo json_encode($response);
        return json_encode($response);
    }

    /*
     * protected functions for
     * delete related model data
     */

    protected function delete_purchase($_object) {
        if (!empty($_object->purchase_items)) {
            foreach ($_object->purchase_items as $items) {
                if (!$items->delete()) {
                    throw new CException(Yii::t("App", "Error while deleting purchase items."));
                }
            }
        }

//        foreach ($_idArr as $_key => $_value) {
//            $_purchase = Purchase::model()->findByPk($_value);
//
//            foreach ($_purchase as $_data) {
//                if (!$_data->delete()) {
//                    throw new CException(Yii::t("App", "Error while deleting purchase."));
//                }
//            }
//        }
    }

    protected function delete_sale($_object) {
        if (!empty($_object->sale_items)) {
            foreach ($_object->sale_items as $items) {
                if (!$items->delete()) {
                    throw new CException(Yii::t("App", "Error while deleting sale items."));
                }
            }
        }

//        foreach ($_idArr as $_key => $_value) {
//            $_sales = Sale::model()->findByPk($_value);
//
//            foreach ($_sales as $_data) {
//                if (!$_data->delete()) {
//                    throw new CException(Yii::t("App", "Error while deleting sales."));
//                }
//            }
//        }
    }

    protected function delete_stock($_object) {
        if (!empty($_object->stocks)) {
            foreach ($_object->stocks as $_data) {
                if (!$_data->delete()) {
                    throw new CException(Yii::t("App", "Error while deleting stocks."));
                }
            }
        }
    }

    protected function delete_size($_object) {
        if (!empty($_object->sizes)) {
            foreach ($_object->sizes as $sizes) {
                if (!$sizes->delete()) {
                    throw new CException(Yii::t("App", "Error while deleting sizes."));
                }
            }
        }
    }

}
