<?php
$this->breadcrumbs = array(
    $this->module->id => array(AppUrl::URL_LEDGER),
    'Expense' => array(AppUrl::URL_LEDGER_EXPENSE),
    'View',
);
?>
<div class="content-panel">
    <div id="printDiv">
        <div class="clearfix mb_10 mb_50 mp_mt" style="border-bottom:1px solid #ccc;margin-bottom:50px;padding-bottom:50px;">
            <div class="row clearfix text-center mp_center mb_10">
                <?php if (!empty($this->settings->logo)) : ?>
                    <img alt="" src="<?php echo Yii::app()->request->baseUrl . '/uploads/' . $this->settings->logo; ?>" style="max-height:50px;position:absolute;left:0;top:0;">
                <?php endif; ?>
                <?php if (!empty($this->settings->title)): ?>
                    <h1 style="font-weight: 600;font-size: 20px;margin: 0;"><?php echo $this->settings->title; ?></h1>
                <?php endif; ?>
                <?php if (!empty($this->settings->author_address)): ?>
                    <span class=""><?php echo $this->settings->author_address; ?></span><br>
                <?php endif; ?>
                <h4 class="inv_title">খরচের ভউচার
                    <span style="display:block;width:120px;height:1px;background-color:#000;margin:3px auto 0;"></span>
                </h4>
            </div>

            <div class="row clearfix mb_10">
                <div class="col-md-4 mpw_33">
                    <strong>ভউচার নং :</strong>&nbsp;<?php echo $model->id; ?>
                </div>
                <div class="col-md-4 mpw_33 pull-right text-right">
                    <strong>তারিখ :</strong>&nbsp;<?php echo date("j M Y, h:i A", strtotime($model->created)); ?>
                </div>
            </div>

            <table class="table table-striped table-bordered">
                <tr>
                    <th style="width:20%">গ্রহীতার নাম</th>
                    <td><?php echo $model->by_whom; ?></td>
                </tr>
                <tr>
                    <th style="width:20%">হিসাবের খাত</th>
                    <td><?php echo LedgerHead::model()->findByPk($model->ledger_head_id)->name; ?></td>
                </tr>
            </table>

            <table class="table table-striped table-bordered">
                <tr class="bg_gray">
                    <th>খরচের বিবরন</th>
                    <th class="text-right">টাকার পরিমান</th>
                </tr>
                <tr>
                    <td><?php echo $model->purpose; ?></td>
                    <td class="text-right"><?php echo $model->amount; ?></td>
                </tr>
                <tr class="bg_gray">
                    <td>
                        <strong>কথায় :</strong> <?php echo AppHelper::int_to_words($model->amount); ?>
                        <strong class="pull-right">মোট=</strong>
                    </td>
                    <td class="text-right"><?php echo $model->amount; ?></td>
                </tr>
            </table>

            <div class="row clearfix">
                <div class="col-md-3 form-group mpw_25" style="padding-top:30px;">
                    <div style="border-top:1px solid #000000;text-align:center;"><?php echo Yii::t("strings", "গ্রহীতার স্বাক্ষর"); ?></div>
                </div>
                <div class="col-md-3 form-group mpw_25" style="padding-top:30px;">
                    <div style="border-top:1px solid #000000;text-align:center;"><?php echo Yii::t("strings", "হিসাবরক্ষক"); ?></div>
                </div>
                <div class="col-md-3 form-group mpw_25" style="padding-top:30px;">
                    <div style="border-top:1px solid #000000;text-align:center;"><?php echo Yii::t("strings", "ব্যবস্থাপক"); ?></div>
                </div>
                <div class="col-md-3 form-group mpw_25" style="padding-top:30px;">
                    <div style="border-top:1px solid #000000;text-align:center;"><?php echo Yii::t("strings", "ব্যবস্থাপনা পরিচালক"); ?></div>
                </div>
            </div>
        </div>

        <div class="clearfix mb_10 show_in_print">
            <div class="row clearfix text-center mp_center mb_10">
                <?php if (!empty($this->settings->logo)) : ?>
                    <img alt="" src="<?php echo Yii::app()->request->baseUrl . '/uploads/' . $this->settings->logo; ?>" style="max-height:50px;position:absolute;left:0;top:0;">
                <?php endif; ?>
                <?php if (!empty($this->settings->title)): ?>
                    <h1 style="font-weight: 600;font-size: 20px;margin: 0;"><?php echo $this->settings->title; ?></h1>
                <?php endif; ?>
                <?php if (!empty($this->settings->author_address)): ?>
                    <span class=""><?php echo $this->settings->author_address; ?></span><br>
                <?php endif; ?>
                <h4 class="inv_title">খরচের ভউচার
                    <span style="display:block;width:120px;height:1px;background-color:#000;margin:3px auto 0;"></span>
                </h4>
            </div>

            <div class="row clearfix mb_10">
                <div class="col-md-4 mpw_33">
                    <strong>ভউচার নং :</strong>&nbsp;<?php echo $model->id; ?>
                </div>
                <div class="col-md-4 mpw_33 pull-right text-right">
                    <strong>তারিখ :</strong>&nbsp;<?php echo date("j M Y, h:i A", strtotime($model->created)); ?>
                </div>
            </div>

            <table class="table table-striped table-bordered">
                <tr>
                    <th style="width:20%">গ্রহীতার নাম</th>
                    <td><?php echo $model->by_whom; ?></td>
                </tr>
                <tr>
                    <th style="width:20%">হিসাবের খাত</th>
                    <td><?php echo LedgerHead::model()->findByPk($model->ledger_head_id)->name; ?></td>
                </tr>
            </table>

            <table class="table table-striped table-bordered">
                <tr class="bg_gray">
                    <th>খরচের বিবরন</th>
                    <th class="text-right">টাকার পরিমান</th>
                </tr>
                <tr>
                    <td><?php echo $model->purpose; ?></td>
                    <td class="text-right"><?php echo $model->amount; ?></td>
                </tr>
                <tr class="bg_gray">
                    <td>
                        <strong>কথায় :</strong> <?php echo AppHelper::int_to_words($model->amount); ?>
                        <strong class="pull-right">মোট=</strong>
                    </td>
                    <td class="text-right"><?php echo $model->amount; ?></td>
                </tr>
            </table>

            <div class="row clearfix">
                <div class="col-md-3 form-group mpw_25" style="padding-top:30px;">
                    <div style="border-top:1px solid #000000;text-align:center;"><?php echo Yii::t("strings", "গ্রহীতার স্বাক্ষর"); ?></div>
                </div>
                <div class="col-md-3 form-group mpw_25" style="padding-top:30px;">
                    <div style="border-top:1px solid #000000;text-align:center;"><?php echo Yii::t("strings", "হিসাবরক্ষক"); ?></div>
                </div>
                <div class="col-md-3 form-group mpw_25" style="padding-top:30px;">
                    <div style="border-top:1px solid #000000;text-align:center;"><?php echo Yii::t("strings", "ব্যবস্থাপক"); ?></div>
                </div>
                <div class="col-md-3 form-group mpw_25" style="padding-top:30px;">
                    <div style="border-top:1px solid #000000;text-align:center;"><?php echo Yii::t("strings", "ব্যবস্থাপনা পরিচালক"); ?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="clearfix">
        <div class="form-group text-center">
            <button type="button" class="btn btn-primary btn-xs" onclick="printDiv('printDiv')" style="margin-right:20px;"><i class="fa fa-print"></i>&nbsp;<?php echo Yii::t("strings", "Print"); ?></button>
            <a href="<?php echo Yii::app()->createUrl(AppUrl::URL_LEDGER_EXPENSE_CREATE); ?>"><button class="btn btn-success btn-xs"><i class="fa fa-plus"></i>&nbsp;<?php echo Yii::t("strings", "New"); ?></button></a>
        </div>
    </div>
</div>