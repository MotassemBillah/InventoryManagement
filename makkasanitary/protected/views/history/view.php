<?php
$this->breadcrumbs = array(
    'History' => array(AppUrl::URL_HISTORY),
    'View'
);
?>
<div class="content-panel">
    <div class="row clearfix mb_10">
        <div class="col-md-4 mpw_33">
            <strong><u>User</u> : </strong><?php echo User::model()->displayname($model->user_id); ?>
        </div>
        <div class="col-md-4 mpw_33 pull-right text-right">
            <strong><u>Date/Time</u> : </strong><?php echo date("j M Y, h:i:s A", strtotime($model->date_time)); ?>
        </div>
    </div>

    <div class="clearfix mb_10">
        <strong><u>Note</u> : </strong><?php echo $model->note; ?>
    </div>
</div>