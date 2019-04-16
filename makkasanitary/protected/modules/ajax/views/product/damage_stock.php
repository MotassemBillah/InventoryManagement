<?php if (!empty($dataset) && count($dataset) > 0) : ?>
    <div class="table-responsive">
        <table class="table table-striped table-bordered tbl_invoice_view">
            <tr id="r_checkAll">
                <th class="text-center" style="width:4%;"><?php echo Yii::t("strings", "SL#"); ?></th>
                <th><?php echo Yii::t("strings", "Product Name"); ?></th>
                <th><?php echo Yii::t("strings", "Size"); ?></th>
                <th><?php echo Yii::t("strings", "Head"); ?></th>
                <th class="w25"><?php echo Yii::t("strings", "Code"); ?></th>
                <th><?php echo Yii::t("strings", "Color"); ?></th>
                <th><?php echo Yii::t("strings", "Grade"); ?></th>
                <th class="text-center"><?php echo Yii::t("strings", "Stock"); ?></th>
            </tr>
            <?php
            $counter = 0;
            foreach ($dataset as $data):
                $counter++;
                ?>
                <tr>
                    <td class="text-center"><?php echo $counter; ?></td>
                    <td>
                        <?php
                        if (!empty($highlight)) {
                            $text = "<span class='highlight'>" . $highlight . "</span>";
                            $productName = str_replace($highlight, $text, strtolower($data->product->name));
                        } else {
                            $productName = AppHelper::getCleanValue($data->product->name);
                        }
                        echo $productName . " ( " . AppObject::categoryName($data->product->category_id) . " )";
                        ?>
                    </td>
                    <td<?php if (empty($data->product->size)) echo ' style="background-color:#ebebeb"'; ?>><?php echo!empty($data->product->size) ? $data->product->size : "N/A"; ?></td>
                    <td<?php if (empty($data->product->type)) echo ' style="background-color:#ebebeb"'; ?>><?php echo!empty($data->product->type) ? AppObject::companyHeadName($data->product->type) : "N/A"; ?></td>
                    <td class="w25"<?php if (empty($data->product->code)) echo ' style="background-color:#ebebeb"'; ?>><?php echo!empty($data->product->code) ? $data->product->code : "N/A"; ?></td>
                    <td<?php if (empty($data->product->color)) echo ' style="background-color:#ebebeb"'; ?>><?php echo!empty($data->product->color) ? ucfirst($data->product->color) : "N/A"; ?></td>
                    <td<?php if (empty($data->product->grade)) echo ' style="background-color:#ebebeb"'; ?>><?php echo!empty($data->product->grade) ? $data->product->grade : "N/A"; ?></td>
                    <td class="text-center">
                        <?php
                        $productStok = AppObject::stokByProduct($data->product->id);

                        if (!empty($data->product->unit) && $data->product->unit == "Foot") {
                            $_stock = $productStok * $data->product->unitsize;
                            $_stockAvailable = "<strong>" . $_stock . "</strong> <em style='color:blue'>({$data->product->unit})</em>";
                        } else {
                            $_stockAvailable = "<strong>" . $productStok . "</strong>";
                        }

                        if ($productStok > 5) {
                            $_style = ' style="color:green"';
                        } elseif ($productStok < 0) {
                            $_style = ' style="color:red"';
                        } else {
                            $_style = ' style="color:black"';
                        }
                        echo "<span{$_style}>{$_stockAvailable}</span>";
                        ?>
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
                'id' => 'pagination',
            )
        ));
        ?>
    </div>
<?php else: ?>
    <div class="alert alert-info">No records found!</div>
<?php endif; ?>