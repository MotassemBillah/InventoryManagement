<?php

class SaleReturnPayment extends CActiveRecord {

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return '{{sale_return_payments}}';
    }

    public function rules() {
        return array();
    }

    public function relations() {
        return array(
            'return_sale' => array(self::BELONGS_TO, 'SaleReturn', 'sale_return_id'),
        );
    }

}
