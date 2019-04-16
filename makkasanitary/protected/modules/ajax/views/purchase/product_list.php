<div class="table-responsive">
    <table class="table table-bordered table-hover">
        <tr>
            <th><?php echo Yii::t("strings", "Product"); ?></th>
            <th>
                <span class="pull-left qty_price_header"><?php echo Yii::t("strings", "Size"); ?></span>
                <span class="pull-right text-center qty_price_header"><?php echo Yii::t("strings", "Price"); ?></span>
                <span class="pull-right text-center qty_price_header"><?php echo Yii::t("strings", "Free"); ?></span>
                <span class="pull-right text-center qty_price_header"><?php echo Yii::t("strings", "Quantity"); ?></span>
            </th>
        </tr>
        <?php foreach ($model->items as $item): ?>
            <tr id="r_<?php echo $item->item_id; ?>">
                <td>
                    <input type="checkbox" id="product-<?php echo $item->product_id; ?>" name="products[]" value="<?php echo $item->product_id; ?>" checked="checked">
                    <?php echo AppObject::productName($item->product_id) . " ( " . Yii::t("strings", AppObject::categoryName($item->product->category_id)) . " )"; ?>
                </td>
                <td>                    
                    <div class="psizes clearfix">
                        <span class="pull-left wxs_100">
                            <label class="txt_np" for="size-<?php echo $item->size_id; ?>">
                                <input type="checkbox" id="size-<?php echo $item->size_id; ?>" name="sizes[]" value="<?php echo $item->product_id . "_" . $item->size_id; ?>" checked="checked">&nbsp;<?php echo AppObject::productSize($item->size_id); ?>
                            </label>
                        </span>
                        <span class="pull-right qty_price"><input type="number" class="form-control rp" name="prices[<?php echo $item->size_id; ?>]" placeholder="price" min="0" step="any" value="<?php echo $item->retail_price; ?>"></span>
                        <span class="pull-right qty_price"><input type="number" class="form-control rp" name="frees[<?php echo $item->size_id; ?>]" placeholder="price" min="0" value="<?php echo $item->free; ?>"></span>
                        <span class="pull-right qty_price"><input type="number" class="form-control qty" name="quantity[<?php echo $item->size_id; ?>]" placeholder="quantity" min="0" value="<?php echo $item->quantity; ?>"></span>    
                        <a class="btn btn-danger btn-xs ajax_del" href="javascript://" data-id="<?php echo $item->item_id; ?>" data-info="<?php echo $item->purchase_id; ?>" title="<?php echo Yii::t("strings", "Remove"); ?>"><i class="fa fa-trash-o"></i></a>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>