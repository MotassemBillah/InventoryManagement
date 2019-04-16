<?php

class UrlManager extends CUrlManager {

//    public function createUrl($route, $params = array(), $ampersand = '&') {
//        if (!isset($params['language'])) {
//            if (Yii::app()->user->hasState('language'))
//                Yii::app()->language = Yii::app()->user->getState('language');
//            else if (isset(Yii::app()->request->cookies['language']))
//                Yii::app()->language = Yii::app()->request->cookies['language']->value;
//            $params['language'] = Yii::app()->language;
//        }
//        return parent::createUrl($route, $params, $ampersand);
//    }

    public function createUrl($route, $params = array(), $ampersand = '&') {
        // We added this by default to all links to show
        // Content based on language - Add only when not excplicity set
        if (!isset($params['language'])) {
            $params['language'] = Yii::app()->language;
        }

        if (( isset($params['language']) && $params['language'] === false)) {
            unset($params['language']);
        }

        // Use parent to finish url construction
        return parent::createUrl($route, $params, $ampersand);
    }

}

?>