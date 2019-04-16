<?php if (!empty($dataset) && count($dataset) > 0) : ?>
    <?php foreach ($dataset as $_data) : ?>
        <table class="table table-bordered no_mrgn_btm">
            <tr>
                <th><?php echo $_data->name; ?></th>
                <th class="text-center" style="width:15%"><?php echo Yii::t("strings", "Purpose"); ?></th>
                <th class="text-center" style="width:10%"><?php echo Yii::t("strings", "Debit"); ?> (TK)</th>
                <th class="text-center" style="width:10%"><?php echo Yii::t("strings", "Credit"); ?> (TK)</th>
                <th class="text-center" style="width:18%"><?php echo Yii::t("strings", "Account"); ?></th>
                <th class="text-center" style="width:12%"><?php echo Yii::t("strings", "Check No"); ?></th>
            </tr>
            <?php foreach ($_data->particulers as $particuler) : ?>
                <tr>
                    <td>
                        <label class="txt_np" for="prticiler_<?php echo $particuler->id; ?>">
                            <input type="checkbox" id="prticiler_<?php echo $particuler->id; ?>" name="particuler[]" value="<?php echo $_data->id . "_" . $particuler->id; ?>">&nbsp;<?php echo $particuler->particuler; ?>
                        </label>
                    </td>
                    <td style="width:15%"><input type="text" class="form-control pad4" id="purpose_<?php echo $particuler->id; ?>" name="purpose[<?php echo $particuler->id; ?>]"></td>
                    <td style="width:10%"><input type="number" class="form-control pad4" id="debit_<?php echo $particuler->id; ?>" name="debit[<?php echo $particuler->id; ?>]" min="0" step="any"></td>
                    <td style="width:10%"><input type="number" class="form-control pad4" id="credit_<?php echo $particuler->id; ?>" name="credit[<?php echo $particuler->id; ?>]" min="0" step="any"></td>
                    <td style="width:18%">
                        <?php
                        $accList = Account::model()->getList();
                        $aList = CHtml::listData($accList, 'id', function($c) {
                                    return $c->account_name . " (" . AppObject::getBankName($c->bank_id) . ")";
                                });
                        echo CHtml::dropDownList("account_id[{$particuler->id}]", "account_id", $aList, array('empty' => 'Select', 'class' => 'form-control pad4'));
                        ?>
                    </td>
                    <td style="width:12%"><input type="text" class="form-control pad4" id="check_no_<?php echo $particuler->id; ?>" name="check_no[<?php echo $particuler->id; ?>]"></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endforeach; ?>
<?php else: ?>
    <div class="alert alert-info">No records found!</div>
<?php endif; ?>