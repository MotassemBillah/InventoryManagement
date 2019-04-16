<meta charset="<?php echo Yii::app()->charset; ?>">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">

<title><?php echo $this->headTitle; ?></title>

<link href="<?php echo!empty($this->settings->favicon) ? Yii::app()->request->baseUrl . '/uploads/' . $this->settings->favicon : ''; ?>" rel="icon" type="image/x-icon">
<link href="<?php echo Yii::app()->request->baseUrl; ?>/fonts/font-awesome.min.css" rel="stylesheet" type="text/css">
<link href="<?php echo Yii::app()->request->baseUrl; ?>/css/bootstrap.min.css" rel="stylesheet" type="text/css">
<link href="<?php echo Yii::app()->request->baseUrl; ?>/css/style.css" rel="stylesheet" type="text/css">
<link href="<?php echo Yii::app()->request->baseUrl; ?>/css/responsive.css" rel="stylesheet" type="text/css" media="screen">
<link href="<?php echo Yii::app()->request->baseUrl; ?>/css/print.css" rel="stylesheet" type="text/css" media="print">
<?php $this->writeCss(); ?>

<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery-1.9.1.min.js" type="text/javascript"></script>
<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery.validate.js" type="text/javascript"></script>
<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/bootstrap.min.js" type="text/javascript"></script>
<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/global.js" type="text/javascript"></script>
<?php $this->writeJs(); ?>

<script  type="text/javascript">
    var baseUrl = '<?php echo Yii::app()->request->baseUrl; ?>';
    var ajaxUrl = '<?php echo Yii::app()->request->baseUrl; ?>/ajax';
    var ledgerUrl = '<?php echo Yii::app()->request->baseUrl; ?>/ledger';
</script>