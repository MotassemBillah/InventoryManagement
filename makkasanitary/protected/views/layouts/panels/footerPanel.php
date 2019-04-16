<div id="footerBottom">
    <div class="container-fluid">
        <div class="text-center">
            <p>
                <?php echo Yii::app()->params['copyrightInfo']; ?><br>
                <?php
                if (YII_DEBUG):
                    echo Yii::getLogger()->getExecutionTime();
                endif;
                ?>
            </p>
        </div>
    </div>
</div>