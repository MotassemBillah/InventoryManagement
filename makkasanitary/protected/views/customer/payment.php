<?php
$this->breadcrumbs = array(
    'Customer' => array(AppUrl::URL_CUSTOMER),
    'Payment'
);
$balanceAmount = AppObject::sumBalanceAmount($customer->id);
if ($balanceAmount > 0) {
    $_btnCls = " btn-success ";
} elseif ($balanceAmount < 0) {
    $_btnCls = " btn-danger ";
} else {
    $_btnCls = " btn-info ";
}
?>
<div class="clearfix">
    <div class="form-group">
        <strong><u><?php echo Yii::t('strings', 'Balance Amount'); ?></u>&nbsp;:</strong>&nbsp;<button type="button" class="btn<?php echo $_btnCls; ?>btn-xs"><?php echo AppHelper::getFloat($balanceAmount); ?>&nbsp;Tk</button>
    </div>
</div>
<div class="well">
    <table width="100%">
        <tr>
            <td class="wmd_70">
                <form class="search-form" method="post" name="frmSearch" id="frmSearch">
                    <input type="hidden" name="customerID" value="<?php echo $customer->id; ?>">
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
                            <input type="number" id="q" name="invoice" class="form-control" placeholder="invoice" min="0" style="max-width: 170px;">
                            <button type="button" id="search" class="btn btn-info"><?php echo Yii::t("strings", "Search"); ?></button>
                            <button type="button" id="clear_from" class="btn btn-primary" data-info="/customer/payment">Clear</button>
                        </div>
                    </div>
                </form>
            </td>
            <td class="text-right wmd_30" style="">
                <a class="btn btn-success btn-xs" href="<?php echo Yii::app()->createUrl(AppUrl::URL_CUSTOMER_PAYMENT_CREATE, array('id' => $customer->_key)); ?>"><i class="fa fa-plus"></i>&nbsp;<?php echo Yii::t("strings", "New"); ?></a>
                <?php if ($this->hasUserAccess('customer_payment_delete')): ?>
                    <button type="button" class="btn btn-danger btn-xs" id="admin_del_btn" disabled="disabled" ><i class="fa fa-trash-o"></i>&nbsp;<?php echo Yii::t("strings", "Delete"); ?></button>
                <?php endif; ?>
                <button type="button" class="btn btn-primary btn-xs" onclick="printDiv('printDiv')"><i class="fa fa-print"></i>&nbsp;<?php echo Yii::t("strings", "Print"); ?></button>
            </td>
        </tr>
    </table>
</div>
<form id="deleteForm" action="#" method="post">
    <div id="printDiv">
        <div class="form-group clearfix text-center txt_left_xs mp_center media_print show_in_print mp_mt">
            <?php if (!empty($this->settings->title)): ?>
                <h1 style="font-size: 30px;margin: 0;"><?php echo $this->settings->title; ?></h1>
            <?php endif; ?>
            <?php if (!empty($this->settings->author_address)): ?>
                <?php echo $this->settings->author_address; ?><br>
            <?php endif; ?>
            <?php
            if (!empty($this->settings->other_contacts)) {
                $other_contacts = explode(",", $this->settings->other_contacts);
                $_str = "<b>Cell</b> : ";
                foreach ($other_contacts as $_k => $_v) {
                    trim($other_contacts[$_k]);
                    $_str .= $_v . " | ";
                }
                echo $_str;
            }
            if (!empty($this->settings->author_mobile)):
                echo $this->settings->author_mobile . " | ";
            endif;
            if (!empty($this->settings->author_phone)):
                echo "<b>Tel</b> : " . $this->settings->author_phone . " | ";
            endif;
            if (!empty($this->settings->author_email)):
                echo "<b>E-mail</b> : " . $this->settings->author_email;
            endif;
            ?>
            <h3 class="inv_title fs_20" style="margin-bottom:5px;"><u><?php echo Yii::t("strings", "Payment Summary Of - ") . $customer->name; ?></u></h3>
            <span><b>Date/Time : </b><?php echo date("d/m/y H:i G"); ?></span>
        </div>
        <div id="ajaxContent">
            <?php if (!empty($dataset) && count($dataset) > 0) : ?>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered tbl_invoice_view">
                        <tr id="r_checkAll">
                            <th class="text-center" style="width:4%;"><?php echo Yii::t('strings', 'SL#'); ?></th>
                            <th><?php echo Yii::t('strings', 'Pay Date'); ?></th>
                            <th><?php echo Yii::t('strings', 'Method'); ?></th>
                            <th class=""><?php echo Yii::t('strings', 'Invoice No'); ?></th>
                            <th class="text-right"><?php echo Yii::t('strings', 'Invoice Bill'); ?></th>
                            <th class="text-right"><?php echo Yii::t('strings', 'Discount'); ?></th>
                            <th class="text-right"><?php echo Yii::t('strings', 'Invoice Paid'); ?></th>
                            <th class="text-right"><?php echo Yii::t('strings', 'Due Collection'); ?></th>
                            <th class="text-right"><?php echo Yii::t('strings', 'Balance'); ?></th>
                            <th class="text-center dis_print"><?php echo Yii::t('strings', 'Actions'); ?></th>
                            <?php if ($this->hasUserAccess('customer_payment_delete')): ?>
                                <th class="text-center dis_print" style="width:3%;"><input type="checkbox" id="checkAll" onclick="toggleCheckboxes(this)"></th>
                            <?php endif; ?>
                        </tr>
                        <?php
                        $counter = 0;
                        if (isset($_GET['page']) && $_GET['page'] > 1) {
                            $counter = ($_GET['page'] - 1) * $pages->pageSize;
                        }
                        foreach ($dataset as $data):
                            $counter++;
                            ?>
                            <tr class="pro_cat pro_cat_<?php echo $data->type; ?>">
                                <td class="text-center"><?php echo $counter; ?></td>
                                <td><?php echo date('j M Y', strtotime($data->pay_date)); ?></td>
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
                                <td class="text-capitalize">
                                    <?php if (empty($data->invoice_no)): ?>
                                        <?php if ($data->type == AppConstant::TYPE_ADVANCE): ?>
                                            <span style="color:gray"><?php echo $data->type; ?></span>
                                        <?php else: ?>
                                            <span style="color:gray"><?php echo $data->type; ?></span>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <?php echo $data->invoice_no; ?>
                                    <?php endif; ?>
                                </td>
                                <td class="text-right"><?php echo AppHelper::getFloat($data->invoice_amount); ?></td>
                                <td class="text-right"><?php echo AppHelper::getFloat($data->discount_amount); ?></td>
                                <td class="text-right"><?php echo AppHelper::getFloat($data->invoice_paid); ?></td>
                                <td class="text-right"><?php echo AppHelper::getFloat($data->advance_amount); ?></td>
                                <td class="text-right"><?php echo AppHelper::getFloat($data->balance_amount); ?></td>
                                <td class="text-center dis_print">
                                    <?php if (empty($data->invoice_amount)): ?>
                                        <?php if ($this->hasUserAccess('customer_payment_edit')): ?>
                                            <a class="btn btn-info btn-xs" href="<?php echo $this->createUrl(AppUrl::URL_CUSTOMER_PAYMENT_EDIT, array('id' => $data->_key)); ?>"><?php echo Yii::t('strings', 'Edit'); ?></a>
                                        <?php endif; ?>
                                        <button type="button" class="btn btn-primary btn-xs btn_print" data-info="<?php echo $data->_key; ?>"><?php echo Yii::t("strings", "View"); ?></button>
                                    <?php else: ?>
                                        <a class="btn btn-primary btn-xs" href="<?php echo $this->createUrl(AppUrl::URL_SALE_VIEW, array('id' => $data->invoice_no)); ?>" target="_blank"><?php echo Yii::t('strings', 'View'); ?></a>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center dis_print">
                                    <?php if (empty($data->invoice_amount)): ?>
                                        <?php if ($this->hasUserAccess('customer_payment_delete')): ?>
                                            <input type="checkbox" name="data[]" value="<?php echo $data->id; ?>" class="check">
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </td>
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
                            <th class="dis_print" colspan="2"></th>
                        </tr>
                    </table>
                </div>

                <div class="paging dis_print">
                    <?php
                    $this->widget('CLinkPager', array(
                        'pages' => $pages,
                        'header' => ' ',
                        'firstPageLabel' => '<<',
                        'lastPageLabel' => '>>',
                        'nextPageLabel' => '> ',
                        'prevPageLabel' => '<',
                        'selectedPageCssClass' => 'active ',
                        'hiddenPageCssClass' => 'disabled ',
                        'maxButtonCount' => 10,
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
    </div>
</form>
<div class="modal fade" id="containerForPaymentInfo" tabindex="-1" role="dialog" aria-labelledby="containerForPaymentInfoLabel"></div>
<script type="text/javascript">
    $(document).ready(function() {
        $(document).on("click", ".btn_print", function(e) {
            showLoader("Processing...", true);
            var _id = $(this).attr("data-info");
            var _url = ajaxUrl + '/sales/payment_view?id=' + _id;

            $("#containerForPaymentInfo").load(_url, function() {
                $("#containerForPaymentInfo").modal({backdrop: 'static', keyboard: false
                });
                showLoader("", false);
            });
            e.preventDefault();
        });
    });
</script>