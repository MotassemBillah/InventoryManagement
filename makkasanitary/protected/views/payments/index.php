<?php
$this->breadcrumbs = array(
    'Payments'
);
?>
<div class="well">
    <table width="100%">
        <tr>
            <td class="wmd_70">
                <form class="search-form" method="post" name="frmSearch" id="frmSearch">
                    <div class="input-group">
                        <div class="input-group-btn clearfix">
                            <select id="itemCount" class="form-control" name="itemCount" style="width:55px;">
                                <?php
                                for ($i = 10; $i <= 100; $i+=10) {
                                    if ($i == $this->settings->page_size) {
                                        echo "<option value='{$i}' selected='selected'>{$i}</option>";
                                    } else {
                                        echo "<option value='{$i}'>{$i}</option>";
                                    }
                                }
                                ?>
                            </select>
                            <div class="col-md-2 col-sm-3 no_pad">
                                <?php
                                $companyList = Company::model()->getList();
                                $comList = CHtml::listData($companyList, 'id', 'name');
                                echo CHtml::dropDownList('company', 'company_id', $comList, array('empty' => 'Company', 'class' => 'form-control'));
                                ?>
                            </div>
                            <div class="col-md-2 col-sm-3 no_pad">
                                <div class="input-group xsw_100">
                                    <input type="text" id="from_date" class="form-control" name="from_date" placeholder="(dd-mm-yyyy)" readonly>
                                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                                </div>
                            </div>
                            <div class="col-md-1 col-sm-1 text-center" style="font-size:14px;width:5%;">
                                <b style="color: rgb(0, 0, 0); vertical-align: middle; display: block; padding: 6px 0px;">TO</b>
                            </div>
                            <div class="col-md-2 col-sm-3 no_pad">
                                <div class="input-group xsw_100">
                                    <input type="text" id="to_date" class="form-control" name="to_date" placeholder="(dd-mm-yyyy)" readonly>
                                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                                </div>
                            </div>
                            <input type="number" id="q" name="q" class="form-control" placeholder="invoice" style="max-width: 170px;">
                            <button type="button" id="search" class="btn btn-info"><?php echo Yii::t("strings", "Search"); ?></button>
                        </div>
                    </div>
                </form>
            </td>
            <td class="text-right wmd_30" style="">
                <a href="<?php echo Yii::app()->createUrl(AppUrl::URL_PAYMENT_CREATE); ?>"><button class="btn btn-success"><i class="fa fa-plus"></i>&nbsp;<?php echo Yii::t("strings", "New"); ?></button></a>
                <?php if ($this->hasUserAccess('payment_delete')): ?>
                    <button type="button" class="btn btn-danger" id="admin_del_btn" disabled="disabled" ><?php echo Yii::t("strings", "Delete"); ?></button>
                <?php endif; ?>
            </td>
        </tr>
    </table>
</div>

<form id="deleteForm" action="#" method="post">
    <div id="ajaxContent">
        <?php if (!empty($dataset) && count($dataset) > 0) : ?>
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <tr id="r_checkAll">
                        <th class="text-center" style="width:5%;"><?php echo Yii::t('strings', 'SL#'); ?></th>
                        <th><?php echo Yii::t('strings', 'Company'); ?></th>
                        <th><?php echo Yii::t('strings', 'Pay Date'); ?></th>
                        <th><?php echo Yii::t('strings', 'Method'); ?></th>
                        <th class="text-right"><?php echo Yii::t('strings', 'Invoice Bill'); ?></th>
                        <th class="text-right"><?php echo Yii::t('strings', 'Discount'); ?></th>
                        <th class="text-right"><?php echo Yii::t('strings', 'Invoice Paid'); ?></th>
                        <th class="text-right"><?php echo Yii::t('strings', 'Advance'); ?></th>
                        <th class="text-right"><?php echo Yii::t('strings', 'Balance'); ?></th>
                        <th class="text-center"><?php echo Yii::t('strings', 'Actions'); ?></th>
                        <?php if ($this->hasUserAccess('payment_delete')): ?>
                            <th class="text-center" style="width:3%;"><input type="checkbox" id="checkAll" onclick="toggleCheckboxes(this)"></th>
                        <?php endif; ?>
                    </tr>
                    <?php
                    $counter = 0;
                    foreach ($dataset as $data):
                        $counter++;
                        ?>
                        <tr class="">
                            <td class="text-center" style="width:5%;"><?php echo $counter; ?></td>
                            <td><?php echo AppObject::companyName($data->company_id); ?></td>
                            <td><?php echo!empty($data->pay_date) ? date('j M Y', strtotime($data->pay_date)) : ""; ?></td>
                            <td>
                                <?php
                                if ($data->payment_mode == AppConstant::PAYMENT_CHECK) {
                                    echo $data->payment_mode . "<br>";
                                    echo "<u>Bank</u>: " . $data->bank_name . "<br>";
                                    echo "<u>Check No</u>: " . $data->check_no;
                                } else if ($data->payment_mode == AppConstant::PAYMENT_CASH) {
                                    echo "<span style='color:forestgreen'>" . $data->payment_mode . "</span>";
                                } else {
                                    echo "<span style='color:grey'>" . AppConstant::PAYMENT_NO . "</span>";
                                }
                                ?>
                            </td>
                            <td class="text-right<?php echo!empty($data->invoice_amount) ? " bg_gray" : ""; ?>"><?php echo AppHelper::getFloat($data->invoice_amount); ?></td>
                            <td class="text-right<?php echo!empty($data->discount_amount) ? " bg_gray" : ""; ?>"><?php echo AppHelper::getFloat($data->discount_amount); ?></td>
                            <td class="text-right<?php echo!empty($data->invoice_paid) ? " bg_gray" : ""; ?>"><?php echo AppHelper::getFloat($data->invoice_paid); ?></td>
                            <td class="text-right<?php echo!empty($data->advance_amount) ? " bg_gray" : ""; ?>"><?php echo AppHelper::getFloat($data->advance_amount); ?></td>
                            <td class="text-right<?php echo!empty($data->balance_amount) ? " bg_gray" : ""; ?>"><?php echo AppHelper::getFloat($data->balance_amount); ?></td>
                            <td class="text-center">
                                <?php if (empty($data->invoice_amount) && in_array($data->type, array(AppConstant::TYPE_ADVANCE, AppConstant::TYPE_DUE))): ?>
                                    <?php if ($this->hasUserAccess('payment_edit')): ?>
                                        <a class="btn btn-info btn-xs" href="<?php echo $this->createUrl(AppUrl::URL_PAYMENT_EDIT, array('id' => $data->_key)); ?>"><?php echo Yii::t('strings', 'Edit'); ?></a>
                                    <?php endif; ?>
                                <?php endif; ?>
                                <?php if (!empty($data->invoice_no)): ?>
                                    <a class="btn btn-primary btn-xs" href="<?php echo $this->createUrl(AppUrl::URL_PURCHASE_VIEW, array('id' => $data->invoice_no)); ?>" target="_blank"><?php echo Yii::t('strings', 'View'); ?></a>
                                <?php endif; ?>
                            </td>
                            <?php if ($this->hasUserAccess('payment_delete')): ?>
                                <td class="text-center" style="width:3%;"><input type="checkbox" name="data[]" value="<?php echo $data->id; ?>" class="check"/></td>
                            <?php endif; ?>
                        </tr>
                        <?php
                        $sum_invoice_amount[] = $data->invoice_amount;
                        $sum_discount_amount[] = $data->discount_amount;
                        $sum_invoice_paid[] = $data->invoice_paid;
                        $sum_adv_amount[] = $data->advance_amount;
                        $sum_balance_amount[] = $data->balance_amount;
                    endforeach;
                    ?>
                    <tr class="bg_gray">
                        <th colspan="4" class="text-right"><?php echo Yii::t("strings", "Total"); ?></th>
                        <th colspan="1" class="text-right"><?php echo AppHelper::getFloat(array_sum($sum_invoice_amount)); ?></th>
                        <th colspan="1" class="text-right"><?php echo AppHelper::getFloat(array_sum($sum_discount_amount)); ?></th>
                        <th colspan="1" class="text-right"><?php echo AppHelper::getFloat(array_sum($sum_invoice_paid)); ?></th>
                        <th colspan="1" class="text-right"><?php echo AppHelper::getFloat(array_sum($sum_adv_amount)); ?></th>
                        <th colspan="1" class="text-right"><?php echo AppHelper::getFloat(array_sum($sum_balance_amount)); ?></th>
                        <th colspan="1"></th>
                        <?php if ($this->hasUserAccess('payment_delete')): ?>
                            <th colspan="1"></th>
                        <?php endif; ?>
                    </tr>
                </table>
            </div>

            <div class="paging">
                <?php
                $this->widget('CLinkPager', array(
                    'pages' => $pages,
                    'header' => ' ',
                    'firstPageLabel' => '<<',
                    'lastPageLabel' => '>>',
                    'nextPageLabel' => '> ',
                    'prevPageLabel' => '< ',
                    'selectedPageCssClass' => 'active ',
                    'hiddenPageCssClass' => 'disabled ',
                    'maxButtonCount' => 5,
                    'htmlOptions' => array(
                        'class' => 'pagination',
                    )
                ));
                ?>
            </div>
        <?php else: ?>
            <div class="alert alert-info"><?php echo Yii::t('strings', 'No records found!'); ?></div>
        <?php endif; ?>
    </div>
</form>
<script type="text/javascript">
    $(document).ready(function() {
        $("#from_date, #to_date").datepicker({
            format: 'dd-mm-yyyy'
        });
    });
</script>