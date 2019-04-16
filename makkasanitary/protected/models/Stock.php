<?php

class Stock extends CActiveRecord {

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return '{{stocks}}';
    }

    public function rules() {
        return array(
//            array('name, model_no', 'required'),
        );
    }

    public function relations() {
        return array(
            'product' => array(self::BELONGS_TO, 'Product', 'product_id'),
            'purchase' => array(self::BELONGS_TO, 'Purchase', 'purchase_id'),
            'sales' => array(self::BELONGS_TO, 'Sale', 'sale_id'),
        );
    }

}
