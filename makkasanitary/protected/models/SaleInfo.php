<?php

class SaleInfo extends CActiveRecord {

    public $quantity;
    public $retail_price;

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return '{{sale_info}}';
    }

    public function relations() {
        return array(
            'sale' => array(self::BELONGS_TO, 'Sale', 'sale_id'),
        );
    }

}
