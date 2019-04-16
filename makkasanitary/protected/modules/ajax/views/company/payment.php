<?php if (!empty($dataset) && count($dataset) > 0) : ?>
    <div class="table-responsive">
        <table class="table table-striped table-bordered tbl_invoice_view">
            <tr id="r_checkAll">
                <th class="text-center" style="width:4%;"><?php echo Yii::t('strings', 'SL#'); ?></th>
                <th><?php echo Yii::t('strings', 'Pay Date'); ?></th>
                <th><?php echo Yii::t('strings', 'Method'); ?></th>
                <th class=""><?php echo Yii::t('strings', 'Invoice No'); ?></th>
                <th class="text-right"><?php echo Yii::t('strings', 'Invoice Bill'); ?></th>
                <th class="text-right"><?php echo Yii::t('strings', 'Discount'); ?></th>
                <th class="text-right"><?php echo Yii::t('strings', 'Invoice Paid'); ?></th>
                <th class="text-right"><?php echo Yii::t('strings', 'Advance'); ?></th>
                <th class="text-right"><?php echo Yii::t('strings', 'Balance'); ?></th>
                <th class="text-center"><?php echo Yii::t('strings', 'Actions'); ?></th>
                <?php if ($this->hasUserAccess('company_payment_delete')): ?>
                    <th class="text-center" style="width:3%;"><input type="checkbox" id="checkAll" onclick="toggleCheckboxes(this)"></th>
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
                <tr class="pro_cat pro_cat_<?php echo $data->type; ?>">
                    <td class="text-center"><?php echo $counter; ?></td>
                    <td><?php echo date('j M Y', strtotime($data->pay_date)); ?></td>
                    <td>
                        <?php
                        if ($data->payment_mode == AppConstant::PAYMENT_CHECK) {
                            echo $data->payment_mode . "<br>";
                            echo "<u>Account</u>: " . Account::model()->getname($data->account_id) . "<br>";
                            echo "<u>Check No</u>: " . $data->check_no;
                        } else if ($data->payment_mode == AppConstant::PAYMENT_CASH) {
                            echo "<span style='color:forestgreen'>" . $data->payment_mode . "</span>";
                        } else {
                            echo "<span style='color:grey'>" . AppConstant::PAYMENT_NO . "</span>";
                        }
                        ?>
                    </td>
                    <td class="<?php echo empty($data->invoice_no) ? " bg_gray" : ""; ?>">
                        <?php if (empty($data->invoice_no)): ?>
                            <?php if ($data->type == AppConstant::TYPE_ADVANCE): ?>
                                <span style="color:gray"><?php echo Yii::t("strings", "Advance"); ?></span>
                            <?php elseif ($data->type == AppConstant::TYPE_DUE): ?>
                                <span style="color:gray"><?php echo Yii::t("strings", "Due Paid"); ?></span>
                            <?php else: ?>
                                <span style="color:gray"><?php echo Yii::t("strings", "Previous Due"); ?></span>
                            <?php endif; ?>
                        <?php else: ?>
                            <?php echo $data->invoice_no; ?>
                        <?php endif; ?>
                    </td>
                    <td class="text-right<?php echo!empty($data->invoice_amount) ? " bg_gray" : ""; ?>"><?php echo AppHelper::getFloat($data->invoice_amount); ?></td>
                    <td class="text-right<?php echo!empty($data->discount_amount) ? " bg_gray" : ""; ?>"><?php echo AppHelper::getFloat($data->discount_amount); ?></td>
                    <td class="text-right<?php echo!empty($data->invoice_paid) ? " bg_gray" : ""; ?>"><?php echo AppHelper::getFloat($data->invoice_paid); ?></td>
                    <td class="text-right<?php echo!empty($data->advance_amount) ? " bg_gray" : ""; ?>"><?php echo AppHelper::getFloat($data->advance_amount); ?></td>
                    <td class="text-right<?php echo!empty($data->balance_amount) ? " bg_gray" : ""; ?>"><?php echo AppHelper::getFloat($data->balance_amount); ?></td>
                    <td class="text-center">
                        <?php if (empty($data->invoice_amount) && in_array($data->type, array(AppConstant::TYPE_ADVANCE, AppConstant::TYPE_DUE, AppConstant::TYPE_PREVIOUS_DUE))): ?>
                            <?php if ($this->hasUserAccess('company_payment_edit')): ?>
                                <a class="btn btn-info btn-xs" href="<?php echo $this->createUrl(AppUrl::URL_COMPANY_PAYMENT_EDIT, array('id' => $data->_key)); ?>"><?php echo Yii::t('strings', 'Edit'); ?></a>
                            <?php endif; ?>
                        <?php endif; ?>
                        <?php if (!empty($data->invoice_no)): ?>
                            <a class="btn btn-primary btn-xs" href="<?php echo $this->createUrl(AppUrl::URL_PURCHASE_VIEW, array('id' => $data->invoice_no)); ?>" target="_blank"><?php echo Yii::t('strings', 'View'); ?></a>
                        <?php endif; ?>
                    </td>
                    <?php if ($this->hasUserAccess('company_payment_delete')): ?>
                        <td class="text-center"><input type="checkbox" name="data[]" value="<?php echo $data->id; ?>" class="check"></td>
                    <?php endif; ?>
                </tr>
                <?php
                $sum_invoice_amount[] = $data->invoice_amount;
                $sum_discount_amount[] = $data->discount_amount;
                $sum_invoice_paid[] = $data->invoice_paid;
                $sum_adv_amount[] = $data->advance_amount;
                $sum_balance_amount[] = $data->balance_amount;
            endforeach;
            ?>
            <tr class="bg_gray">
                <th colspan="4" class="text-right"><?php echo Yii::t("strings", "Total"); ?></th>
                <th colspan="1" class="text-right"><?php echo AppHelper::getFloat(array_sum($sum_invoice_amount)); ?></th>
                <th colspan="1" class="text-right"><?php echo AppHelper::getFloat(array_sum($sum_discount_amount)); ?></th>
                <th colspan="1" class="text-right"><?php echo AppHelper::getFloat(array_sum($sum_invoice_paid)); ?></th>
                <th colspan="1" class="text-right"><?php echo AppHelper::getFloat(array_sum($sum_adv_amount)); ?></th>
                <th colspan="1" class="text-right"><?php echo AppHelper::getFloat(array_sum($sum_balance_amount)); ?></th>
                <th colspan=""></th>
                <?php if ($this->hasUserAccess('company_payment_delete')): ?>
                    <th colspan=""></th>
                <?php endif; ?>
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
            'prevPageLabel' => '<',
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
    <div class="alert alert-info"><?php echo Yii::t('strings', 'No records found!'); ?></div>
<?php endif; ?>