<?php

class Category extends CActiveRecord {

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return '{{categories}}';
    }

    public function rules() {
        return array(
            array('name', 'required'),
        );
    }

    public function relations() {
        return array(
            'product' => array(self::HAS_MANY, 'Product', 'category_id'),
        );
    }

    public function getCategoryOptions() {
        $asts = Category::model()->findAll();
        $arr = array();
        foreach ($asts as $ast) :
            $astd = Category::model()->findByPk($ast['parent']);
            $arr[] = array('id' => $ast['id'], 'text' => $ast['name'], 'group' => $astd['name']);
        endforeach;

        return $arr;
    }

    public function getList() {
        $criteria = new CDbCriteria();
        $criteria->order = "name ASC";
        $_dataset = Category::model()->findAll($criteria);
        return $_dataset;
    }

}
