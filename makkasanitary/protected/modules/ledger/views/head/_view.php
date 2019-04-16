<div class="table-responsive">
    <table class="table table-striped table-bordered tbl_invoice_view">
        <?php if (!empty($dataset) && count($dataset) > 0) : ?>
            <tr class="bg_gray">
                <th class="text-center" style="width:4%;"><?php echo Yii::t('strings', 'SL#'); ?></th>
                <th style="width:12%;"><?php echo Yii::t('strings', 'Date'); ?></th>
                <th><?php echo Yii::t('strings', 'Purpose'); ?></th>
                <th style="width:15%;"><?php echo Yii::t('strings', 'By Whom'); ?></th>
                <th><?php echo Yii::t('strings', 'Note'); ?></th>
                <th class="text-right" style="width:10%;"><?php echo Yii::t('strings', 'Debit'); ?></th>
                <th class="text-right" style="width:10%;"><?php echo Yii::t('strings', 'Credit'); ?></th>
                <th class="text-right" style="width:10%;"><?php echo Yii::t('strings', 'Balance'); ?></th>
            </tr>
            <?php
            $counter = 0;
            if (isset($_GET['page']) && $_GET['page'] > 1) {
                $counter = ($_GET['page'] - 1) * $pages->pageSize;
            }
            foreach ($dataset as $data):
                $counter++;
                $_debit = CashAccount::model()->sumDebit($data->id);
                $_credit = CashAccount::model()->sumCredit($data->id);
                $_balance = CashAccount::model()->sumBalance($data->id);
                ?>
                <tr>
                    <td class="text-center" style="width:5%;"><?php echo $counter; ?></td>
                    <td><?php echo date('j M Y', strtotime($data->created)); ?></td>
                    <td><?php echo $data->purpose; ?></td>
                    <td><?php echo $data->by_whom; ?></td>
                    <td>
                        <?php
                        echo!empty($data->bank_id) ? Bank::model()->findByPk($data->bank_id)->name . "<br>" : '';
                        echo!empty($data->account_id) ? Account::model()->findByPk($data->account_id)->name . "<br>" : '';
                        echo!empty($data->check_no) ? $data->check_no : '';
                        ?>
                    </td>
                    <td class="text-right" style="width:10%;"><?php echo AppHelper::getFloat($data->debit); ?></td>
                    <td class="text-right" style="width:10%;"><?php echo AppHelper::getFloat($data->credit); ?></td>
                    <td class="text-right" style="width:10%;"><?php echo AppHelper::getFloat($data->balance); ?></td>
                </tr>
                <?php
                $sum_debit[] = $data->debit;
                $sum_credit[] = $data->credit;
                $sum_balance[] = $data->balance;
            endforeach;
            ?>
            <tr class="bg_gray">
                <th class="text-right" colspan="5">Total</th>
                <th class="text-right"><?php echo AppHelper::getFloat(array_sum($sum_debit)); ?></th>
                <th class="text-right"><?php echo AppHelper::getFloat(array_sum($sum_credit)); ?></th>
                <th class="text-right"><?php echo AppHelper::getFloat(array_sum($sum_balance)); ?></th>
            </tr>
        <?php else: ?>
            <tr>
                <td colspan="8"><?php echo Yii::t("strings", "No Transaction Found."); ?></td>
            </tr>
        <?php endif; ?>
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