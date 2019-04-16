<!DOCTYPE html>
<html lang="<?php echo Yii::app()->language; ?>">
    <head>
        <?php $this->beginContent('/layouts/panels/header'); ?>
        <?php $this->endContent(); ?>
    </head>
    <body class="body_pad">
        <header id="headerPanel">
            <?php $this->beginContent('/layouts/panels/headerPanel'); ?>
            <?php $this->endContent(); ?>
        </header>

        <section id="bodyPanel" style="">
            <div class="container-fluid">
                <div class="row clearfix">
                    <div class="col-md-2 col-sm-2 no_pad" id="admin_nav">
                        <?php $this->widget('UserMenu'); ?>
                    </div>
                    <div class="col-md-12" id="admin_view">
                        <?php
                        if (AppHelper::hasFlashMessage()) :
                            AppHelper::renderFlashMessage();
                        endif;
                        ?>

                        <div class="form-group">
                            <h2 class="page-title"><?php echo Yii::t('strings', $this->getPageTitle()); ?></h2>
                        </div>

                        <?php echo $content; ?>
                    </div>
                </div>
            </div>
        </section>

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