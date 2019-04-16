<!DOCTYPE html>
<html lang="<?php echo Yii::app()->language; ?>">
    <head>
        <?php $this->beginContent('/layouts/panels/header'); ?>
        <?php $this->endContent(); ?>
    </head>
    <body>
        <header id="headerPanel">
            <?php $this->beginContent('/layouts/panels/headerPanel'); ?>
            <?php $this->endContent(); ?>
        </header>

        <section id="bodyPanel" style="min-height: 400px;">
            <div class="container-fluid">
                <div class="form-group">
                    <h2 class="page-title"><?php echo Yii::t('strings', $this->getPageTitle()); ?></h2>
                </div>
                <?php
                if (AppHelper::hasFlashMessage()):
                    AppHelper::renderFlashMessage();
                endif;
                ?>
                <?php echo $content; ?>
            </div>
        </section>

        <footer id="footerPanel" class="">
            <?php $this->beginContent('/layouts/panels/footerPanel'); ?>
            <?php $this->endContent(); ?>
        </footer>
        <div id="popup"></div>
    </body>
</html>