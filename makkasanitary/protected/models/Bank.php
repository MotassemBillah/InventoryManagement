<?php

class Bank extends CActiveRecord {

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return '{{banks}}';
    }

    public function rules() {
        return array(
            array('name', 'required'),
        );
    }

    public function relations() {
        return array();
    }

    public function getList() {
        $criteria = new CDbCriteria();
        $criteria->condition = "is_deleted = 0";
        $criteria->order = "name ASC";
        $_dataset = Bank::model()->findAll($criteria);
        return $_dataset;
    }

}
