<?php if (!empty($dataset) && count($dataset) > 0) : ?>
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
                        <?php echo AppHelper::getCleanValue($data->name); ?>&nbsp;<?php echo!empty($data->size) ? "(" . $data->size . ")&nbsp;" : ""; ?>
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
<?php else: ?>
    <div class="alert alert-info">No products found!</div>
<?php endif; ?>


