<?php

class SaleReturnInfo extends CActiveRecord {

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return '{{sale_return_info}}';
    }

    public function relations() {
        return array(
            'retSale' => array(self::BELONGS_TO, 'SaleReturn', 'sale_return_id'),
        );
    }

}
