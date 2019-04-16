<?php

class Cart extends CActiveRecord {

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return '{{cart}}';
    }

    public function rules() {
        return array();
    }

    public function relations() {
        return array(
            'product' => array(self::BELONGS_TO, 'Product', 'product_id'),
        );
    }

}
