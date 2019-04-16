<!DOCTYPE html>
<html lang="<?php echo Yii::app()->language; ?>">
    <head>
        <?php $this->beginContent('/layouts/panels/header'); ?>
        <?php $this->endContent(); ?>
    </head>
    <body class="body_pad" onFocus="parent_disable();" onclick="parent_disable();">
        <header id="headerPanel">
            <?php $this->beginContent('/layouts/panels/headerPanel'); ?>
            <?php $this->endContent(); ?>
        </header>

        <div id="bodyPanel">
            <div class="container-fluid">
                <div class="row clearfix">
                    <div class="col-md-2 col-sm-2 no_pad" id="admin_nav">
                        <?php $this->widget('UserMenu'); ?>
                    </div>
                    <div class="col-md-12" id="admin_view">
                        <div id="breadcrumbBar" class="breadcrumb site_nav_links no_bdr_rad clearfix">
                            <div class="col-md-3 col-sm-3 col-xs-2 cxs_2 no_pad">
                                <button class="btn btn-info btn-xs" type="button" onclick="history.back()" title="<?php echo Yii::t("strings", "Go Back"); ?>"><span class="visible-xs"><i class="fa fa-arrow-left"></i></span><span class="hidden-xs"><?php echo Yii::t("strings", "Back"); ?></span></button>
                                <button class="btn btn-info btn-xs" onclick="redirectTo('<?php echo Yii::app()->createUrl(AppUrl::URL_CLEAR_CACHE); ?>')" title="<?php echo Yii::t("strings", "Refresh"); ?>" type="button"><span class="visible-xs"><i class="fa fa-refresh"></i></span><span class="hidden-xs"><?php echo Yii::t("strings", "Refresh"); ?></span></button>
                            </div>
                            <div class="col-md-6 col-sm-6 col-xs-6 cxs_10 text-center">
                                <h2 class="page-title"><?php echo Yii::t('strings', $this->getPageTitle()); ?></h2>
                            </div>
                            <div class="col-md-3 col-sm-3 col-xs-4 cxs_12 no_pad">
                                <?php $this->beginContent('/layouts/panels/breadcrumb'); ?>
                                <?php $this->endContent(); ?>
                            </div>
                        </div>
                        <?php
                        if (AppHelper::hasFlashMessage()) :
                            AppHelper::renderFlashMessage();
                        endif;
                        ?>
                        <div id="ajaxHandler">
                            <div class="alert alert-danger" id="ajaxMessage"></div>
                        </div>
                        <?php echo $content; ?>
                    </div>
                </div>
            </div>
        </div>

        <footer id="footerPanel">
            <?php $this->beginContent('/layouts/panels/footerPanel'); ?>
            <?php $this->endContent(); ?>
        </footer>
        <div id="popup"></div>
        <div id="superLoader">
            <img alt="loading..." class="img-responsive" src="<?php echo Yii::app()->request->baseUrl; ?>/img/loading.gif">
            <span id="superLoaderText"></span>
        </div>
        <div id="cover"></div>
    </body>
</html>