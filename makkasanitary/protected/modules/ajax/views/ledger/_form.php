<?php if (!empty($dataset) && count($dataset) > 0) : ?>
    <?php foreach ($dataset as $_data) : ?>
        <?php if (!empty($_data->descp)) : ?>
            <table class="table table-bordered no_mrgn_btm">
                <tr>
                    <th><?php echo $_data->particuler; ?></th>
                    <th class="" style="width:15%"><?php echo Yii::t("strings", "Purpose"); ?></th>
                    <th class="text-center" style="width:10%"><?php echo Yii::t("strings", "Debit"); ?> (TK)</th>
                    <th class="text-center" style="width:10%"><?php echo Yii::t("strings", "Credit"); ?> (TK)</th>
                    <th class="text-center" style="width:18%"><?php echo Yii::t("strings", "Account"); ?></th>
                    <th class="text-center" style="width:12%"><?php echo Yii::t("strings", "Check No"); ?></th>
                </tr>
                <?php foreach ($_data->descp as $descp) : ?>
                    <tr>
                        <td>
                            <label class="txt_np" for="prticiler_<?php echo $descp->id; ?>">
                                <input type="checkbox" id="prticiler_<?php echo $descp->id; ?>" name="description[]" value="<?php echo $_data->id . "_" . $descp->id; ?>">&nbsp;<?php echo $descp->description; ?>
                            </label>
                        </td>
                        <td style="width:15%"><input type="text" class="form-control pad4" id="purpose_<?php echo $descp->id; ?>" name="purpose[<?php echo $descp->id; ?>]"></td>
                        <td style="width:10%"><input type="number" class="form-control pad4" id="debit_<?php echo $descp->id; ?>" name="debit[<?php echo $descp->id; ?>]" min="0" step="any"></td>
                        <td style="width:10%"><input type="number" class="form-control pad4" id="credit_<?php echo $descp->id; ?>" name="credit[<?php echo $descp->id; ?>]" min="0" step="any"></td>
                        <td style="width:18%">
                            <?php
                            $accList = LedgerBankAccount::model()->getList();
                            $aList = CHtml::listData($accList, 'id', function($c) {
                                        return $c->account_name . " (" . $c->bank_name . ")";
                                    });
                            echo CHtml::dropDownList("account_id[{$descp->id}]", "account_id", $aList, array('empty' => 'Select', 'class' => 'form-control pad4'));
                            ?>
                        </td>
                        <td style="width:12%"><input type="text" class="form-control pad4" id="check_no_<?php echo $descp->id; ?>" name="check_no[<?php echo $descp->id; ?>]"></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>
    <?php endforeach; ?>
<?php else: ?>
    <div class="alert alert-info">No records found!</div>
<?php endif; ?>