<?php
$this->breadcrumbs = array(
    'Account' => array(AppUrl::URL_ACCOUNT),
    'Balance'
);
?>
<div class="row clearfix" style="border-bottom:1px solid #cccccc;margin:0 -15px 10px -15px;padding-bottom:7px;">
    <div class="col-md-4 xs_txt_left">
        <strong><?php echo Yii::t("strings", "Bank Name"); ?></strong>:&nbsp;<?php echo AppObject::getBankName($account->bank_id); ?>
    </div>
    <div class="col-md-4 text-center xs_txt_left">
        <strong><?php echo Yii::t("strings", "Account Name"); ?></strong>:&nbsp;<?php echo $account->account_name; ?>
    </div>
    <div class="col-md-4 text-right xs_txt_left">
        <strong><?php echo Yii::t("strings", "Account Number"); ?></strong>:&nbsp;<?php echo $account->account_number; ?>
    </div>
</div>
<div class="well">
    <table width="100%">
        <tr>
            <td class="wmd_70 wxs_100">
                <form class="search-form" method="post" name="frmSearch" id="frmSearch">
                    <input type="hidden" name="accountID" value="<?php echo $account->id; ?>">
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
                            <select id="type" name="type" class="form-control" style="width:auto;">
                                <option value="">All</option>
                                <option value="<?php echo AppConstant::CASH_IN; ?>"><?php echo AppConstant::CASH_IN; ?></option>
                                <option value="<?php echo AppConstant::CASH_OUT; ?>"><?php echo AppConstant::CASH_OUT; ?></option>
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
                            <button type="button" id="search" class="btn btn-info"><?php echo Yii::t("strings", "Search"); ?></button>
                            <button type="button" id="clear_from" class="btn btn-primary"><?php echo Yii::t("strings", "Clear"); ?></button>
                        </div>
                    </div>
                </form>
            </td>
        </tr>
    </table>
</div>
<form id="deleteForm" action="" method="post">
    <table class="table table-bordered tbl_invoice_view" style="margin:0">
        <tr>
            <td><strong>Debit : </strong> <?php echo AccountBalance::model()->sumDebit($account->id); ?>&nbsp;Tk</td>
            <td><strong>Credit : </strong> <?php echo AccountBalance::model()->sumCredit($account->id); ?>&nbsp;Tk</td>
            <td><strong>Balance : </strong> <?php echo AccountBalance::model()->sumBalance($account->id); ?>&nbsp;Tk</td>
        </tr>
    </table>
    <div id="ajaxContent">
        <?php if (!empty($dataset) && count($dataset) > 0) : ?>
            <div class="table-responsive">
                <table class="table table-striped table-bordered tbl_invoice_view">
                    <tr id="r_checkAll">
                        <th class="text-center" style="width:4%;"><?php echo Yii::t("strings", "SL#"); ?></th>
                        <th><?php echo Yii::t("strings", "Date"); ?></th>
                        <th><?php echo Yii::t("strings", "Purpose"); ?></th>
                        <th><?php echo Yii::t("strings", "Person"); ?></th>
                        <th class="text-right" style="width:10%;"><?php echo Yii::t("strings", "Debit"); ?></th>
                        <th class="text-right" style="width:10%;"><?php echo Yii::t("strings", "Credit"); ?></th>
                    </tr>
                    <?php
                    $counter = 0;
                    foreach ($dataset as $data):
                        $counter++;
                        ?>
                        <tr>
                            <td class="text-center"><?php echo $counter; ?></td>
                            <td><?php echo date("d-m-Y", strtotime($data->created)); ?></td>
                            <td><?php echo AppHelper::getCleanValue($data->purpose); ?></td>
                            <td><?php echo AppHelper::getCleanValue($data->by_whom); ?></td>
                            <td class="text-right"><?php echo AppHelper::getFloat($data->debit); ?></td>
                            <td class="text-right"><?php echo AppHelper::getFloat($data->credit); ?></td>
                        </tr>
                        <?php
                        $sumDebt[] = $data->debit;
                        $sumCrdt[] = $data->credit;
                    endforeach;
                    ?>
                    <tr>
                        <th colspan="4" class="text-right"><?php echo Yii::t("strings", "Total"); ?></th>
                        <th class="text-right"><?php echo AppHelper::getFloat(array_sum($sumDebt)); ?></th>
                        <th class="text-right"><?php echo AppHelper::getFloat(array_sum($sumCrdt)); ?></th>
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
                    'maxButtonCount' => 10,
                    'htmlOptions' => array(
                        'class' => 'pagination',
                    )
                ));
                ?>
            </div>
        <?php else: ?>
            <div class="alert alert-info">No records found!</div>
        <?php endif; ?>
    </div>
</form>
<div class="modal fade" id="containerForDetailInfo" tabindex="-1" role="dialog" aria-labelledby="containerForDetailInfoLabel"></div>
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
                url: baseUrl + "/account/search_balance",
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