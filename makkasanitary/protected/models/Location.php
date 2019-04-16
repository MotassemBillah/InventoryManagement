<?php

class Location extends CActiveRecord {

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return '{{locations}}';
    }

    public function rules() {
        return array(
            array('name', 'required'),
        );
    }

    public function relations() {
        return array(
//            'product' => array(self::BELONGS_TO, 'Product', 'product_id'),
        );
    }

}
