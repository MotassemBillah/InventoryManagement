<?php if (!empty($dataset) && count($dataset) > 0) : ?>
    <div class="table-responsive">
        <table class="table table-striped table-bordered tbl_invoice_view">
            <tr class="bg_gray" id="r_checkAll">
                <th class="text-center" style="width:3%;"><?php echo Yii::t("strings", "SL#"); ?></th>
                <th style="width:12%;"><?php echo Yii::t("strings", "Date"); ?></th>
                <th style="width:10%;"><?php echo Yii::t("strings", "Head"); ?></th>
                <th style="width:10%;"><?php echo Yii::t("strings", "By Whom"); ?></th>
                <th><?php echo Yii::t("strings", "Purpose"); ?></th>
                <th class="text-right" style="width:10%;"><?php echo Yii::t("strings", "Debit"); ?></th>
                <th class="text-right" style="width:10%;"><?php echo Yii::t("strings", "Credit"); ?></th>
                <th class="text-right" style="width:10%;"><?php echo Yii::t("strings", "Balance"); ?></th>
                <th class="text-center dis_print" style="width:8%;"><?php echo Yii::t("strings", "Actions"); ?></th>
                <th class="text-center dis_print" style="width:3%;">
                    <?php if ($this->hasUserAccess('cash_account_delete')): ?>
                        <input type="checkbox" id="checkAll" onclick="toggleCheckboxes(this)">
                    <?php endif; ?>
                </th>
            </tr>
            <?php
            $counter = 0;
            if (isset($_GET['page']) && $_GET['page'] > 1) {
                $counter = ($_GET['page'] - 1) * $pages->pageSize;
            }
            foreach ($dataset as $data):
                $counter++;
                $_head = !empty($data->ledger_head_id) ? LedgerHead::model()->findByPk($data->ledger_head_id) : '';
                ?>
                <tr>
                    <td class="text-center"><?php echo $counter; ?></td>
                    <td><?php echo date("j M Y", strtotime($data->created)); ?></td>
                    <td><?php echo!empty($_head) ? $_head->name : ''; ?></td>
                    <td><?php echo $data->by_whom; ?></td>
                    <td><?php echo $data->purpose; ?></td>
                    <td class="text-right"><?php echo AppHelper::getFloat($data->debit); ?></td>
                    <td class="text-right"><?php echo AppHelper::getFloat($data->credit); ?></td>
                    <td class="text-right"><?php echo AppHelper::getFloat($data->balance); ?></td>
                    <td class="text-center dis_print">
                        <?php if ($this->hasUserAccess('cash_account_edit')): ?>
                            <?php if ($data->is_editable == 1): ?>
                                <?php
                                if ($data->type == 'D') {
                                    $_link = Yii::app()->createUrl(AppUrl::URL_CASH_ACCOUNT_DEPOSIT_EDIT, array('id' => $data->_key));
                                } else {
                                    $_link = Yii::app()->createUrl(AppUrl::URL_CASH_ACCOUNT_WITHDRAW_EDIT, array('id' => $data->_key));
                                }
                                ?>
                                <a class="btn btn-info btn-xs" href="<?php echo $_link; ?>"><?php echo Yii::t('strings', 'Edit'); ?></a>
                                <a class="btn btn-primary btn-xs" href="<?php echo Yii::app()->createUrl(AppUrl::URL_CASH_ACCOUNT_VOUCHER, array('id' => $data->_key)); ?>"><?php echo Yii::t('strings', 'View'); ?></a>
                            <?php endif; ?>
                        <?php endif; ?>
                    </td>
                    <td class="text-center dis_print">
                        <?php if ($this->hasUserAccess('cash_account_delete')): ?>
                            <?php if ($data->is_editable == 1): ?>
                                <input type="checkbox" name="data[]" value="<?php echo $data->id; ?>" class="check">
                            <?php endif; ?>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php
                $sum_dbt[] = $data->debit;
                $sum_cdt[] = $data->credit;
                $sum_blance[] = $data->balance;
            endforeach;
            ?>
            <tr class="bg_gray">
                <th class="text-right" colspan="5"><?php echo Yii::t("strings", "Total"); ?></th>
                <th class="text-right"><?php echo AppHelper::getFloat(array_sum($sum_dbt)); ?></th>
                <th class="text-right"><?php echo AppHelper::getFloat(array_sum($sum_cdt)); ?></th>
                <th class="text-right"><?php echo AppHelper::getFloat(array_sum($sum_blance)); ?></th>
                <th class="dis_print" colspan="2"></th>
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
    <div class="alert alert-info">No records found!</div>
<?php endif; ?>