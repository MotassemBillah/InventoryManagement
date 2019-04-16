<?php
$this->breadcrumbs = array(
    $this->module->id => array(AppUrl::URL_LEDGER),
    'Balancesheet',
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
                            <?php if (!UserIdentity::isDeletable() || $this->hasUserAccess('balance_sheet_update')): ?>
                                <a class="btn btn-primary" href="<?php echo Yii::app()->createUrl(AppUrl::URL_LEDGER_BALANCE_SHEET_UPDATE); ?>"><?php echo Yii::t("strings", "Update"); ?></a>
                            <?php endif; ?>
                        </div>
                    </div>
                </form>
            </td>
        </tr>
    </table>
</div>
<form id="deleteForm" action="" method="post">
    <div id="ajaxContent">
        <?php if (!empty($dataset) && count($dataset) > 0) : ?>
            <div class="table-responsive">
                <table class="table table-striped table-bordered tbl_invoice_view">
                    <tr id="r_checkAll" class="bg_gray">
                        <th class="text-center" style="width:4%;"><?php echo Yii::t('strings', 'SL#'); ?></th>
                        <th><?php echo Yii::t('strings', 'Date'); ?></th>
                        <th class="text-right" style="width:12%;"><?php echo Yii::t('strings', 'Debit'); ?></th>
                        <th class="text-right" style="width:12%;"><?php echo Yii::t('strings', 'Credit'); ?></th>
                        <th class="text-right" style="width:12%;"><?php echo Yii::t('strings', 'Balance'); ?></th>
                    </tr>
                    <?php
                    $counter = 0;
                    if (isset($_GET['page']) && $_GET['page'] > 1) {
                        $counter = ($_GET['page'] - 1) * $pages->pageSize;
                    }
                    foreach ($dataset as $data):
                        $counter++;
                        $_debit = AppObject::balancesheetSumDebit($data->pay_date);
                        $_credit = AppObject::balancesheetSumCredit($data->pay_date);
                        $_balance = AppObject::balancesheetSumBalance($data->pay_date);
                        ?>
                        <tr class="">
                            <td class="text-center"><?php echo $counter; ?></td>
                            <td><?php echo date('j M Y', strtotime($data->pay_date)); ?></td>
                            <td class="text-right"><?php echo $_debit; ?></td>
                            <td class="text-right"><?php echo $_credit; ?></td>
                            <td class="text-right"><?php echo $_balance; ?></td>
                        </tr>
                        <?php
                        $sum_debit[] = $_debit;
                        $sum_credit[] = $_credit;
                        $sum_balance[] = $_balance;
                    endforeach;
                    ?>
                    <tr class="bg_gray">
                        <th colspan="2" class="text-right"><?php echo Yii::t("strings", "Total Amount"); ?></th>
                        <th colspan="1" class="text-right"><?php echo AppHelper::getFloat(array_sum($sum_debit)); ?></th>
                        <th colspan="1" class="text-right"><?php echo AppHelper::getFloat(array_sum($sum_credit)); ?></th>
                        <th colspan="1" class="text-right"><?php echo AppHelper::getFloat(array_sum($sum_balance)); ?></th>
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
        <?php else: ?>
            <div class="alert alert-info"><?php echo Yii::t("strings", "No records found!"); ?></div>
        <?php endif; ?>
    </div>
</form>
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
                url: baseUrl + "/ledger/balancesheet/search",
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