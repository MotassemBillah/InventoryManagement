<?php

class Company extends CActiveRecord {

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return '{{companies}}';
    }

    public function rules() {
        return array(
            array('name, phone', 'required'),
            array('email', 'email'),
            array('phone', 'numerical', 'integerOnly' => true, 'message' => 'Phone number must in numbers (0-9) only'),
        );
    }

    public function relations() {
        return array(
            'products' => array(self::HAS_MANY, 'Product', 'company_id'),
            'heads' => array(self::HAS_MANY, 'CompanyHead', 'company_id'),
        );
    }

    public function getList() {
        $criteria = new CDbCriteria();
        $criteria->condition = "is_deleted = 0";
        $criteria->order = "name ASC";
        $_dataset = Company::model()->findAll($criteria);
        return $_dataset;
    }

    public function stockAmount($id) {
        $_purchase = PurchaseItem::model()->sumCompanyTotal($id);
        $_sale = SaleItem::model()->sumCompanyTotal($id);
        $_amount = ($_purchase - $_sale);
        return !empty($_amount) ? AppHelper::getFloat($_amount) : 0;
    }

}
