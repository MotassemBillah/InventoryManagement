<?php
$this->breadcrumbs = array(
    'Sales' => array(AppUrl::URL_SALE),
    'Edit'
);
?>
<div class="row content-panel">
    <div class="col-md-12">
        <?php
        $form = $this->beginWidget('CActiveForm', array(
            'id' => 'frmSaleReturn',
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
                        $customerList = Customer::model()->findAll();
                        $custList = CHtml::listData($customerList, 'id', 'name');
                        echo CHtml::dropDownList('customer_id', $model->customer_id, $custList, array('empty' => 'Select', 'class' => 'form-control', 'style' => 'width:100%'));
                        ?>
                    </div>
                </div>
                <div class="col-md-3 col-sm-3">
                    <div class="form-group">
                        <label for="created_by"><?php echo Yii::t("strings", "Person"); ?></label>
                        <?php
                        $userList = User::model()->getList();
                        $ulist = CHtml::listData($userList, 'id', 'login');
                        echo CHtml::dropDownList('created_by', $model->created_by, $ulist, array('empty' => 'Select', 'class' => 'form-control', 'required' => 'required'));
                        ?>
                    </div>
                </div>
                <div class="col-md-3 col-sm-4">
                    <div class="form-group">
                        <?php echo $form->labelEx($model, "return_invoice"); ?>
                        <?php echo $form->textField($model, 'return_invoice', array('class' => 'form-control')); ?>
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
                                                <b>[</b><b>{</b><?php echo $catName . $typeName . $colorName . $gradeName; ?><b>}</b><b>]</b>
                                            </span>
                                        </label>
                                    </td>
                                    <td>
                                        <div class="psizes clearfix">
                                            <span class="pull-right qty_price"><input type="number" class="form-control" name="prices[<?php echo $item->product_id; ?>]" placeholder="price" min="0" step="any" value="<?php echo $item->price; ?>"></span>
                                            <span class="pull-right qty_price"><input type="number" class="form-control" name="quantity[<?php echo $item->product_id; ?>]" placeholder="quantity" min="0" value="<?php echo $item->quantity; ?>"></span>
                                            <span class="pull-right qty_price"><input type="hidden" name="pid[]" value="<?php echo $item->product_id; ?>"></span>
                                            <a class="btn btn-danger btn-xs ajax_del" href="javascript://" data-id="<?php echo $item->product_id; ?>" data-info="<?php echo $item->sale_return_id; ?>" title="<?php echo Yii::t("strings", "Remove"); ?>"><i class="fa fa-trash-o"></i></a>
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
<script type="text/javascript">
    $(document).ready(function() {
        $(document).on("click", ".ajax_del", function() {
            var _rc = confirm('Are you sure about this action? This cannot be undone!');
            if (_rc == true) {
                getLoader(this, -5, 0, 20);
                var _key = $(this).attr('data-id');
                var _pid = $(this).attr('data-info');
                var _url = ajaxUrl + '/sales/remove';

                $.post(_url, {key: _key, sid: _pid}, function(response) {
                    if (response.success == true) {
                        $("#popup").html(response.message).show();
                        $("#r_" + _key).remove();
                        setTimeout(hidePopup, 4000);
                        if (response.redirect == true) {
                            redirectTo(baseUrl + '/sales');
                        }
                    } else {
                        $("#popup").html(response.message).show();
                        setTimeout(hidePopup, 4000);
                    }
                    $(".ajaxLoader").remove();
                }, "json");
            } else {
                return false;
            }
        });
    });
</script>