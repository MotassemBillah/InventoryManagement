<?php if (!empty($dataset) && count($dataset) > 0) : ?>
    <div class="table-responsive">
        <table class="table table-striped table-bordered tbl_invoice_view">
            <tr id="r_checkAll" class="bg_gray">
                <th class="text-center" style="width:5%;"><?php echo Yii::t('strings', 'SL#'); ?></th>
                <th><?php echo Yii::t('strings', 'Date'); ?></th>
                <th><?php echo Yii::t('strings', 'Head'); ?></th>
                <th><?php echo Yii::t('strings', 'Purpose'); ?></th>
                <th><?php echo Yii::t('strings', 'By Whom'); ?></th>
                <th class="text-right"><?php echo Yii::t('strings', 'Amount'); ?></th>
                <th class="text-center dis_print"><?php echo Yii::t('strings', 'Actions'); ?></th>
                <?php if ($this->hasUserAccess('expense_delete')): ?>
                    <th class="text-center dis_print" style="width:3%;"><input type="checkbox" id="checkAll" onclick="toggleCheckboxes(this)"></th>
                <?php endif; ?>
            </tr>
            <?php
            $counter = 0;
            if (isset($_GET['page']) && $_GET['page'] > 1) {
                $counter = ($_GET['page'] - 1) * $pages->pageSize;
            }
            foreach ($dataset as $data):
                $counter++;
                ?>
                <tr class="pro_cat pro_cat_">
                    <td class="text-center"><?php echo $counter; ?></td>
                    <td><?php echo date('j M Y', strtotime($data->pay_date)); ?></td>
                    <td><?php echo!empty($data->ledger_head_id) ? LedgerHead::model()->findByPk($data->ledger_head_id)->name : ''; ?></td>
                    <td><?php echo $data->purpose; ?></td>
                    <td><?php echo $data->by_whom; ?></td>
                    <td class="text-right"><?php echo AppHelper::getFloat($data->amount); ?></td>
                    <td class="text-center dis_print">
                        <?php if ($this->hasUserAccess('expense_edit')): ?>
                            <a class="btn btn-info btn-xs" href="<?php echo $this->createUrl(AppUrl::URL_LEDGER_EXPENSE_EDIT, array('id' => $data->_key)); ?>"><?php echo Yii::t('strings', 'Edit'); ?></a>
                        <?php endif; ?>
                        <a class="btn btn-primary btn-xs" href="<?php echo $this->createUrl(AppUrl::URL_LEDGER_EXPENSE_VIEW, array('id' => $data->_key)); ?>"><?php echo Yii::t('strings', 'View'); ?></a>
                    </td>
                    <?php if ($this->hasUserAccess('expense_delete')): ?>
                        <td class="text-center dis_print"><input type="checkbox" name="data[]" value="<?php echo $data->id; ?>" class="check"></td>
                    <?php endif; ?>
                </tr>
                <?php
                $sum_balance_amount[] = $data->amount;
            endforeach;
            ?>
            <tr class="bg_gray dis_print">
                <th colspan="5" class="text-right"><?php echo Yii::t("strings", "Total Amount"); ?></th>
                <th colspan="1" class="text-right"><?php echo AppHelper::getFloat(array_sum($sum_balance_amount)); ?></th>
                <th class="text-center dis_print" colspan="1"></th>
                <?php if ($this->hasUserAccess('expense_delete')): ?>
                    <th class="text-center dis_print" colspan="1"></th>
                <?php endif; ?>
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