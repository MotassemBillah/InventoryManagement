<?php

Yii::setPathOfAlias('AjaxModule', dirname(__FILE__));

class AjaxModule extends CWebModule {

    public function init() {
        $this->setImport(array(
            'AjaxModule.controllers.*',
            'AjaxModule.models.*',
            'AjaxModule.components.*',
//            'AjaxModule.core.*',
        ));
    }

}