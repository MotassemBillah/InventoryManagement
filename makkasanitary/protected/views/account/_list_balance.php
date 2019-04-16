<?php if (!empty($dataset) && count($dataset) > 0) : ?>
    <div class="table-responsive">
        <table class="table table-striped table-bordered tbl_invoice_view">
            <tr id="r_checkAll">
                <th class="text-center" style="width:4%;"><?php echo Yii::t("strings", "SL#"); ?></th>
                <th><?php echo Yii::t("strings", "Date"); ?></th>
                <th><?php echo Yii::t("strings", "Purpose"); ?></th>
                <th><?php echo Yii::t("strings", "Person"); ?></th>
                <th class="text-right" style="width:10%;"><?php echo Yii::t("strings", "Debit"); ?></th>
                <th class="text-right" style="width:10%;"><?php echo Yii::t("strings", "Credit"); ?></th>
            </tr>
            <?php
            $counter = 0;
            foreach ($dataset as $data):
                $counter++;
                ?>
                <tr>
                    <td class="text-center"><?php echo $counter; ?></td>
                    <td><?php echo date("d-m-Y", strtotime($data->created)); ?></td>
                    <td><?php echo AppHelper::getCleanValue($data->purpose); ?></td>
                    <td><?php echo AppHelper::getCleanValue($data->by_whom); ?></td>
                    <td class="text-right"><?php echo AppHelper::getFloat($data->debit); ?></td>
                    <td class="text-right"><?php echo AppHelper::getFloat($data->credit); ?></td>
                </tr>
                <?php
                $sumDebt[] = $data->debit;
                $sumCrdt[] = $data->credit;
            endforeach;
            ?>
            <tr>
                <th colspan="4" class="text-right"><?php echo Yii::t("strings", "Total"); ?></th>
                <th class="text-right"><?php echo AppHelper::getFloat(array_sum($sumDebt)); ?></th>
                <th class="text-right"><?php echo AppHelper::getFloat(array_sum($sumCrdt)); ?></th>
            </tr>
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