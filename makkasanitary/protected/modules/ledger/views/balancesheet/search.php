<?php if (!empty($dataset) && count($dataset) > 0) : ?>
    <div class="table-responsive">
        <table class="table table-striped table-bordered tbl_invoice_view">
            <tr id="r_checkAll" class="bg_gray">
                <th class="text-center" style="width:4%;"><?php echo Yii::t('strings', 'SL#'); ?></th>
                <th><?php echo Yii::t('strings', 'Date'); ?></th>
                <th class="text-right" style="width:12%;"><?php echo Yii::t('strings', 'Debit'); ?></th>
                <th class="text-right" style="width:12%;"><?php echo Yii::t('strings', 'Credit'); ?></th>
                <th class="text-right" style="width:12%;"><?php echo Yii::t('strings', 'Balance'); ?></th>
            </tr>
            <?php
            $counter = 0;
            if (isset($_GET['page']) && $_GET['page'] > 1) {
                $counter = ($_GET['page'] - 1) * $pages->pageSize;
            }
            foreach ($dataset as $data):
                $counter++;
                $_debit = AppObject::balancesheetSumDebit($data->pay_date);
                $_credit = AppObject::balancesheetSumCredit($data->pay_date);
                $_balance = AppObject::balancesheetSumBalance($data->pay_date);
                ?>
                <tr class="">
                    <td class="text-center"><?php echo $counter; ?></td>
                    <td><?php echo date('j M Y', strtotime($data->pay_date)); ?></td>
                    <td class="text-right"><?php echo $_debit; ?></td>
                    <td class="text-right"><?php echo $_credit; ?></td>
                    <td class="text-right"><?php echo $_balance; ?></td>
                </tr>
                <?php
                $sum_debit[] = $_debit;
                $sum_credit[] = $_credit;
                $sum_balance[] = $_balance;
            endforeach;
            ?>
            <tr class="bg_gray">
                <th colspan="2" class="text-right"><?php echo Yii::t("strings", "Total Amount"); ?></th>
                <th colspan="1" class="text-right"><?php echo AppHelper::getFloat(array_sum($sum_debit)); ?></th>
                <th colspan="1" class="text-right"><?php echo AppHelper::getFloat(array_sum($sum_credit)); ?></th>
                <th colspan="1" class="text-right"><?php echo AppHelper::getFloat(array_sum($sum_balance)); ?></th>
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
    <div class="alert alert-info"><?php echo Yii::t("strings", "No records found!"); ?></div>
<?php endif; ?>