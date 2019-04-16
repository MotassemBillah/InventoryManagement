<?php

class Role extends CActiveRecord {

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return '{{roles}}';
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
        $criteria->order = "name ASC";
        $_dataset = Role::model()->findAll($criteria);
        return $_dataset;
    }

}
