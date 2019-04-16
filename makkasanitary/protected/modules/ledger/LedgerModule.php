<?php

Yii::setPathOfAlias('LedgerModule', dirname(__FILE__));

class LedgerModule extends CWebModule {

    public function init() {
        parent::init();
        $this->layoutPath = Yii::getPathOfAlias('application.modules.ledger.views.layouts');

        $this->setImport(array(
            'ledger.components.*',
            'ledger.controllers.*',
            'ledger.models.*',
        ));
    }

    public function beforeControllerAction($controller, $action) {
        if (parent::beforeControllerAction($controller, $action)) {
            // this method is called before any module controller action is performed
            // you may place customized code here
            return true;
        } else
            return false;
    }

}
