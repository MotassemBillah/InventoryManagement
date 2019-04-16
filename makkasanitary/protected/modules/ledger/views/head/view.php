<?php
$this->breadcrumbs = array(
    $this->module->id => array(AppUrl::URL_LEDGER),
    'Transcation Detail',
);
?>
<div class="well">
    <table width="100%">
        <tr>
            <td class="wmd_70">
                <form class="search-form" method="post" name="frmSearch" id="frmSearch">
                    <input type="hidden" name="ledger_head_id" value="<?php echo $headID; ?>">
                    <div class="input-group">
                        <div class="input-group-btn clearfix">
                            <select id="itemCount" class="form-control" name="itemCount" style="width:55px;">
                                <?php
                                for ($i = 10; $i <= 500; $i+=10) {
                                    if ($i > 100)
                                        $i+=40;
                                    if ($i > 200)
                                        $i+=50;
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
                            <div class="col-md-1 col-sm-1 text-center" style="font-size:14px;width: 5%;">
                                <b style="color: rgb(0, 0, 0); vertical-align: middle; display: block; padding: 6px 0px;">TO</b>
                            </div>
                            <div class="col-md-2 col-sm-3 no_pad">
                                <div class="input-group xsw_100">
                                    <input type="text" id="to_date" class="form-control" name="to_date" placeholder="(dd-mm-yyyy)" readonly>
                                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                                </div>
                            </div>
                            <button type="button" id="search" class="btn btn-info"><?php echo Yii::t("strings", "Search"); ?></button>
                        </div>
                    </div>
                </form>
            </td>
            <td class="wmd_30 text-right">
                <button type="button" class="btn btn-primary btn-xs pull-right" onclick="printDiv('printDiv')"><i class="fa fa-print"></i>&nbsp;<?php echo Yii::t("strings", "Print"); ?></button>
            </td>
        </tr>
    </table>
</div>
<div id="printDiv">
    <div class="clearfix text-center txt_left_xs mp_center mb_10 show_in_print mp_mt">
        <?php if (!empty($this->settings->logo)) : ?>
            <img alt="" src="<?php echo Yii::app()->request->baseUrl . '/uploads/' . $this->settings->logo; ?>" style="max-height:50px;position:absolute;left:0;top:0;">
        <?php endif; ?>
        <?php if (!empty($this->settings->title)): ?>
            <h1 style="font-size:20px;margin:0;"><?php echo $this->settings->title; ?></h1>
        <?php endif; ?>
        <?php if (!empty($this->settings->author_address)): ?>
            <?php echo $this->settings->author_address; ?><br>
        <?php endif; ?>
        <h3 class="inv_title" style="font-size:17px;"><u><?php echo Yii::t("strings", "Transactions Details"); ?></u></h3>
    </div>
    <table class="table table-striped table-bordered tbl_invoice_view" style="margin:0;">
        <tr class="bg_gray">
            <th>
                <?php echo $headName; ?>&nbsp;
                <span style="margin-left: 15px;">[ Debit = <?php echo CashAccount::model()->sumDebit($headID); ?> ]</span>
                <span style="margin-left: 15px;">[ Credit = <?php echo CashAccount::model()->sumCredit($headID); ?> ]</span>
                <span style="margin-left: 15px;">[ Balance = <?php echo CashAccount::model()->sumBalance($headID); ?> ]</span>
            </th>
        </tr>
    </table>
    <div id="ajaxContent">
        <div class="table-responsive">
            <table class="table table-striped table-bordered tbl_invoice_view">
                <?php if (!empty($dataset) && count($dataset) > 0) : ?>
                    <tr class="bg_gray">
                        <th class="text-center" style="width:4%;"><?php echo Yii::t('strings', 'SL#'); ?></th>
                        <th style="width:12%;"><?php echo Yii::t('strings', 'Date'); ?></th>
                        <th><?php echo Yii::t('strings', 'Purpose'); ?></th>
                        <th style="width:15%;"><?php echo Yii::t('strings', 'By Whom'); ?></th>
                        <th><?php echo Yii::t('strings', 'Note'); ?></th>
                        <th class="text-right" style="width:10%;"><?php echo Yii::t('strings', 'Debit'); ?></th>
                        <th class="text-right" style="width:10%;"><?php echo Yii::t('strings', 'Credit'); ?></th>
                        <th class="text-right" style="width:10%;"><?php echo Yii::t('strings', 'Balance'); ?></th>
                    </tr>
                    <?php
                    $counter = 0;
                    if (isset($_GET['page']) && $_GET['page'] > 1) {
                        $counter = ($_GET['page'] - 1) * $pages->pageSize;
                    }
                    foreach ($dataset as $data):
                        $counter++;
                        $_debit = CashAccount::model()->sumDebit($data->id);
                        $_credit = CashAccount::model()->sumCredit($data->id);
                        $_balance = CashAccount::model()->sumBalance($data->id);
                        ?>
                        <tr>
                            <td class="text-center" style="width:5%;"><?php echo $counter; ?></td>
                            <td><?php echo date('j M Y', strtotime($data->created)); ?></td>
                            <td><?php echo $data->purpose; ?></td>
                            <td><?php echo $data->by_whom; ?></td>
                            <td>
                                <?php
                                echo!empty($data->bank_id) ? Bank::model()->findByPk($data->bank_id)->name . "<br>" : '';
                                echo!empty($data->account_id) ? Account::model()->findByPk($data->account_id)->name . "<br>" : '';
                                echo!empty($data->check_no) ? $data->check_no : '';
                                ?>
                            </td>
                            <td class="text-right" style="width:10%;"><?php echo AppHelper::getFloat($data->debit); ?></td>
                            <td class="text-right" style="width:10%;"><?php echo AppHelper::getFloat($data->credit); ?></td>
                            <td class="text-right" style="width:10%;"><?php echo AppHelper::getFloat($data->balance); ?></td>
                        </tr>
                        <?php
                        $sum_debit[] = $data->debit;
                        $sum_credit[] = $data->credit;
                        $sum_balance[] = $data->balance;
                    endforeach;
                    ?>
                    <tr class="bg_gray">
                        <th class="text-right" colspan="5">Total</th>
                        <th class="text-right"><?php echo AppHelper::getFloat(array_sum($sum_debit)); ?></th>
                        <th class="text-right"><?php echo AppHelper::getFloat(array_sum($sum_credit)); ?></th>
                        <th class="text-right"><?php echo AppHelper::getFloat(array_sum($sum_balance)); ?></th>
                    </tr>
                <?php else: ?>
                    <tr>
                        <td colspan="8"><?php echo Yii::t("strings", "No Transaction Found."); ?></td>
                    </tr>
                <?php endif; ?>
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
                'prevPageLabel' => '< ',
                'selectedPageCssClass' => 'active ',
                'hiddenPageCssClass' => 'disabled ',
                'maxButtonCount' => 10,
                'htmlOptions' => array(
                    'class' => 'pagination',
                )
            ));
            ?>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        $("#from_date, #to_date").datepicker({
            format: 'dd-mm-yyyy'
        });

        $(document).on("click", "#search", function(e) {
            showLoader("Processing...", true);
            var _form = $("#frmSearch");

            $.ajax({
                type: "POST",
                url: ledgerUrl + "/head/search_detail",
                data: _form.serialize(),
                success: function(res) {
                    showLoader("", false);
                    $("#ajaxContent").html('');
                    $("#ajaxContent").html(res);
                }
            });
            e.preventDefault();
        });
    });
</script>