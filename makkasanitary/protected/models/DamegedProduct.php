<?php

class DamegedProduct extends CActiveRecord {

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return '{{damaged_products}}';
    }

    public function rules() {
        return array();
    }

    public function relations() {
        return array(
            'product' => array(self::BELONGS_TO, 'Product', 'product_id'),
            'company' => array(self::BELONGS_TO, 'Company', 'company_id'),
        );
    }

}
