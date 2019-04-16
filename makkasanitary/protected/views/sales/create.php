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
                                    <?php
                                    $companyList = Company::model()->getList();
                                    $comlist = CHtml::listData($companyList, 'id', 'name');
                                    echo CHtml::dropDownList('company', 'company_id', $comlist, array('empty' => 'Company', 'class' => 'form-control'));
                                    ?>
                                </div>
                                <div class="col-md-2 col-sm-2">
                                    <select id="type" class="form-control" name="type" style="width:100%;">
                                        <option value=""><?php echo Yii::t("strings", "Company Head"); ?></option>
                                    </select>
                                </div>
                                <div class="col-md-2 col-sm-2">
                                    <?php
                                    $categoryList = Category::model()->getList();
                                    $catList = CHtml::listData($categoryList, 'id', 'name');
                                    echo CHtml::dropDownList('category', 'category_id', $catList, array('empty' => 'Category', 'class' => 'form-control'));
                                    ?>
                                </div>
                                <div class="col-md-3 col-sm-3">
                                    <input type="text" name="q" id="q" class="form-control" placeholder="search name" size="30">
                                </div>
                            </div>
                        </form>
                    </td>
                </tr>
            </table>
        </div>
        <?php
        $form = $this->beginWidget('CActiveForm', array(
            'id' => 'frmSale',
            'enableClientValidation' => true,
            'clientOptions' => array('validateOnSubmit' => true),
            'htmlOptions' => array('class' => 'ps_form')
        ));
        ?>
        <div class="row">
            <div class="clearfix">
                <div class="col-md-3 col-sm-6">
                    <div class="form-group">
                        <label for="existing_customer"><?php echo Yii::t("strings", "Customer Type"); ?>:</label>&nbsp;
                        <label class="txt_np" for="existing_customer"><input type="radio" id="existing_customer" class="customer_toggle" name="customer_type" value="exist" data-target="#exist_customer_form" checked="checked">&nbsp;Existing</label>
                        <label class="txt_np" for="new_customer"><input type="radio" id="new_customer" class="customer_toggle" name="customer_type" value="new" data-target="#new_customer_form">&nbsp;New</label>
                    </div>
                </div>
                <div class="col-md-3 col-sm-4">
                    <div class="form-group">
                        <input type="text" class="form-control" name="invoice_no" placeholder="Invoice Number">
                    </div>
                </div>
                <div class="col-md-3 col-sm-4">
                    <div class="form-group">
                        <label for="transport">
                            <input type="checkbox" id="transport" name="transport" value="1" class="chk_no_mvam">&nbsp;
                            <?php echo Yii::t("strings", "Has Transport"); ?>
                        </label>
                    </div>
                </div>
            </div>

            <div class="col-md-12 customer_div" id="exist_customer_form" style="display: block;">
                <div class="form-group">
                    <label for="customer_id"><?php echo Yii::t("strings", "Customer"); ?> :</label>
                    <?php
                    $customerList = Customer::model()->getList();
                    $custList = CHtml::listData($customerList, 'id', 'name');
                    echo CHtml::dropDownList('customer_id', 'customer_id', $custList, array('empty' => 'Select', 'class' => 'form-control', 'style' => 'display:inline-block;min-width:150px'));
                    ?>
                </div>
            </div>

            <div class="col-md-12 col-sm-12 customer_div" id="new_customer_form">
                <div class="row clearfix">
                    <div class="col-md-3 col-sm-4">
                        <div class="form-group">
                            <?php echo $form->labelEx($customer, 'name'); ?>
                            <?php echo $form->textField($customer, 'name', array('class' => 'form-control')); ?>
                        </div>
                        <div class="form-group">
                            <?php echo $form->labelEx($customer, 'phone'); ?>
                            <?php echo $form->textField($customer, 'phone', array('class' => 'form-control')); ?>
                        </div>
                    </div>
                    <div class="col-md-9 col-sm-8">
                        <div class="form-group">
                            <?php echo $form->labelEx($customer, 'address'); ?>
                            <?php echo $form->textArea($customer, 'address', array('class' => 'form-control')); ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-12 col-sm-12">
                <div id="ajaxContent">
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
                            foreach ($dataset as $data):
                                $catName = "<u>" . AppObject::categoryName($data->category_id) . "</u>";
                                $typeName = !empty($data->type) ? " - <u>" . AppObject::companyHeadName($data->type) . "</u>" : " - <u>n/a</u>";
                                $colorName = !empty($data->color) ? " - <u>" . strtolower($data->color) . "</u>" : " - <u>n/a</u>";
                                $gradeName = !empty($data->grade) ? " - <u>" . strtoupper($data->grade) . "</u>" : " - <u>n/a</u>";

                                $productStok = AppObject::stokByProduct($data->id);

                                if (!empty($data->unit) && $data->unit == "Foot") {
                                    $_stockAvailable = $productStok . " <em style='color:blue'>({$data->unit})</em>";
                                } else {
                                    $_stockAvailable = $productStok;
                                }
                                ?>
                                <tr id="tr_<?php echo $data->id; ?>" class="pro_cat pro_cat_<?php echo AppObject::categoryName($data->category_id); ?>">
                                    <td>
                                        <label class="txt_np" for="product_<?php echo $data->id; ?>" data-info='<?php echo $data->name; ?>'>
                                            <input type="checkbox" id="product_<?php echo $data->id; ?>" name="products[<?php echo $data->id; ?>]" value="<?php echo $data->id; ?>">
                                            <?php echo AppHelper::getCleanValue($data->name); ?>&nbsp;
                                            <span>
                                                <b>[</b><b>{</b><?php echo $catName . $typeName . $colorName . $gradeName; ?><b>}</b>&nbsp;Available : <?php echo $_stockAvailable; ?><b>]</b>
                                            </span>
                                        </label>
                                    </td>
                                    <td>
                                        <div class="psizes clearfix">
                                            <span class="pull-right"><a class="btn btn-primary btn-xs add_to_cart" data-rel="<?php echo $data->id; ?>" data-info='<?php echo $data->name; ?>' href="javascript:void(0);" style="padding: 4px;"><?php echo Yii::t("strings", "Add To Cart"); ?></a></span>
                                            <span class="pull-right qty_price"><input type="number" id="price_<?php echo $data->id; ?>" class="form-control" name="prices[<?php echo $data->id; ?>]" placeholder="price" min="0" step="any" value="<?php echo $this->setPrice(AppObject::purchasePrice($data->id)); ?>"></span>
                                            <span class="pull-right qty_price"><input type="number" id="qty_<?php echo $data->id; ?>" class="form-control qty_check" name="quantity[<?php echo $data->id; ?>]" placeholder="quantity" min="0" step="any" pattern="[0-9]|1\d|2[0-3]"></span>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
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
                </div>
            </div>

            <!--            <div class="col-md-12 col-sm-12 form-group text-center">
            <?php // echo CHtml::resetButton('Reset', array('class' => 'btn btn-info')); ?>
                            <button type="button" class="btn btn-primary" name="btnSale" id="btnSale"><?php // echo Yii::t("strings", "Save");              ?></button>
                        </div>-->
        </div>
        <?php $this->endWidget(); ?>
    </div>
</div>