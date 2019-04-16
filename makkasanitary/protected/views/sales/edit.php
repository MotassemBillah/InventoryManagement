<?php
$this->breadcrumbs = array(
    'Sales' => array(AppUrl::URL_SALE),
    'Edit'
);
?>
<div class="row content-panel">
    <div class="col-md-12">
        <form action="" method="post">
            <input type="hidden" id="saleId" name="saleId" value="<?php echo $model->id; ?>">
            <div class="row clearfix">
                <div class="col-md-6 col-sm-6">
                    <div class="form-group">
                        <div class="input-group">
                            <input type="search" id="product" name="product" class="form-control" placeholder="search product">
                            <span class="input-group-addon" id="customerSearch" style="padding:0;">
                                <button class="btn btn-info btn-xs" id="clearForm" type="button" style="padding:6px 10px;"><?php echo Yii::t("strings", "Clear"); ?></button>
                            </span>
                            <div id="ajaxContainer" style="display: none;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <?php
        $form = $this->beginWidget('CActiveForm', array(
            'id' => 'frmSale',
            'enableClientValidation' => true,
            'clientOptions' => array('validateOnSubmit' => true),
            'htmlOptions' => array('class' => 'ps_form')
        ));
        ?>
        <div class="row clearfix">
            <div class="clearfix">
                <div class="col-md-3 col-sm-3">
                    <div class="form-group">
                        <label for="customer_id"><?php echo Yii::t("strings", "Customer"); ?></label>
                        <?php
                        $customerList = Customer::model()->getList();
                        $custList = CHtml::listData($customerList, 'id', 'name');
                        echo CHtml::dropDownList('customer_id', $model->customer_id, $custList, array('empty' => 'Select', 'class' => 'form-control', 'style' => 'width:100%'));
                        ?>
                    </div>
                </div>
                <div class="col-md-3 col-sm-3">
                    <div class="form-group">
                        <label for="created_by"><?php echo Yii::t("strings", "Sale Person"); ?></label>
                        <?php
                        $userList = User::model()->getList();
                        $ulist = CHtml::listData($userList, 'id', 'login');
                        echo CHtml::dropDownList('created_by', $model->created_by, $ulist, array('empty' => 'Select', 'class' => 'form-control', 'required' => 'required'));
                        ?>
                    </div>
                </div>
                <div class="col-md-3 col-sm-4">
                    <div class="form-group">
                        <?php echo $form->labelEx($model, "invoice_no"); ?>
                        <?php echo $form->textField($model, 'invoice_no', array('class' => 'form-control')); ?>
                    </div>
                </div>
                <div class="col-md-3 col-sm-4">
                    <div class="form-group">
                        <?php echo $form->checkbox($model, 'has_transport', array('class' => 'chk_no_mvam')); ?>&nbsp;
                        <?php echo $form->labelEx($model, "has_transport"); ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="row clearfix">
            <div class="col-md-12 col-sm-12">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <tr>
                            <th>
                                <?php echo Yii::t("strings", "Product"); ?>&nbsp;
                                <span>
                                    <b>[</b><b>{</b><u>Category</u> - <u>Head</u> - <u>Color</u> - <u>Grade</u><b>}</b> Available<b>]</b>
                                </span>
                            </th>
                            <th>
                                <span class="pull-right text-center qty_price_header"><?php echo Yii::t("strings", "Price"); ?></span>
                                <span class="pull-right text-center qty_price_header"><?php echo Yii::t("strings", "Quantity"); ?></span>
                            </th>
                        </tr>
                        <?php
                        if (!empty($model->items) && count($model->items) > 0):
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
                                                <b>[</b><b>{</b><?php echo $catName . $typeName . $colorName . $gradeName; ?><b>}</b>&nbsp;Available : <?php echo AppObject::inStockProduct($item->product_id); ?><b>]</b>
                                            </span>
                                        </label>
                                    </td>
                                    <td>
                                        <div class="psizes clearfix">
                                            <span class="pull-right qty_price"><input type="number" class="form-control" name="prices[<?php echo $item->product_id; ?>]" placeholder="price" min="0" step="any" value="<?php echo $item->price; ?>"></span>
                                            <span class="pull-right qty_price"><input type="number" class="form-control" name="quantity[<?php echo $item->product_id; ?>]" placeholder="quantity" min="0" step="any" value="<?php echo $item->quantity; ?>"></span>
                                            <span class="pull-right qty_price"><input type="hidden" name="pid[]" value="<?php echo $item->product_id; ?>"></span>
                                            <?php if ($this->hasUserAccess('sale_item_delete')): ?>
                                                <a class="btn btn-danger btn-xs ajax_del" href="javascript://" data-target="#r_<?php echo $item->id; ?>" data-info="<?php echo $item->id; ?>" title="<?php echo Yii::t("strings", "Remove"); ?>"><i class="fa fa-trash-o"></i></a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php
                            endforeach;
                        endif;
                        ?>
                    </table>
                </div>
            </div>
        </div>

        <div class="clearfix">
            <div class="col-md-12 col-sm-12 form-group text-center">
                <?php echo CHtml::submitButton('Save', array('class' => 'btn btn-primary', 'name' => 'btnSale')); ?>
            </div>
        </div>
        <?php $this->endWidget(); ?>
    </div>
</div>
<div class="modal fade" id="containerForAddItem" tabindex="-1" role="dialog" aria-labelledby="containerForAddItemLabel"></div>
<script type="text/javascript">
    $(document).ready(function() {
        $(document).on("input", "#product", function(e) {
            var _url = ajaxUrl + '/product/find';

            if ($(this).val() == "") {
                $("#ajaxContainer").html("<ul class='pro_list'><li>type something for search</li></ul>").show();
                return false;
            }

            $.post(_url, {name: $(this).val(), ajax: true}, function(data) {
                if (data.success == true) {
                    $("#ajaxContainer").html(data.html).show();
                } else {
                    $("#ajaxContainer").html(data.html).show();
                }
            }, "json");

            e.preventDefault();
        });

        $(document).on("change", ".include_product", function(e) {
            var _saleID = $("#saleId").val();
            var _productID = $(this).val();
            var _url = ajaxUrl + '/sales/add';

            if ($(this).is(":checked")) {
                $.post(_url, {saleId: _saleID, productID: _productID}, function(data) {
                    if (data.success === true) {
                        $(".include_product").prop("checked", false);
                        window.location.reload();
                    } else {
                        $("#popup").html(data.message).show();
                    }
                }, "json");
            }
            e.preventDefault();
        });

        $(document).on("click", "#clearForm", function(e) {
            $("#product").val("");
            $("#ajaxContainer").html("").hide();
            e.preventDefault();
        });

        $(document).on("click", ".ajax_del", function() {
            var _rc = confirm('Are you sure about this action? This cannot be undone!');

            if (_rc === true) {
                showLoader("Processing...", true);
                var _row = $(this).attr('data-target');
                var _itemID = $(this).attr('data-info');
                var _url = ajaxUrl + '/sales/remove';

                $.post(_url, {itemNo: _itemID, ajax: true}, function(response) {
                    if (response.success == true) {
                        $("#popup").html(response.message).show();
                        $(_row).remove();
                        setTimeout(hidePopup, 4000);
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
    });
</script>