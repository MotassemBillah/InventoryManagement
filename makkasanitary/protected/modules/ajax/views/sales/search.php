<?php if (!empty($dataset) && count($dataset) > 0) : ?>
    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <tr>
                <th>
                    <?php echo Yii::t("strings", "Product"); ?>&nbsp;
                    <span>
                        <b>[</b><b>{</b><u>Category</u> - <u>Head</u> - <u>Color</u> - <u>Grade</u> - <u>Code</u><b>}</b> Available<b>]</b>
                    </span>
                </th>
                <th>
                    <span class="pull-right text-center qty_price_header"></span>
                    <span class="pull-right text-center qty_price_header"><?php echo Yii::t("strings", "Price"); ?></span>
                    <span class="pull-right text-center qty_price_header"><?php echo Yii::t("strings", "Quantity"); ?></span>
                </th>
            </tr>
            <?php foreach ($dataset as $data): ?>
                <?php
                $catName = "<u>" . AppObject::categoryName($data->category_id) . "</u>";
                $typeName = !empty($data->type) ? " - <u>" . AppObject::companyHeadName($data->type) . "</u>" : " - <u>n/a</u>";
                $colorName = !empty($data->color) ? " - <u>" . strtolower($data->color) . "</u>" : " - <u>n/a</u>";
                $gradeName = !empty($data->grade) ? " - <u>" . strtoupper($data->grade) . "</u>" : " - <u>n/a</u>";
                $codeName = !empty($data->code) ? " - <u>" . strtoupper($data->code) . "</u>" : " - <u>n/a</u>";

                if (!empty($highlight)) {
                    $text = "<span class='highlight'>" . $highlight . "</span>";
                    $productName = str_replace($highlight, $text, strtolower($data->name));
                } else {
                    $productName = AppHelper::getCleanValue($data->name);
                }

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
                            <?php echo $productName; ?>&nbsp;
                            <span>
                                <b>[</b><b>{</b><?php echo $catName . $typeName . $colorName . $gradeName . $codeName; ?><b>}</b>&nbsp;Available : <?php echo $_stockAvailable; ?><b>]</b>
                            </span>
                        </label>
                    </td>
                    <td>
                        <div class="psizes clearfix">
                            <span class="pull-right"><a class="btn btn-primary btn-xs add_to_cart" data-rel="<?php echo $data->id; ?>" data-info='<?php echo $data->name; ?>' href="javascript:void(0);" style="padding: 4px;"><?php echo Yii::t("strings", "Add To Cart"); ?></a></span>
                            <span class="pull-right qty_price"><input type="number" id="price_<?php echo $data->id; ?>" class="form-control" name="prices[<?php echo $data->id; ?>]" placeholder="price"  min="0" value="<?php echo $this->setPrice(AppObject::purchasePrice($data->id)); ?>"></span>
                            <span class="pull-right qty_price"><input type="number" id="qty_<?php echo $data->id; ?>" class="form-control qty_check" name="quantity[<?php echo $data->id; ?>]" placeholder="quantity" min="0" pattern="[0-9]|1\d|2[0-3]"></span>
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
    <script type="text/javascript">
        $(document).ready(function() {
            $(document).on("click", ".pagination li a", function(e) {
                showLoader("Processing...", true);
                var _form = $("#frmSearch");
                var _srcUrl = $(this).attr('href');

                $.ajax({
                    type: "POST",
                    url: _srcUrl,
                    data: _form.serialize(),
                    success: function(res) {
                        showLoader("", false);
                        $("#ajaxContent").html('');
                        $("#ajaxContent").html(res);
                    }
                });
                e.preventDefault();
            });
        });
    </script>
<?php else: ?>
    <div class="alert alert-info">No records found!</div>
<?php endif; ?>