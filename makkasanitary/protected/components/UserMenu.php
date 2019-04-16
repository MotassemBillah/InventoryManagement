<?php

class UserMenu extends CWidget {

    public $currentPage;

    public function init() {
        parent::init();
        $this->currentPage = $this->_getCurPage();
    }

    public function run() {
        $this->render('user_menu');
    }

    public function hasUserAccess($name) {
        $accessList = Permission::model()->find("user_id=:user_id", array(":user_id" => Yii::app()->user->id));
        $accssItems = json_decode($accessList->items);

        if (in_array($name, $accssItems)) {
            return true;
        } else {
            return false;
        }
    }

    public function checkUserAccess($name) {
        $niceName = str_replace('_', ' ', $name);
        if ($this->hasUserAccess($name)) {
            return true;
        } else {
            Yii::app()->user->setFlash("warning", Yii::t("strings", "You are not authorized For <strong style='text-transform:capitalize;'> " . ucfirst($niceName) . "</strong>"));
            if (!empty(Yii::app()->request->urlReferrer)) {
                $this->redirect(Yii::app()->request->urlReferrer);
            } else {
                $this->redirect($this->createUrl(AppUrl::URL_DASHBOARD));
            }
            Yii::app()->end();
        }
    }

    protected function _getCurPage() {
        $pathInfo = Yii::app()->getRequest()->getPathInfo();
        $route = explode('/', $pathInfo);
        $_module = $route[0];
        $_controller = Yii::app()->getController()->getAction()->controller->id;
        $_action = Yii::app()->getController()->getAction()->controller->action->id;

        if (Yii::app()->hasModule($_module)) {
            if (!empty($_action) && $_action == 'index') {
                $this->currentPage = "/{$_module}/{$_controller}";
            } else {
                $this->currentPage = "/{$_module}/{$_controller}/{$_action}";
            }
        } else {
            if (!empty($_action) && $_action == 'index') {
                $this->currentPage = "/{$_controller}";
            } else {
                $this->currentPage = "/{$_controller}/{$_action}";
            }
        }

        return $this->currentPage;
    }

}
