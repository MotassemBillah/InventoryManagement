<?php if (!empty($dataset) && count($dataset) > 0) : ?>
    <div class="table-responsive">
        <table class="table table-striped table-bordered tbl_invoice_view">
            <tr class="bg_gray">
                <th class="text-center" style="width:5%;"><?php echo Yii::t('strings', 'SL#'); ?></th>
                <th><?php echo Yii::t('strings', 'Name'); ?></th>
                <th class="text-right" style="width:10%;"><?php echo Yii::t('strings', 'Debit'); ?></th>
                <th class="text-right" style="width:10%;"><?php echo Yii::t('strings', 'Credit'); ?></th>
                <th class="text-right" style="width:10%;"><?php echo Yii::t('strings', 'Balance'); ?></th>
                <th class="text-center dis_print" style="width:8%;"><?php echo Yii::t('strings', 'Actions'); ?></th>
                <?php if ($this->hasUserAccess('head_delete')): ?>
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
                $_debit = CashAccount::model()->sumDebit($data->id);
                $_credit = CashAccount::model()->sumCredit($data->id);
                $_balance = CashAccount::model()->sumBalance($data->id);
                ?>
                <tr class="pro_cat pro_cat_">
                    <td class="text-center">
                        <?php
                        if (in_array(Yii::app()->user->id, [1])):
                            echo $counter . "_{$data->id}";
                        else:
                            echo $counter;
                        endif;
                        ?>
                    </td>
                    <td>
                        <span class="dis_print">
                            <a href="<?php echo Yii::app()->createUrl(AppUrl::URL_LEDGER_HEAD_VIEW, array('id' => $data->id)); ?>" title="View All Transactions"><?php echo $data->name; ?></a>
                        </span>
                        <span class="show_in_print"><?php echo $data->name; ?></span>
                    </td>
                    <td class="text-right"><?php echo $_debit; ?></td>
                    <td class="text-right"><?php echo $_credit; ?></td>
                    <td class="text-right"><?php echo $_balance; ?></td>
                    <td class="text-center dis_print">
                        <?php if ($this->hasUserAccess('head_edit')): ?>
                            <a class="btn btn-info btn-xs" href="<?php echo $this->createUrl(AppUrl::URL_LEDGER_HEAD_EDIT, array('id' => $data->_key)); ?>"><?php echo Yii::t('strings', 'Edit'); ?></a>
                        <?php endif; ?>
                    </td>
                    <?php if ($this->hasUserAccess('head_delete')): ?>
                        <td class="text-center dis_print">
                            <?php if ($data->is_fixed == 0): ?>
                                <input type="checkbox" name="data[]" value="<?php echo $data->id; ?>" class="check">
                            <?php endif; ?>
                        </td>
                    <?php endif; ?>
                </tr>
                <?php
                $sum_debit[] = $_debit;
                $sum_credit[] = $_credit;
                $sum_balance[] = $_balance;
            endforeach;
            ?>
            <tr class="bg_gray">
                <th class="text-right" colspan="2">Total</th>
                <th class="text-right"><?php echo AppHelper::getFloat(array_sum($sum_debit)); ?></th>
                <th class="text-right"><?php echo AppHelper::getFloat(array_sum($sum_credit)); ?></th>
                <th class="text-right"><?php echo AppHelper::getFloat(array_sum($sum_balance)); ?></th>
                <th class="text-center dis_print"></th>
                <?php if ($this->hasUserAccess('head_delete')): ?>
                    <th class="text-center dis_print"></th>
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