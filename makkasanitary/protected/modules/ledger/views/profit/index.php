<?php
$this->breadcrumbs = array(
    $this->module->id => array(AppUrl::URL_LEDGER),
    'Profit'
);
?>
<div class="well">
    <form class="search-form" method="post" name="frmSearch" id="frmSearch">
        <table width="100%">
            <tr>
                <td class="wmd_70">
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
                            <button type="button" id="search" class="btn btn-info"><?= Yii::t("strings", "Search"); ?></button>
                            <button type="button" id="clear_from" class="btn btn-primary"><?= Yii::t("strings", "Clear"); ?></button>
                        </div>
                    </div>
                </td>
                <td class="text-right wmd_30" style="position: relative;">
                    <button type="button" class="btn btn-primary btn-xs" onclick="printDiv('deleteForm')"><i class="fa fa-print"></i>&nbsp;<?php echo Yii::t("strings", "Print"); ?></button>
                </td>
            </tr>
        </table>
    </form>
</div>
<form id="deleteForm" action="" method="post">
    <div class="row form-group clearfix text-center txt_left_xs mp_center media_print mp_visible">
        <?php if (!empty($this->settings->title)): ?>
            <h1 style="font-size: 30px;margin: 0;"><?php echo $this->settings->title; ?></h1>
        <?php endif; ?>
        <?php if (!empty($this->settings->author_address)): ?>
            <?php echo $this->settings->author_address; ?><br>
        <?php endif; ?>
        <h3 class="inv_title text-uppercase"><u><?php echo Yii::t("strings", "Profit And Loss Statement"); ?></u></h3><br>
    </div>
    <div id="ajaxContent">
        <?php if (!empty($dataset) && count($dataset) > 0) : ?>
            <div class="table-responsive">
                <table class="table table-striped table-bordered tbl_invoice_view">
                    <tr id="r_checkAll" class="bg_gray">
                        <th class="text-center" style="width:4%;"><?php echo Yii::t('strings', 'SL#'); ?></th>
                        <th><?php echo Yii::t('strings', 'Invoice Date'); ?></th>
                        <th><?php echo Yii::t('strings', 'Invoice No'); ?></th>
                        <th class="text-right"><?php echo Yii::t("strings", "Sale"); ?></th>
                        <th class="text-right"><?php echo Yii::t("strings", "Purchase"); ?></th>
                        <th class="text-right"><?php echo Yii::t("strings", "Profit"); ?></th>
                    </tr>
                    <?php
                    $counter = 0;
                    if (isset($_GET['page']) && $_GET['page'] > 1) {
                        $counter = ($_GET['page'] - 1) * $pages->pageSize;
                    }
                    foreach ($dataset as $data):
                        $counter++;
                        ?>
                        <tr>
                            <td class="text-center"><?php echo $counter; ?></td>
                            <td><?php echo date('j M Y', strtotime($data->invoice_date)); ?></td>
                            <td>
                                <a class="txt_ul dis_print" href="<?php echo $this->createUrl(AppUrl::URL_SALE_VIEW, array('id' => trim($data->invoice_no))); ?>" target="_blank"><?php echo $data->invoice_no; ?></a>
                                <span class="show_in_print"><?php echo $data->invoice_no; ?></span>
                            </td>
                            <td class="text-right"><?php echo AppHelper::getFloat($data->invoice_amount); ?></td>
                            <td class="text-right"><?php echo AppHelper::getFloat($data->purchase_amount); ?></td>
                            <td class="text-right<?php echo ($data->profit < 0) ? ' color_red' : ''; ?>"><?php echo AppHelper::getFloat($data->profit); ?></td>
                        </tr>
                        <?php
                        $sumPur[] = $data->purchase_amount;
                        $sumSale[] = $data->invoice_amount;
                        $sumProfit[] = $data->profit;
                    endforeach;
                    ?>
                    <tr class="bg_gray">
                        <th colspan="3" class="text-right"><?php echo Yii::t("strings", "Total"); ?></th>
                        <th class="text-right"><?php echo AppHelper::getFloat(array_sum($sumSale)); ?></th>
                        <th class="text-right"><?php echo AppHelper::getFloat(array_sum($sumPur)); ?></th>
                        <th class="text-right"><?php echo AppHelper::getFloat(array_sum($sumProfit)); ?></th>
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
            <div class="alert alert-info"><?php echo Yii::t("strings", "No records found! Please search between dates."); ?></div>
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
                url: ledgerUrl + "/profit/search",
                data: _form.serialize(),
                success: function(res) {
                    showLoader("", false);
                    $("#ajaxContent").html('');
                    $("#ajaxContent").html(res);
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    var _html = "<b>Request failed :</b> " + errorThrown + " [" + jqXHR.status + "]";
                    showLoader("", false);
                    //$('#ajaxContent').html(_html).addClass('alert-danger');
                    $("#ajaxContent").showAjaxMessage({html: _html, type: 'error'});
                }
            });
            e.preventDefault();
        });
    });
</script>