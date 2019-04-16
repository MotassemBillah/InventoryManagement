<?php

class CustomerBalance extends CActiveRecord {

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return '{{customer_balance}}';
    }

    public function rules() {
        return array();
    }

    public function relations() {
        return array(
                //'customer' => array(self::BELONGS_TO, 'Customer', 'customer_id'),
        );
    }

}
