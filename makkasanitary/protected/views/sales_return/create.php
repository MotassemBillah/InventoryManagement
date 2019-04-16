<?php
$this->breadcrumbs = array(
    'Sales' => array(AppUrl::URL_SALE),
    'Create'
);
?>
<div class="row content-panel">
    <div class="col-md-12">
        <div class="well" style="margin-bottom: 10px;">
            <table width="100%">
                <tr>
                    <td style="width:100%;">
                        <form class="search-form" method="post" name="frmSearch" id="frmSearch">
                            <div class="row clearfix">
                                <div class="col-md-3 col-sm-3">
                                    <input type="text" class="form-control" id="invoice_no" name="invoice_no" placeholder="sale invoice number">
                                </div>
                                <button type="button" id="searchSale" class="btn btn-info"><?php echo Yii::t("strings", "Search"); ?></button>
                            </div>
                        </form>
                    </td>
                </tr>
            </table>
        </div>
        <div class="row">
            <div class="col-md-12 col-sm-12">
                <div id="ajaxContent"></div>
            </div>
        </div>
    </div>
</div>