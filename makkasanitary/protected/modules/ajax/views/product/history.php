<?php
$_currentStock = AppObject::stokByProduct($model->id);
$unitInfo = !empty($model->unit) ? " <em style='color:blue'>({$model->unit})</em>" : "";

if (!empty($model->unit) && $model->unit == "Foot") {
    //$_pcstock = $_currentStock * $model->unitsize;
    $_currentStockAilable = $_currentStock . $unitInfo;
} else {
    $_currentStockAilable = $_currentStock;
}
?>
<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close" title="Close"><span aria-hidden="true">x</span></button>
            <div class="modal-title">
                <label>
                    <u><?php echo $model->name; ?></u>
                    <span style="font-weight:normal;margin-left:10px;"><b style="color:#d9534f">[</b>&nbsp;<?php echo Yii::t("strings", "Available"); ?>&nbsp;:&nbsp;<?php echo $_currentStockAilable; ?>&nbsp;<b style="color:#d9534f">]</b></span>
                </label><br>
                <label><?php echo Yii::t("strings", "Purchase And Sale History"); ?></label>
            </div>
        </div>
        <div class="modal-body" style="overflow-y: auto;min-height: 200px;max-height: 440px;">
            <div class="row clearfix">
                <div class="col-md-6 col-sm-6 no_pad_rgt">
                    <div class="table-responsive">
                        <label><?php echo Yii::t("strings", "Purchase Record"); ?></label>
                        <?php if (!empty($model->purchase_items) && count($model->purchase_items) > 0): ?>
                            <table class="table table-bordered">
                                <tr>
                                    <th><?php echo Yii::t("strings", "From"); ?></th>
                                    <th><?php echo Yii::t("strings", "In Date"); ?></th>
                                    <th class="text-center"><?php echo Yii::t("strings", "In Qty"); ?></th>
                                    <th class="text-right"><?php echo Yii::t("strings", "In Price"); ?></th>
                                </tr>
                                <?php
                                foreach ($model->purchase_items as $pitems):
                                    ?>
                                    <tr>
                                        <td><?php echo $pitems->purchase->local_company_name; ?></td>
                                        <td><?php echo date('d-m-Y', strtotime($pitems->purchase->created)); ?></td>
                                        <td class="text-center"><?php echo $pitems->quantity; ?></td>
                                        <td class="text-right"><?php echo $pitems->price; ?></td>
                                    </tr>
                                    <?php
                                    $qtyArr[] = $pitems->quantity;
                                    $priceArr[] = $pitems->price;
                                endforeach;
                                ?>
                                <tr>
                                    <th colspan="2"></th>
                                    <th class="text-center"><?php echo array_sum($qtyArr) . $unitInfo; ?></th>
                                    <th class="text-right"><?php echo array_sum($priceArr); ?></th>
                                </tr>
                            </table>
                        <?php else: ?>
                            <div class="alert alert-info">No purchase record found!</div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-md-6 col-sm-6 no_pad_lft">
                    <div class="table-responsive">
                        <label><?php echo Yii::t("strings", "Sales Record"); ?></label>
                        <?php if (!empty($model->sale_items) && count($model->sale_items) > 0): ?>
                            <table class="table table-bordered">
                                <tr>
                                    <th><?php echo Yii::t("strings", "Out Date"); ?></th>
                                    <th class="text-center"><?php echo Yii::t("strings", "Out Qty"); ?></th>
                                    <th class="text-right"><?php echo Yii::t("strings", "Out Price"); ?></th>
                                </tr>
                                <?php
                                foreach ($model->sale_items as $sitems):
                                    ?>
                                    <tr>
                                        <td><?php echo date('j M Y', strtotime($sitems->sale->created)); ?></td>
                                        <td class="text-center"><?php echo $sitems->quantity; ?></td>
                                        <td class="text-right"><?php echo $sitems->price; ?></td>
                                    </tr>
                                    <?php
                                    $sqtyArr[] = $sitems->quantity;
                                    $spriceArr[] = $sitems->price;
                                endforeach;
                                ?>
                                <tr>
                                    <th></th>
                                    <th class="text-center"><?php echo array_sum($sqtyArr) . $unitInfo; ?></th>
                                    <th class="text-right"><?php echo array_sum($spriceArr); ?></th>
                                </tr>
                            </table>
                        <?php else: ?>
                            <div class="alert alert-info">No sale record found!</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-info" data-dismiss="modal" aria-label="Close" title="Close"><?php echo Yii::t("strings", "Close"); ?></button>
        </div>
    </div>
</div>