<?php
$this->breadcrumbs = array(
    'Cash Account'
);
?>
<div class="well">
    <table width="100%">
        <tr>
            <td class="wmd_70 wxs_100">
                <form class="search-form" method="post" name="frmSearch" id="frmSearch">
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
                            <div class="col-md-2 col-sm-3 no_pad" style="width: 12%;">
                                <select id="type" class="form-control" name="type">
                                    <option value="All">All</option>
                                    <option value="D">Debit</option>
                                    <option value="W">Credit</option>
                                </select>
                            </div>
                            <div class="col-md-2 col-sm-3 no_pad" style="width: 15%;">
                                <?php
                                $headList = LedgerHead::model()->getList();
                                $hlist = CHtml::listData($headList, 'id', 'name', 'type');
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
                            <input type="text" name="q" id="q" class="form-control" placeholder="search by name" size="30" style="width:15%;">
                            <button type="button" id="search" class="btn btn-info"><?php echo Yii::t("strings", "Search"); ?></button>
                            <button type="button" id="clear_from" class="btn btn-primary" data-info="/account/cash"><?php echo Yii::t("strings", "Clear"); ?></button>
                            <div class="col-md-2 col-sm-3 no_pad">
                                <select id="sortBy" class="form-control" name="sort_by">
                                    <option value="">Sort By</option>
                                    <option value="created" style="text-transform:capitalize">Date</option>
                                    <option value="purpose" style="text-transform:capitalize">Purpose</option>
                                    <option value="by_whom" style="text-transform:capitalize">By whom</option>
                                    <option value="debit" style="text-transform:capitalize">Debit</option>
                                    <option value="credit" style="text-transform:capitalize">Credit</option>
                                </select>
                            </div>
                            <div class="col-md-2 col-sm-3 no_pad">
                                <select id="sortType" class="form-control" name="sort_type">
                                    <option value="ASC">Ascending</option>
                                    <option value="DESC">Descending</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </form>
            </td>
            <td class="text-right wmd_30" style="position: relative;">
                <?php if ($this->hasUserAccess('cash_deposit')): ?>
                    <a href="<?php echo Yii::app()->createUrl(AppUrl::URL_CASH_ACCOUNT_DEPOSIT); ?>"><button type="button" class="btn btn-success btn-xs"><i class="fa fa-plus"></i>&nbsp;<?php echo Yii::t("strings", "Deposit"); ?></button></a>
                <?php endif; ?>
                <?php if ($this->hasUserAccess('cash_withdraw')): ?>
                    <a href="<?php echo Yii::app()->createUrl(AppUrl::URL_CASH_ACCOUNT_WITHDRAW); ?>"><button type="button" class="btn btn-warning btn-xs"><i class="fa fa-minus"></i>&nbsp;<?php echo Yii::t("strings", "Withdraw"); ?></button></a>
                <?php endif; ?>
                <br>
                <button type="button" class="btn btn-primary btn-xs" onclick="printDiv('printDiv')"><i class="fa fa-print"></i>&nbsp;<?php echo Yii::t("strings", "Print"); ?></button>
                <?php if ($this->hasUserAccess('cash_account_delete')): ?>
                    <button type="button" class="btn btn-danger btn-xs" id="admin_del_btn" disabled="disabled"><i class="fa fa-trash-o"></i>&nbsp;<?php echo Yii::t("strings", "Delete"); ?></button>
                <?php endif; ?>
            </td>
        </tr>
    </table>
</div>
<form id="deleteForm" action="" method="post">
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
            <h3 class="inv_title" style="font-size:18px;"><u><?php echo Yii::t("strings", "Cash Account Statement"); ?></u></h3>
        </div>
        <table class="table table-striped table-bordered tbl_invoice_view" style="margin: 0;">
            <tr>
                <td><strong><?php echo Yii::t("strings", "Debit"); ?></strong>:&nbsp;<?php echo CashAccount::model()->sumDebit(); ?></td>
                <td><strong><?php echo Yii::t("strings", "Credit"); ?></strong>:&nbsp;<?php echo CashAccount::model()->sumCredit(); ?></td>
                <td><strong><?php echo Yii::t("strings", "Balance"); ?></strong>:&nbsp;<?php echo CashAccount::model()->sumBalance(); ?></td>
            </tr>
        </table>
        <div id="ajaxContent">
            <?php if (!empty($dataset) && count($dataset) > 0) : ?>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered tbl_invoice_view">
                        <tr class="bg_gray" id="r_checkAll">
                            <th class="text-center" style="width:3%;"><?php echo Yii::t("strings", "SL#"); ?></th>
                            <th style="width:12%;"><?php echo Yii::t("strings", "Date"); ?></th>
                            <th style="width:10%;"><?php echo Yii::t("strings", "Head"); ?></th>
                            <th style="width:10%;"><?php echo Yii::t("strings", "By Whom"); ?></th>
                            <th><?php echo Yii::t("strings", "Purpose"); ?></th>
                            <th class="text-right" style="width:10%;"><?php echo Yii::t("strings", "Debit"); ?></th>
                            <th class="text-right" style="width:10%;"><?php echo Yii::t("strings", "Credit"); ?></th>
                            <th class="text-right" style="width:10%;"><?php echo Yii::t("strings", "Balance"); ?></th>
                            <th class="text-center dis_print" style="width:8%;"><?php echo Yii::t("strings", "Actions"); ?></th>
                            <th class="text-center dis_print" style="width:3%;">
                                <?php if ($this->hasUserAccess('cash_account_delete')): ?>
                                    <input type="checkbox" id="checkAll" onclick="toggleCheckboxes(this)">
                                <?php endif; ?>
                            </th>
                        </tr>
                        <?php
                        $counter = 0;
                        if (isset($_GET['page']) && $_GET['page'] > 1) {
                            $counter = ($_GET['page'] - 1) * $pages->pageSize;
                        }
                        foreach ($dataset as $data):
                            $counter++;
                            $_head = !empty($data->ledger_head_id) ? LedgerHead::model()->findByPk($data->ledger_head_id) : '';
                            ?>
                            <tr>
                                <td class="text-center"><?php echo $counter; ?></td>
                                <td><?php echo date("j M Y", strtotime($data->created)); ?></td>
                                <td><?php echo!empty($_head) ? $_head->name : ''; ?></td>
                                <td><?php echo $data->by_whom; ?></td>
                                <td><?php echo $data->purpose; ?></td>
                                <td class="text-right"><?php echo AppHelper::getFloat($data->debit); ?></td>
                                <td class="text-right"><?php echo AppHelper::getFloat($data->credit); ?></td>
                                <td class="text-right"><?php echo AppHelper::getFloat($data->balance); ?></td>
                                <td class="text-center dis_print">
                                    <?php if ($this->hasUserAccess('cash_account_edit')): ?>
                                        <?php if ($data->is_editable == 1): ?>
                                            <?php
                                            if ($data->type == 'D') {
                                                $_link = Yii::app()->createUrl(AppUrl::URL_CASH_ACCOUNT_DEPOSIT_EDIT, array('id' => $data->_key));
                                            } else {
                                                $_link = Yii::app()->createUrl(AppUrl::URL_CASH_ACCOUNT_WITHDRAW_EDIT, array('id' => $data->_key));
                                            }
                                            ?>
                                            <a class="btn btn-info btn-xs" href="<?php echo $_link; ?>"><?php echo Yii::t('strings', 'Edit'); ?></a>
                                            <a class="btn btn-primary btn-xs" href="<?php echo Yii::app()->createUrl(AppUrl::URL_CASH_ACCOUNT_VOUCHER, array('id' => $data->_key)); ?>"><?php echo Yii::t('strings', 'View'); ?></a>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center dis_print">
                                    <?php if ($this->hasUserAccess('cash_account_delete')): ?>
                                        <?php if ($data->is_editable == 1): ?>
                                            <input type="checkbox" name="data[]" value="<?php echo $data->id; ?>" class="check">
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php
                            $sum_dbt[] = $data->debit;
                            $sum_cdt[] = $data->credit;
                            $sum_blance[] = $data->balance;
                        endforeach;
                        ?>
                        <tr class="bg_gray">
                            <th class="text-right" colspan="5"><?php echo Yii::t("strings", "Total"); ?></th>
                            <th class="text-right"><?php echo AppHelper::getFloat(array_sum($sum_dbt)); ?></th>
                            <th class="text-right"><?php echo AppHelper::getFloat(array_sum($sum_cdt)); ?></th>
                            <th class="text-right"><?php echo AppHelper::getFloat(array_sum($sum_blance)); ?></th>
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
                url: baseUrl + "/cash_account/search",
                data: _form.serialize(),
                success: function(res) {
                    showLoader("", false);
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
                var _url = baseUrl + '/cash_account/deleteall';

                $.post(_url, _form.serialize(), function(res) {
                    if (res.success === true) {
                        $("#ajaxMessage").showAjaxMessage({html: res.message, type: 'success'});
                        $("tr.bg-danger").remove();
                        $("#clear_from").trigger('click');
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