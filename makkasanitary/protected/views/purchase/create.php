<?php
$this->breadcrumbs = array(
    'Purchase' => array(AppUrl::URL_PURCHASE),
    'Create'
);
?>
<?php if (!empty($dataset) && count($dataset) > 0) : ?>
    <?php
    $form = $this->beginWidget('CActiveForm', array(
        'id' => 'frmCreatePurchase',
        'enableAjaxValidation' => true,
        'enableClientValidation' => true,
        'clientOptions' => array('validateOnSubmit' => true),
        'htmlOptions' => array('class' => 'ps_form')
    ));
    ?>
    <div class="well">
        <table width="100%">
            <tr>
                <td style="width:100%;">
                    <div class="row clearfix">
                        <div class="col-md-2 col-sm-2 no_pad_rgt">
                            <?php
                            $companyList = Company::model()->getList();
                            $comlist = CHtml::listData($companyList, 'id', 'name');
                            echo CHtml::dropDownList('company', 'company_id', $comlist, array('empty' => 'Company', 'class' => 'form-control'));
                            ?>
                        </div>
                        <div class="col-md-2 col-sm-2 no_pad">
                            <select id="type" class="form-control" name="type" style="width:100%;">
                                <option value=""><?php echo Yii::t("strings", "Company Head"); ?></option>
                            </select>
                        </div>
                        <div class="col-md-2 col-sm-2 no_pad">
                            <?php
                            $categoryList = Category::model()->getList();
                            $catList = CHtml::listData($categoryList, 'id', 'name');
                            echo CHtml::dropDownList('category', 'category_id', $catList, array('empty' => 'Category', 'class' => 'form-control'));
                            ?>
                        </div>
                        <div class="col-md-2 col-sm-2 no_pad">
                            <input type="text" class="form-control" name="invoice_no" placeholder="Invoice Number">
                        </div>
                        <div class="col-md-2 col-sm-2 no_pad">
                            <div class="input-group">
                                <input type="text" id="datepickerExample" class="form-control" name="invoice_date" placeholder="Date (yyyy-mm-dd)" readonly>
                                <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                            </div>
                        </div>
                        <div class="col-md-2 col-sm-2 no_pad_lft">
                            <input type="text" class="form-control" name="local_company_name" placeholder="local company name">
                        </div>
                    </div>
                    <div class="clearfix">
                        <?php echo $form->checkbox($model, 'has_transport', array('class' => 'chk_no_mvam')); ?>&nbsp;
                        <?php echo $form->labelEx($model, "has_transport", array('class' => 'no_mrgn')); ?>
                    </div>
                </td>
            </tr>
        </table>
    </div>
    <div class="table-responsive table_ps_list" id="ajaxContent">
        <table class="table table-bordered table-hover no_mrgn">
            <tr>
                <th>
                    <?php echo Yii::t("strings", "Product"); ?>&nbsp;
                    <span>
                        <b>[</b><u>Category</u> - <u>Head</u> - <u>Color</u> - <u>Grade</u> - <u>Code</u><b>]</b>
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
                $codeName = !empty($data->code) ? " - <u>" . strtoupper($data->code) . "</u>" : " - <u>n/a</u>";
                ?>
                <tr class="pro_cat pro_cat_<?php echo $data->category_id; ?>">
                    <td>
                        <label class="txt_np" for="product_<?php echo $data->id; ?>">
                            <input type="checkbox" id="product_<?php echo $data->id; ?>" name="products[]" value="<?php echo $data->id; ?>">
                            <?php echo AppHelper::getCleanValue($data->name); ?>&nbsp;
                            <span>
                                <b>[</b><?php echo $catName . $typeName . $colorName . $gradeName . $codeName; ?><b>]</b>
                            </span>
                        </label>
                    </td>
                    <td>
                        <div class="psizes clearfix">
                            <span class="pull-right qty_price"><input type="number" class="form-control rp" name="prices[<?php echo $data->id; ?>]" placeholder="price" min="0"></span>
                            <span class="pull-right qty_price"><input type="number" class="form-control qty" name="quantity[<?php echo $data->id; ?>]" placeholder="quantity" min="0"></span>
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

    <div class="form-group text-center">
        <?php echo CHtml::resetButton('Reset', array('class' => 'btn btn-info')); ?>
        <button id="btnPurchase" name="btnPurchase" class="btn btn-primary" type="button"><?php echo Yii::t("strings", "Save"); ?></button>
    </div>
    <?php $this->endWidget(); ?>
<?php else: ?>
    <div class="alert alert-info">
        No products found! Please <a class="btn btn-warning" href="<?php echo Yii::app()->createUrl(AppUrl::URL_PRODUCT_CREATE); ?>"><?php echo Yii::t("strings", "Create Product"); ?></a>
    </div>
<?php endif; ?>
<script type="text/javascript">
    $(document).ready(function() {
        $("#datepickerExample").datepicker({
            format: 'yyyy-mm-dd'
        });

        $(document).on("change", "#company", function() {
            var _url = ajaxUrl + "/company/findmeta";

            if ($(this).val() !== "") {
                $.post(_url, {com_id: $(this).val()}, function(response) {
                    if (response.success === true) {
                        $("#type").html(response.html);
                    } else {
                        $("#type").html(response.html);
                    }
                }, "json");
            } else {
                $("#type").html("<option value=''>All</option>");
            }
        });

        $(document).on("change", "#company, #category, #type", function(e) {
            showLoader("Fetching data...", true);
            var _url = ajaxUrl + "/product/search";
            var _com = $("#company").val();
            var _cat = $("#category").val();
            var _type = $("#type").val();

            $.post(_url, {com: _com, cat: _cat, type: _type}, function(res) {
                $("#ajaxContent").html('');
                $("#ajaxContent").html(res);
                showLoader("", false);
            });
            e.preventDefault();
        });
    });
</script>