<?php
$this->breadcrumbs = array(
    $this->module->id => array(AppUrl::URL_LEDGER),
    'Heads',
);
?>
<div class="well">
    <table width="100%">
        <tr>
            <td class="wmd_70">
                <form action="" class="search-form" method="post" name="frmSearch" id="frmSearch">
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
                                <select id="itemType" class="form-control" name="itemType" style="">
                                    <option value="0">All</option>
                                    <option value="1">Fixed</option>
                                </select>
                            </div>
                            <button type="submit" id="search" class="btn btn-info"><?php echo Yii::t("strings", "Search"); ?></button>
                        </div>
                    </div>
                </form>
            </td>
            <td class="text-right wmd_30" style="position: relative;">
                <?php if ($this->hasUserAccess('head_create')): ?>
                    <a class="btn btn-success btn-xs" href="<?php echo Yii::app()->createUrl(AppUrl::URL_LEDGER_HEAD_CREATE); ?>"><i class="fa fa-plus"></i>&nbsp;<?php echo Yii::t("strings", "New"); ?></a>
                <?php endif; ?>
                <?php if ($this->hasUserAccess('head_delete')): ?>
                    <button type="button" class="btn btn-danger btn-xs" id="admin_del_btn" disabled="disabled"><i class="fa fa-trash-o"></i>&nbsp;<?php echo Yii::t("strings", "Delete"); ?></button>
                <?php endif; ?>
                <button type="button" class="btn btn-primary btn-xs" onclick="printDiv('printDiv')"><i class="fa fa-print"></i>&nbsp;<?php echo Yii::t("strings", "Print"); ?></button>
            </td>
        </tr>
    </table>
</div>
<form id="deleteForm" action="" method="post">
    <div id="printDiv">
        <div class="form-group clearfix text-center txt_left_xs mp_center media_print show_in_print mp_mt">
            <?php if (!empty($this->settings->title)): ?>
                <h1 style="font-size:20px;margin:0;"><?php echo $this->settings->title; ?></h1>
            <?php endif; ?>
            <?php if (!empty($this->settings->author_address)): ?>
                <?php echo $this->settings->author_address; ?><br>
            <?php endif; ?>
            <h3 class="inv_title" style="font-size:17px;"><u><?php echo Yii::t("strings", "Debit And Credit By Head"); ?></u></h3>
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
                        <tr class="bg_gray">
                            <th class="text-center" style="width:5%;"><?php echo Yii::t('strings', 'SL#'); ?></th>
                            <th><?php echo Yii::t('strings', 'Name'); ?></th>
                            <th class="text-right" style="width:10%;"><?php echo Yii::t('strings', 'Debit'); ?></th>
                            <th class="text-right" style="width:10%;"><?php echo Yii::t('strings', 'Credit'); ?></th>
                            <th class="text-right" style="width:10%;"><?php echo Yii::t('strings', 'Balance'); ?></th>
                            <th class="text-center dis_print" style="width:8%;"><?php echo Yii::t('strings', 'Actions'); ?></th>
                            <?php if ($this->hasUserAccess('head_delete')): ?>
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
                            $_debit = CashAccount::model()->sumDebit($data->id);
                            $_credit = CashAccount::model()->sumCredit($data->id);
                            $_balance = CashAccount::model()->sumBalance($data->id);
                            ?>
                            <tr class="pro_cat pro_cat_">
                                <td class="text-center"><?php echo $counter; ?></td>
                                <td>
                                    <span class="dis_print">
                                        <a href="<?php echo Yii::app()->createUrl(AppUrl::URL_LEDGER_HEAD_VIEW, array('id' => $data->id)); ?>" title="View All Transactions"><?php echo $data->name; ?></a>
                                    </span>
                                    <span class="show_in_print"><?php echo $data->name; ?></span>
                                </td>
                                <td class="text-right"><?php echo $_debit; ?></td>
                                <td class="text-right"><?php echo $_credit; ?></td>
                                <td class="text-right"><?php echo $_balance; ?></td>
                                <td class="text-center dis_print">
                                    <?php if ($this->hasUserAccess('head_edit')): ?>
                                        <a class="btn btn-info btn-xs" href="<?php echo $this->createUrl(AppUrl::URL_LEDGER_HEAD_EDIT, array('id' => $data->_key)); ?>"><?php echo Yii::t('strings', 'Edit'); ?></a>
                                    <?php endif; ?>
                                </td>
                                <?php if ($this->hasUserAccess('head_delete')): ?>
                                    <td class="text-center dis_print">
                                        <?php if ($data->is_fixed == 0): ?>
                                            <input type="checkbox" name="data[]" value="<?php echo $data->id; ?>" class="check">
                                        <?php endif; ?>
                                    </td>
                                <?php endif; ?>
                            </tr>
                            <?php
                            $sum_debit[] = $_debit;
                            $sum_credit[] = $_credit;
                            $sum_balance[] = $_balance;
                        endforeach;
                        ?>
                        <tr class="bg_gray">
                            <th class="text-right" colspan="2">Total</th>
                            <th class="text-right"><?php echo AppHelper::getFloat(array_sum($sum_debit)); ?></th>
                            <th class="text-right"><?php echo AppHelper::getFloat(array_sum($sum_credit)); ?></th>
                            <th class="text-right"><?php echo AppHelper::getFloat(array_sum($sum_balance)); ?></th>
                            <th class="text-center dis_print"></th>
                            <?php if ($this->hasUserAccess('head_delete')): ?>
                                <th class="text-center dis_print"></th>
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
<div id="container_for_detail" class="modal fade" tabindex="-1" role="dialog"></div>
<script type="text/javascript">
    $(document).ready(function() {
        $(document).on('click', '#search', function(e) {
            showLoader("Processing...", true);
            var _form = $("#frmSearch");

            $.ajax({
                type: "POST",
                url: ledgerUrl + "/head/search",
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
                var _url = ledgerUrl + '/head/deleteall';

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