<?php if (!empty($dataset) && count($dataset) > 0) : ?>
    <div class="table-responsive">
        <table class="table table-striped table-bordered tbl_invoice_view">
            <tr id="r_checkAll" class="bg_gray">
                <th class="text-center" style="width:4%;"><?php echo Yii::t('strings', 'SL#'); ?></th>
                <th><?php echo Yii::t('strings', 'Invoice Date'); ?></th>
                <th><?php echo Yii::t('strings', 'Invoice No'); ?></th>
                <th class="text-right"><?php echo Yii::t("strings", "Sale"); ?></th>
                <th class="text-right"><?php echo Yii::t("strings", "Purchase"); ?></th>
                <th class="text-right"><?php echo Yii::t("strings", "Profit"); ?></th>
            </tr>
            <?php
            $counter = 0;
            if (isset($_GET['page']) && $_GET['page'] > 1) {
                $counter = ($_GET['page'] - 1) * $pages->pageSize;
            }
            foreach ($dataset as $data):
                $counter++;
                ?>
                <tr>
                    <td class="text-center"><?php echo $counter; ?></td>
                    <td><?php echo date('j M Y', strtotime($data->invoice_date)); ?></td>
                    <td>
                        <a class="txt_ul dis_print" href="<?php echo $this->createUrl(AppUrl::URL_SALE_VIEW, array('id' => trim($data->invoice_no))); ?>" target="_blank"><?php echo $data->invoice_no; ?></a>
                        <span class="show_in_print"><?php echo $data->invoice_no; ?></span>
                    </td>
                    <td class="text-right"><?php echo AppHelper::getFloat($data->invoice_amount); ?></td>
                    <td class="text-right"><?php echo AppHelper::getFloat($data->purchase_amount); ?></td>
                    <td class="text-right<?php echo ($data->profit < 0) ? ' color_red' : ''; ?>"><?php echo AppHelper::getFloat($data->profit); ?></td>
                </tr>
                <?php
                $sumPur[] = $data->purchase_amount;
                $sumSale[] = $data->invoice_amount;
                $sumProfit[] = $data->profit;
            endforeach;
            ?>
            <tr class="bg_gray">
                <th colspan="3" class="text-right"><?php echo Yii::t("strings", "Total"); ?></th>
                <th class="text-right"><?php echo AppHelper::getFloat(array_sum($sumSale)); ?></th>
                <th class="text-right"><?php echo AppHelper::getFloat(array_sum($sumPur)); ?></th>
                <th class="text-right"><?php echo AppHelper::getFloat(array_sum($sumProfit)); ?></th>
            </tr>
        </table>
    </div>

    <div class="paging dis_print">
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
    <div class="alert alert-info"><?php echo Yii::t("strings", "No records found! Please search between dates."); ?></div>
<?php endif; ?>