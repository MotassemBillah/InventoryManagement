<?php
$this->breadcrumbs = array(
    $this->module->id => array(AppUrl::URL_LEDGER),
    'Expense',
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
                                $headList = LedgerHead::model()->getList();
                                $hlist = CHtml::listData($headList, 'id', 'name');
                                echo CHtml::dropdownList('ledger_head', 'ledger_head', $hlist, array('empty' => 'All Head', 'class' => 'form-control'));
                                ?>
                            </div>
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
                            <input type="text" id="search_purpose" name="search_purpose" class="form-control" placeholder="search purpose" size="25" style="width:20%;">
                            <button type="button" id="search" class="btn btn-info"><?= Yii::t("strings", "Search"); ?></button>
                            <button type="button" id="clear_from" class="btn btn-primary"><?= Yii::t("strings", "Clear"); ?></button>
                        </div>
                    </div>
                </form>
            </td>
            <td class="text-right wmd_30" style="position: relative;">
                <a href="<?php echo Yii::app()->createUrl(AppUrl::URL_LEDGER_EXPENSE_CREATE); ?>"><button type="button" class="btn btn-success btn-xs"><i class="fa fa-plus"></i>&nbsp;<?php echo Yii::t("strings", "New"); ?></button></a>
                <?php if ($this->hasUserAccess('expense_delete')): ?>
                    <button type="button" class="btn btn-danger btn-xs" id="admin_del_btn" disabled="disabled"><i class="fa fa-trash-o"></i>&nbsp;<?php echo Yii::t("strings", "Delete"); ?></button>
                <?php endif; ?>
                <button type="button" class="btn btn-primary btn-xs" onclick="printDiv('printDiv')"><i class="fa fa-print"></i>&nbsp;<?php echo Yii::t("strings", "Print"); ?></button>
            </td>
        </tr>
    </table>
</div>
<form id="deleteForm" action="" method="post">
    <div id="printDiv">
        <div class="clearfix text-center mp_center mb_10 show_in_print mp_mt">
            <?php if (!empty($this->settings->logo)) : ?>
                <img alt="" src="<?php echo Yii::app()->request->baseUrl . '/uploads/' . $this->settings->logo; ?>" style="max-height:50px;position:absolute;left:0;top:0;">
            <?php endif; ?>
            <?php if (!empty($this->settings->title)): ?>
                <h1 style="font-size:20px;margin:0;"><?php echo $this->settings->title; ?></h1>
            <?php endif; ?>
            <?php if (!empty($this->settings->author_address)): ?>
                <?php echo $this->settings->author_address; ?><br>
            <?php endif; ?>
            <h3 class="inv_title" style="font-size:17px;"><u><?php echo Yii::t("strings", "Daily Expenses"); ?></u></h3>
        </div>
        <table class="table table-striped table-bordered tbl_invoice_view" style="margin: 0;">
            <tr>
                <td>Total Expense = <?php echo Expense::model()->sumTotal(); ?> Tk</td>
            </tr>
        </table>
        <div id="ajaxContent">
            <?php if (!empty($dataset) && count($dataset) > 0) : ?>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered tbl_invoice_view">
                        <tr id="r_checkAll" class="bg_gray">
                            <th class="text-center" style="width:5%;"><?php echo Yii::t('strings', 'SL#'); ?></th>
                            <th><?php echo Yii::t('strings', 'Date'); ?></th>
                            <th><?php echo Yii::t('strings', 'Head'); ?></th>
                            <th><?php echo Yii::t('strings', 'Purpose'); ?></th>
                            <th><?php echo Yii::t('strings', 'By Whom'); ?></th>
                            <th class="text-right"><?php echo Yii::t('strings', 'Amount'); ?></th>
                            <th class="text-center dis_print"><?php echo Yii::t('strings', 'Actions'); ?></th>
                            <?php if ($this->hasUserAccess('expense_delete')): ?>
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
                            <tr class="pro_cat pro_cat_">
                                <td class="text-center"><?php echo $counter; ?></td>
                                <td><?php echo date('j M Y', strtotime($data->pay_date)); ?></td>
                                <td><?php echo!empty($data->ledger_head_id) ? LedgerHead::model()->findByPk($data->ledger_head_id)->name : ''; ?></td>
                                <td><?php echo $data->purpose; ?></td>
                                <td><?php echo $data->by_whom; ?></td>
                                <td class="text-right"><?php echo AppHelper::getFloat($data->amount); ?></td>
                                <td class="text-center dis_print">
                                    <?php if ($this->hasUserAccess('expense_edit')): ?>
                                        <a class="btn btn-info btn-xs" href="<?php echo $this->createUrl(AppUrl::URL_LEDGER_EXPENSE_EDIT, array('id' => $data->_key)); ?>"><?php echo Yii::t('strings', 'Edit'); ?></a>
                                    <?php endif; ?>
                                    <a class="btn btn-primary btn-xs" href="<?php echo $this->createUrl(AppUrl::URL_LEDGER_EXPENSE_VIEW, array('id' => $data->_key)); ?>"><?php echo Yii::t('strings', 'View'); ?></a>
                                </td>
                                <?php if ($this->hasUserAccess('expense_delete')): ?>
                                    <td class="text-center dis_print"><input type="checkbox" name="data[]" value="<?php echo $data->id; ?>" class="check"></td>
                                <?php endif; ?>
                            </tr>
                            <?php
                            $sum_balance_amount[] = $data->amount;
                        endforeach;
                        ?>
                        <tr class="bg_gray">
                            <th colspan="5" class="text-right"><?php echo Yii::t("strings", "Total Amount"); ?></th>
                            <th colspan="1" class="text-right"><?php echo AppHelper::getFloat(array_sum($sum_balance_amount)); ?></th>
                            <th class="text-center dis_print" colspan="1"></th>
                            <?php if ($this->hasUserAccess('expense_delete')): ?>
                                <th class="text-center dis_print" colspan="1"></th>
                            <?php endif; ?>
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
                url: ledgerUrl + "/expense/search",
                data: _form.serialize(),
                success: function(res) {
                    showLoader("", false);
                    $("#ajaxContent").html('');
                    $("#ajaxContent").html(res);
                }
            });
            e.preventDefault();
        });

        $(document).on("input", "input#q", function(e) {
            //showLoader("Processing...", true);
            var _form = $("#frmSearch");

            $.ajax({
                type: "POST",
                url: ledgerUrl + "/expense/search",
                data: _form.serialize(),
                success: function(res) {
                    //showLoader("", false);
                    $("#ajaxContent").html('');
                    $("#ajaxContent").html(res);
                }
            });
            e.preventDefault();
        });

        $(document).on('click', '#admin_del_btn', function(e) {
            var _rc = confirm('Are you sure about this action? This cannot be undone!');

            if (_rc === true) {
                showLoader("Processing...", true);
                var _form = $("#deleteForm");
                var _url = ledgerUrl + '/expense/deleteall';

                $.post(_url, _form.serialize(), function(res) {
                    if (res.success === true) {
                        $("#ajaxMessage").showAjaxMessage({html: res.message, type: 'success'});
                        $("tr.bg-danger").not('tr#r_checkAll').remove();
                        $("#search").trigger('click');
                    } else {
                        $("#ajaxMessage").showAjaxMessage({html: res.message, type: 'error'});
                    }
                    reset_index();
                    showLoader("", false);
                }, "json");
            } else {
                return false;
            }
            e.preventDefault();
        });
    });
</script>