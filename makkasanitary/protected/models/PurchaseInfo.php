<?php

class PurchaseInfo extends CActiveRecord {

    public $quantity;
    public $retail_price;

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return '{{purchase_info}}';
    }

    public function relations() {
        return array(
            'purchase' => array(self::BELONGS_TO, 'Purchase', 'purchase_id'),
        );
    }

}
