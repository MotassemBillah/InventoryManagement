<?php
$this->breadcrumbs = array(
    'Purchase' => array(AppUrl::URL_PURCHASE),
    'Edit'
);
?>
<?php
$form = $this->beginWidget('CActiveForm', array(
    'id' => 'frmCreatePurchase',
    'enableAjaxValidation' => true,
    'enableClientValidation' => true,
    'clientOptions' => array('validateOnSubmit' => true),
    'htmlOptions' => array('class' => 'ps_form')
        )
);
?>
<input type="hidden" name="catID" value="">

<div class="well">
    <div class="input-group-btn clearfix">
        <div class="col-md-2 col-sm-2 no_pad">
            <?php
            $companyList = Company::model()->getList();
            $comList = CHtml::listData($companyList, 'id', 'name');
            echo CHtml::dropDownList('company', $model->company_id, $comList, array('empty' => 'Company', 'class' => 'form-control', 'style' => '', 'onchange' => 'showCatgoryRows(this)'));
            ?>
        </div>
        <div class="col-md-2 col-sm-2 no_pad">
            <?php
            $categoryList = Category::model()->getList();
            $catList = CHtml::listData($categoryList, 'id', 'name');
            echo CHtml::dropDownList('category', $model->category_id, $catList, array('empty' => 'Category', 'class' => 'form-control', 'style' => '', 'onchange' => 'showCatgoryRows(this)'));
            ?>
        </div>
        <div class="col-md-2 col-sm-2 no_pad">
            <?php
            $userList = User::model()->getList();
            $ulist = CHtml::listData($userList, 'id', 'login');
            echo CHtml::dropDownList('created_by', $model->created_by, $ulist, array('empty' => 'Select', 'class' => 'form-control', 'required' => 'required'));
            ?>
        </div>
        <div class="col-md-2 col-sm-2 no_pad">
            <input type="text" class="form-control" name="invoice_no" placeholder="Invoice Number" value="<?php echo $model->invoice_no; ?>">
        </div>
        <div class="col-md-2 col-sm-2 no_pad">
            <div class="input-group">
                <input type="text" id="datepickerExample" class="form-control" name="invoice_date" placeholder="Invoice Date (yyyy-mm-dd)" readonly value="<?php echo date("Y-m-d", strtotime($model->invoice_date)); ?>">
                <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
            </div>
        </div>
        <div class="col-md-2 col-sm-2 no_pad">
            <input type="text" class="form-control" name="local_company_name" placeholder="local company name" value="<?php echo $model->local_company_name; ?>">
        </div>
    </div>
    <div class="clearfix">
        <?php echo $form->checkbox($model, 'has_transport', array('class' => 'chk_no_mvam')); ?>&nbsp;
        <?php echo $form->labelEx($model, "has_transport"); ?>
    </div>
</div>
<div class="table-responsive">
    <table class="table table-bordered table-hover" id="ajaxProductList">
        <tr id="r_header">
            <th>
                <?php echo Yii::t("strings", "Product"); ?>&nbsp;
                <span>
                    <b>[</b><u>Category</u> - <u>Head</u> - <u>Color</u> - <u>Grade</u><b>]</b>
                </span>
            </th>
            <th>
                <span class="pull-right text-center qty_price_header"><?php echo Yii::t("strings", "Price"); ?></span>
                <span class="pull-right text-center qty_price_header"><?php echo Yii::t("strings", "Quantity"); ?></span>
            </th>
        </tr>
        <?php
        foreach ($model->items as $item):
            $catName = "<u>" . AppObject::categoryName($item->product->category_id) . "</u>";
            $typeName = !empty($item->product->type) ? " - <u>" . AppObject::companyHeadName($item->product->type) . "</u>" : " - <u>n/a</u>";
            $colorName = !empty($item->product->color) ? " - <u>" . strtolower($item->product->color) . "</u>" : " - <u>n/a</u>";
            $gradeName = !empty($item->product->grade) ? " - <u>" . strtoupper($item->product->grade) . "</u>" : " - <u>n/a</u>";
            ?>
            <tr id="r_<?php echo $item->id; ?>">
                <td>
                    <label class="txt_np" for="product_<?php echo $item->product_id; ?>">
                        <input type="checkbox" id="product_<?php echo $item->product_id; ?>" name="products[]" value="<?php echo $item->product_id; ?>" checked="checked">
                        <?php echo AppObject::productName($item->product_id); ?>&nbsp;
                        <span>
                            <b>[</b><?php echo $catName . $typeName . $colorName . $gradeName; ?><b>]</b>
                        </span>
                    </label>
                </td>
                <td>
                    <div class="psizes clearfix">
                        <span class="pull-right qty_price"><input type="number" class="form-control rp" name="prices[<?php echo $item->product_id; ?>]" min="0" step="any" value="<?php echo $item->price; ?>"></span>
                        <span class="pull-right qty_price"><input type="number" class="form-control qty" name="quantity[<?php echo $item->product_id; ?>]" min="0" value="<?php echo $item->quantity; ?>"></span>
                        <span class="pull-right qty_price"><input type="hidden" name="pid[]" value="<?php echo $item->product_id; ?>"></span>
                        <?php if ($this->hasUserAccess('purchase_item_delete')): ?>
                            <a class="btn btn-danger btn-xs ajax_del" href="javascript://" data-id="<?php echo $item->id; ?>" data-info="<?php echo $item->purchase_id; ?>" title="<?php echo Yii::t("strings", "Remove"); ?>"><i class="fa fa-trash-o"></i></a>
                        <?php endif; ?>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
    <div id="productList"></div>
</div>

<div class="form-group text-center">
    <?php echo CHtml::submitButton('Save', array('class' => 'btn btn-primary', 'name' => 'btnPurchase')); ?>
</div>
<?php $this->endWidget(); ?>
<script type="text/javascript">
    $(document).ready(function() {
        $("#datepickerExample").datepicker({
            format: 'yyyy-mm-dd'
        });

        $(document).on("click", ".ajax_del", function() {
            var _rc = confirm('Are you sure about this action? This cannot be undone!');

            if (_rc === true) {
                showLoader("Processing...", true);
                var _key = $(this).attr('data-id');
                var _pid = $(this).attr('data-info');
                var _url = ajaxUrl + '/purchase/remove';

                $.post(_url, {key: _key, pid: _pid}, function(response) {
                    if (response.success === true) {
                        $("#popup").html(response.message).show();
                        $("#r_" + _key).remove();
                        setTimeout(hidePopup, 3000);
                        if (response.redirect === true) {
                            redirectTo(baseUrl + '/purchase');
                        }
                    } else {
                        $("#popup").html(response.message).show();
                        setTimeout(hidePopup, 4000);
                    }
                    showLoader("", false);
                }, "json");
            } else {
                return false;
            }
        });

        $(document).on("click", "#btnPurchaseAd", function(e) {
            showLoader("Processing...", true);
            var _form = $("#frmCreatePurchase");
            var _table = $("#ajaxProductList");
            var _tableLastRow = _table.find("tr:last-child");
            var _url = ajaxUrl + '/purchase/productlist';

            $.post(_url, _form.serialize(), function(response) {
                if (response.success === true) {
                    _tableLastRow.after(response.data);
                } else {
                    $("#popup").html(response.message).show();
                    setTimeout(hidePopup, 3000);
                }
                showLoader("", false);
            }, "json");
            e.preventDefault();
            return false;
        });
    });
</script>