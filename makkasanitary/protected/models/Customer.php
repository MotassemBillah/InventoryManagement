<?php

class Customer extends CActiveRecord {

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return '{{customers}}';
    }

    public function rules() {
        return array(
            array('name', 'required'),
            array('phone', 'required'),
            //array('email', 'email'),
            array('phone', 'unique'),
            array('phone', 'numerical', 'integerOnly' => true),
        );
    }

    public function relations() {
        return array(
            'sales' => array(self::HAS_MANY, 'Sale', 'customer_id'),
            'payments' => array(self::HAS_MANY, 'CustomerPayment', 'customer_id'),
                //'balance' => array(self::HAS_MANY, 'CustomerBalance', 'customer_id'),
                //'sumBalance' => array(self::STAT, 'CustomerBalance', 'customer_id', 'select' => 'SUM(balance)'),
        );
    }

    public function getList() {
        $criteria = new CDbCriteria();
        $criteria->order = "name ASC";
        $_dataset = Customer::model()->findAll($criteria);
        return $_dataset;
    }

    public function typeList() {
        return [
            AppConstant::CTYPE_ADVANCE => AppConstant::CTYPE_ADVANCE,
            AppConstant::CTYPE_DUE => AppConstant::CTYPE_DUE,
            AppConstant::CTYPE_REGULAR => AppConstant::CTYPE_REGULAR,
        ];
    }

    public function updateBalance($id) {
        $_customer = Customer::model()->findByPk($id);
        $_balance = AppObject::sumBalanceAmount($id);
        if ($_balance > 0) {
            $_customer->type = AppConstant::CTYPE_ADVANCE;
        } elseif ($_balance < 0) {
            $_customer->type = AppConstant::CTYPE_DUE;
        } else {
            $_customer->type = AppConstant::CTYPE_REGULAR;
        }
        $_customer->save();
    }

    public function schemaInfo() {
        $exclude = ['id', 'code', 'avatar', 'password', '_key'];
        $schemaInfo = Yii::app()->db->schema->getTable($this->tableName());

        foreach ($exclude as $k => $v) {
            unset($schemaInfo->columns[$v]);
        }

        return $schemaInfo;
    }

}
