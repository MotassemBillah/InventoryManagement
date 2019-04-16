<?php

class Invoice extends CActiveRecord {

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return '{{invoices}}';
    }

    public function rules() {
        return array();
    }

    public function relations() {
        return array();
    }

}
