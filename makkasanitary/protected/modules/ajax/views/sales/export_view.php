<?php if (!empty($dataset) && count($dataset) > 0): ?>
    <div id="printDiv">
        <div class="row form-group clearfix text-center txt_left_xs mp_center media_print mp_visible">
            <?php if (!empty($this->settings->title)): ?>
                <h1 style="font-size: 30px;margin: 0;"><?php echo $this->settings->title; ?></h1>
            <?php endif; ?>
            <?php if (!empty($this->settings->author_address)): ?>
                <?php echo $this->settings->author_address; ?><br>
            <?php endif; ?>
            <?php
            if (!empty($this->settings->other_contacts)) {
                $other_contacts = explode(",", $this->settings->other_contacts);
                $_str = "<b>Cell</b> : ";
                foreach ($other_contacts as $_k => $_v) {
                    trim($other_contacts[$_k]);
                    $_str .= $_v . " | ";
                }
                echo $_str;
            }
            if (!empty($this->settings->author_mobile)):
                echo $this->settings->author_mobile . " | ";
            endif;
            if (!empty($this->settings->author_phone)):
                echo "<b>Tel</b> : " . $this->settings->author_phone . " | ";
            endif;
            if (!empty($this->settings->author_email)):
                echo "<b>E-mail</b> : " . $this->settings->author_email;
            endif;
            ?>
            <h3 class="inv_title text-uppercase"><u><?php echo Yii::t("strings", "Invoice Summary"); ?></u></h3><br>
        </div>
        <div class="table-responsive">
            <table class="table table-striped table-bordered tbl_invoice_view">
                <tr id="r_checkAll">
                    <th class="text-center" style="width: 5%;"><?php echo Yii::t("strings", "SL#"); ?></th>
                    <th style="width:10%;"><?php echo Yii::t("strings", "Date"); ?></th>
                    <th><?php echo Yii::t("strings", "Customer"); ?></th>
                    <th><?php echo Yii::t("strings", "Sales Person"); ?></th>
                    <th class="text-left"><?php echo Yii::t("strings", "Invoice No"); ?></th>
                    <th class="text-right"><?php echo Yii::t("strings", "Amount"); ?></th>
                    <th class="text-right"><?php echo Yii::t("strings", "Discount"); ?></th>
                    <th class="text-right"><?php echo Yii::t("strings", "Paid"); ?></th>
                    <th class="text-right"><?php echo Yii::t("strings", "Due Collection"); ?></th>
                    <th class="text-right"><?php echo Yii::t("strings", "Balance"); ?></th>
                </tr>
                <?php
                $counter = 0;
                foreach ($dataset as $data) :
                    $counter++;
                    ?>
                    <tr>
                        <td class="text-center"><?php echo $counter; ?></td>
                        <td><?php echo date('j M Y', strtotime($data->created)); ?></td>
                        <td><?php echo AppObject::customerName($data->customer_id); ?></td>
                        <td><?php echo AppObject::displayNameByUser($data->created_by); ?></td>
                        <td>
                            <?php if (empty($data->invoice_no)): ?>
                                <?php if ($data->type == AppConstant::TYPE_ADVANCE): ?>
                                    <span style="color:grey"><?php echo Yii::t("strings", "Due Collection"); ?></span>
                                <?php endif; ?>
                            <?php else: ?>
                                <a href="<?php echo Yii::app()->createUrl(AppUrl::URL_SALE_VIEW, array('id' => $data->invoice_no)); ?>" target="_blank"><?php echo $data->invoice_no; ?></a>
                            <?php endif; ?>
                        </td>
                        <td class="text-right"><?php echo AppHelper::getFloat($data->invoice_total); ?></td>
                        <td class="text-right<?php echo empty($data->discount_amount) ? " bg_gray" : ""; ?>"><?php echo AppHelper::getFloat($data->discount_amount); ?></td>
                        <td class="text-right<?php echo empty($data->invoice_paid) ? " bg_gray" : ""; ?>"><?php echo AppHelper::getFloat($data->invoice_paid); ?></td>
                        <td class="text-right<?php echo empty($data->advance_amount) ? " bg_gray" : ""; ?>"><?php echo AppHelper::getFloat($data->advance_amount); ?></td>
                        <td class="text-right<?php echo empty($data->balance_amount) ? " bg_gray" : ""; ?>"><?php echo AppHelper::getFloat($data->balance_amount); ?></td>
                    </tr>
                    <?php
                    $sum['amount'][] = $data->invoice_total;
                    $sum['discount'][] = ($data->discount_amount);
                    $sum['paid'][] = $data->invoice_paid;
                    $sum['advance'][] = $data->advance_amount;
                    $sum['dues'][] = ($data->balance_amount);
                endforeach;
                ?>
                <tr class="bg_gray">
                    <th colspan="4"></th>
                    <th class="text-right"><?php echo Yii::t("strings", "Total"); ?></th>
                    <th class="text-right"><?php echo AppHelper::getFloat(array_sum($sum['amount'])); ?></th>
                    <th class="text-right"><?php echo AppHelper::getFloat(array_sum($sum['discount'])); ?></th>
                    <th class="text-right"><?php echo AppHelper::getFloat(array_sum($sum['paid'])); ?></th>
                    <th class="text-right"><?php echo AppHelper::getFloat(array_sum($sum['advance'])); ?></th>
                    <th class="text-right"><?php echo AppHelper::getFloat(array_sum($sum['dues'])); ?></th>
                </tr>
                <tr class="bg_gray">
                    <th colspan="4"></th>
                    <th class="text-right"><?php echo Yii::t("strings", "Total Cash"); ?></th>
                    <th colspan="2"></th>
                    <th class="text-center" colspan="2"><?php echo AppHelper::getFloat(array_sum($sum['paid']) + array_sum($sum['advance'])); ?></th>
                    <th></th>
                </tr>
            </table>
        </div>
    </div>
    <div class="form-group text-center">
        <button type="button" class="btn btn-primary" onclick="printElem('#printDiv')"><i class="fa fa-print"></i>&nbsp;<?php echo Yii::t("strings", "Print"); ?></button>
    </div>
<?php else: ?>
    <div class="alert alert-info">No records found!</div>
<?php endif; ?>