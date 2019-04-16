<?php

class SaleItem extends CActiveRecord {

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return '{{sale_items}}';
    }

    public function rules() {
        return array(
                //array('product_id', 'required', 'message' => 'Please select a product'),
                //array('size_id', 'required', 'message' => 'Please select a product size'),
                //array('quantity', 'required', 'message' => 'Quantity cannot be blank'),
                //array('price', 'required', 'message' => 'Price cannot be blank'),
                //array('quantity', 'numerical', 'integerOnly' => true, 'message' => 'Please provide quantity in number'),
                //array('price', 'match', 'pattern' => '/([1-9][0-9]*?)(\.[0-9]{2})?/', 'message' => 'Price is invalid. Please choose from 0.01 to 9999.99'),
        );
    }

    public function relations() {
        return array(
            'sale' => array(self::BELONGS_TO, 'Sale', 'sale_id'),
            'product' => array(self::BELONGS_TO, 'Product', 'product_id'),
        );
    }

    public function sumCompanyTotal($company = null) {
        if (!is_null($company)) {
            $_where = "company_id={$company}";
        } else {
            $_where = "";
        }
        $data = Yii::app()->db->createCommand()->select('SUM(total) as total')->where($_where)->from($this->tableName())->queryRow();
        return !empty($data['total']) ? AppHelper::getFloat($data['total']) : 0;
    }

}
