<?php
$this->breadcrumbs = array(
    $this->module->id => array(AppUrl::URL_LEDGER),
    'Finance Statement'
);
?>
<div class="well">
    <form class="search-form" method="post" name="frmSearch" id="frmSearch">
        <table width="100%">
            <tr>
                <td class="wmd_70">
                    <div class="input-group">
                        <div class="input-group-btn clearfix">
                            <div class="col-md-2 col-sm-3 no_pad">
                                <div class="input-group xsw_100">
                                    <input type="text" id="from_date" class="form-control" name="from_date" placeholder="(dd-mm-yyyy)" required readonly>
                                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                                </div>
                            </div>
                            <div class="col-md-1 col-sm-1 text-center" style="font-size:14px;width:5%;">
                                <b style="color: rgb(0, 0, 0); vertical-align: middle; display: block; padding: 6px 0px;">TO</b>
                            </div>
                            <div class="col-md-2 col-sm-3 no_pad">
                                <div class="input-group xsw_100">
                                    <input type="text" id="to_date" class="form-control" name="to_date" placeholder="(dd-mm-yyyy)" required readonly>
                                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                                </div>
                            </div>
                            <button type="button" id="search" class="btn btn-info"><?php echo Yii::t("strings", "Search"); ?></button>
                        </div>
                    </div>
                </td>
                <td class="text-right wmd_30">
                    <button type="button" class="btn btn-primary btn-xs" onclick="printDiv('printDiv')"><i class="fa fa-print"></i>&nbsp;<?php echo Yii::t("strings", "Print"); ?></button>
                </td>
            </tr>
        </table>
    </form>
</div>
<?php $_openingBalance = 0; ?>
<div id="printDiv">
    <div id="ajaxContent">
        <div class="form-group clearfix text-center mp_center media_print show_in_print mp_mt">
            <?php if (!empty($this->settings->title)): ?>
                <h1 style="font-size:20px;font-weight:600;margin:0;"><?php echo $this->settings->title; ?></h1>
            <?php endif; ?>
            <?php if (!empty($this->settings->author_address)): ?>
                <?php echo $this->settings->author_address; ?><br>
            <?php endif; ?>
            <h4 class="inv_title" style="font-size:16px;font-weight:600;"><u><?php echo Yii::t("strings", "Financial Statement"); ?></u></h4>
            <h4 class="inv_title" style="font-size:16px;font-weight:600;">
                From : <u style="font-weight:normal"><?php echo!empty($dataset) ? date("d-m-Y", strtotime($dataset[0]->created)) : ''; ?></u>
                To : <u style="font-weight:normal"><?php echo!empty($dataset) ? date("d-m-Y", strtotime($dataset[count($dataset) - 1]->created)) : ''; ?></u>
            </h4>
        </div>
        <table class="table table-bordered tbl_invoice_view" style="margin: 0;">
            <tr>
                <th>Opening Balance : <?php echo $_openingBalance; ?></th>
            </tr>
        </table>
        <div class="clearfix">
            <div class="col-md-6 mpw_50" style="padding-left:0;">
                <table class="table table-striped table-bordered tbl_invoice_view" style="margin:0;">
                    <tr>
                        <th class="text-center" colspan="3">Debit</th>
                    </tr>
                    <?php if (!empty($debitDataset) && count($debitDataset) > 0) : ?>
                        <tr>
                            <th class="text-center" style="width:5%;"><?php echo Yii::t('strings', 'SL#'); ?></th>
                            <th><?php echo Yii::t('strings', 'Name'); ?></th>
                            <th class="text-right" style="width:10%;"><?php echo Yii::t('strings', 'Debit'); ?></th>
                        </tr>
                        <?php
                        $counter = 0;
                        foreach ($debitDataset as $data):
                            $_debit = CashAccount::model()->sumDebit($data->id);
                            if ($_debit != 0) {
                                $counter++;
                                ?>
                                <tr class="pro_cat pro_cat_">
                                    <td class="text-center"><?php echo $counter; ?></td>
                                    <td><?php echo $data->name; ?></td>
                                    <td class="text-right"><?php echo $_debit; ?></td>
                                </tr>
                                <?php
                            }
                            $sum_debit[] = $_debit;
                        endforeach;
                        ?>
                        <tr class="bg_gray">
                            <th class="text-right" colspan="2">Total</th>
                            <th class="text-right"><?php echo AppHelper::getFloat(array_sum($sum_debit)); ?></th>
                        </tr>
                    <?php else: ?>
                        <tr>
                            <th class="text-center" colspan="3">No Data Found.</th>
                        </tr>
                    <?php endif ?>
                </table>
            </div>
            <div class="col-md-6 pull-right mpw_50" style="padding-right:0;">
                <table class="table table-striped table-bordered tbl_invoice_view" style="margin: 0;">
                    <tr>
                        <th class="text-center" colspan="3">Credit</th>
                    </tr>
                    <?php if (!empty($creditDataset) && count($creditDataset) > 0) : ?>
                        <tr>
                            <th class="text-center" style="width:5%;"><?php echo Yii::t('strings', 'SL#'); ?></th>
                            <th><?php echo Yii::t('strings', 'Name'); ?></th>
                            <th class="text-right" style="width:10%;"><?php echo Yii::t('strings', 'Credit'); ?></th>
                        </tr>
                        <?php
                        $counter_cr = 0;
                        foreach ($creditDataset as $cdata):
                            $_credit = CashAccount::model()->sumCredit($cdata->id);
                            if ($_credit != 0) {
                                $counter_cr++;
                                ?>
                                <tr class="pro_cat pro_cat_">
                                    <td class="text-center"><?php echo $counter_cr; ?></td>
                                    <td><?php echo $cdata->name; ?></td>
                                    <td class="text-right"><?php echo $_credit; ?></td>
                                </tr>
                                <?php
                            }
                            $sum_credit[] = $_credit;
                        endforeach;
                        ?>
                        <tr class="bg_gray">
                            <th class="text-right" colspan="2">Total</th>
                            <th class="text-right"><?php echo AppHelper::getFloat(array_sum($sum_credit)); ?></th>
                        </tr>
                    <?php else: ?>
                        <tr>
                            <th class="text-center" colspan="3">No Data Found.</th>
                        </tr>
                    <?php endif ?>
                </table>
            </div>
        </div>
        <?php
        $_debitBalance = !empty($debitDataset) ? array_sum($sum_debit) : 0;
        $_creditBalance = !empty($creditDataset) ? array_sum($sum_credit) : 0;
        $_closingBalance = (($_openingBalance + $_debitBalance) - $_creditBalance);
        ?>
        <table class="table table-bordered tbl_invoice_view">
            <tr>
                <th>Closing Balance : <?php echo $_closingBalance; ?></th>
            </tr>
        </table>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        $("#from_date, #to_date").datepicker({
            format: 'dd-mm-yyyy'
        });

        $(document).on('click', '#search', function(e) {
            var _form = $("#frmSearch");

            if ($("#from_date").val() == '') {
                $("#ajaxMessage").showAjaxMessage({html: "From date required.", type: "error"});
                $("#from_date").focus();
                return false;
            } else if ($("#to_date").val() == '') {
                $("#ajaxMessage").showAjaxMessage({html: "To date required.", type: "error"});
                $("#to_date").focus();
                return false;
            } else {
                $("#ajaxMessage").hide();
                showLoader("Processing...", true);
                $.ajax({
                    type: "POST",
                    url: ledgerUrl + "/statement/search",
                    data: _form.serialize(),
                    success: function(res) {
                        showLoader("", false);
                        $("#ajaxContent").html('');
                        $("#ajaxContent").html(res);
                    }
                });
            }
            e.preventDefault();
        });
    });
</script>