<h2 class="page-title-new"><?php echo $this->getPageTitle(); ?></h2>

<div class="alert alert-danger">
    <h2 style="margin: 0;">Error: <?php echo $error['code'] ?></h2>
</div>
<div class="alert alert-warning">
    <?php echo $error['message']; ?><br>
    <?php echo Yii::app()->errorHandler->error['message']; ?>
</div>
<?php
//AppHelper::pr($error);
?>