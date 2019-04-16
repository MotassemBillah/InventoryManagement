<?php

class SaleReturnItem extends CActiveRecord {

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return '{{sale_return_items}}';
    }

    public function rules() {
        return array();
    }

    public function relations() {
        return array(
            'retSale' => array(self::BELONGS_TO, 'SaleReturn', 'sale_return_id'),
            'product' => array(self::BELONGS_TO, 'Product', 'product_id'),
        );
    }

}
