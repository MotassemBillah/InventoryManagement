<?php

class Product extends CActiveRecord {

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return '{{products}}';
    }

    public function rules() {
        return array(
            //array('product_id', 'required', 'on' => 'sale', 'message' => 'Please select a product'),
            array('name', 'required', 'on' => 'create'),
            array('company_id', 'required', 'on' => 'create', 'message' => 'Please select a company'),
            array('category_id', 'required', 'on' => 'create', 'message' => 'Please select a category'),
            array('name,company_id,category_id', 'safe'),
        );
    }

    public function relations() {
        return array(
            'company' => array(self::HAS_MANY, 'Company', 'company_id'),
            'category' => array(self::BELONGS_TO, 'Category', 'category_id'),
            'sizes' => array(self::HAS_MANY, 'Size', 'product_id'),
            'purchase_items' => array(self::HAS_MANY, 'PurchaseItem', 'product_id'),
            'sale_items' => array(self::HAS_MANY, 'SaleItem', 'product_id'),
            'location' => array(self::HAS_ONE, 'Location', 'product_id'),
            'stocks' => array(self::HAS_MANY, 'Stock', 'product_id'),
        );
    }

    public function getName() {
        return $this->name;
    }

    public function getCompany() {
        return $this->hasMany(Company::className(), ['id' => 'company_id']);
    }

    public function gradeList() {
        $criteria = new CDbCriteria();
        $criteria->distinct = true;
        $criteria->condition = "grade = grade";
        $criteria->addCondition('grade IS NOT NULL');
        $_dataset = Product::model()->findAll($criteria);
        return $_dataset;
    }

}
