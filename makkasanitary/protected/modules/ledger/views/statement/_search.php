<div class="form-group clearfix text-center mp_center media_print mp_mt">
    <?php if (!empty($this->settings->title)): ?>
        <h1 style="font-size:20px;font-weight:600;margin:0;"><?php echo $this->settings->title; ?></h1>
    <?php endif; ?>
    <?php if (!empty($this->settings->author_address)): ?>
        <?php echo $this->settings->author_address; ?><br>
    <?php endif; ?>
    <h4 class="inv_title" style="font-size:16px;font-weight:600;"><u><?php echo Yii::t("strings", "Financial Statement"); ?></u></h4>
    <h4 class="inv_title" style="font-size:16px;font-weight:600;">
        From : <u style="font-weight:normal"><?php echo $dateForm; ?></u>
        To : <u style="font-weight:normal"><?php echo $dateTo; ?></u>
    </h4>
</div>
<?php
$_openingBalance = $openingBalance;
?>
<table class="table table-bordered tbl_invoice_view" style="margin:0;">
    <tr>
        <th>Opening Balance : <?php echo $_openingBalance; ?></th>
    </tr>
</table>
<div class="clearfix">
    <div class="col-md-6 mpw_50" style="padding-left:0;">
        <table class="table table-striped table-bordered tbl_invoice_view" style="margin:0;">
            <tr>
                <th class="text-center" colspan="3">Debit</th>
            </tr>
            <?php if (!empty($debitDataset) && count($debitDataset) > 0): ?>
                <tr>
                    <th class="text-center" style="width:5%;">#SL</th>
                    <th>Head Name</th>
                    <th class="text-right">Amount</th>
                </tr>
                <?php
                $counter = 0;
                foreach ($debitDataset as $data):
                    $counter++;
                    $dcashSum = CashAccount::model()->sumDebitBetweenDate($dateForm, $dateTo, $data->ledger_head_id);
                    ?>
                    <tr>
                        <td class="text-center"><?php echo $counter; ?></td>
                        <td><?php echo!empty($data->ledger_head_id) ? LedgerHead::model()->findByPk($data->ledger_head_id)->name : ""; ?></td>
                        <td class="text-right"><?php echo $dcashSum; ?></td>
                    </tr>
                    <?php
                    $sum_debit[] = $dcashSum;
                endforeach;
                ?>
                <tr>
                    <th colspan="2">Total</th>
                    <th class="text-right"><?php echo AppHelper::getFloat(array_sum($sum_debit)); ?></th>
                </tr>
            <?php else: ?>
                <tr>
                    <td class="text-center" colspan="3">No Data Found.</td>
                </tr>
            <?php endif; ?>
        </table>
    </div>
    <div class="col-md-6 pull-right mpw_50" style="padding-right:0;">
        <table class="table table-striped table-bordered tbl_invoice_view" style="margin:0;">
            <tr>
                <th class="text-center" colspan="3">Credit</th>
            </tr>
            <?php if (!empty($creditDataset) && count($creditDataset) > 0): ?>
                <tr>
                    <th class="text-center" style="width:5%;">#SL</th>
                    <th>Head Name</th>
                    <th class="text-right">Amount</th>
                </tr>
                <?php
                $_num = 0;
                foreach ($creditDataset as $datac):
                    $_num++;
                    $ccashSum = CashAccount::model()->sumCreditBetweenDate($dateForm, $dateTo, $datac->ledger_head_id);
                    ?>
                    <tr>
                        <td class="text-center"><?php echo $_num; ?></td>
                        <td><?php echo $datac->created . !empty($datac->ledger_head_id) ? LedgerHead::model()->findByPk($datac->ledger_head_id)->name : "No Head"; ?></td>
                        <td class="text-right"><?php echo $ccashSum; ?></td>
                    </tr>
                    <?php
                    $sum_credit[] = $ccashSum;
                endforeach;
                ?>
                <tr>
                    <th colspan="2">Total</th>
                    <th class="text-right"><?php echo AppHelper::getFloat(array_sum($sum_credit)); ?></th>
                </tr>
            <?php else: ?>
                <tr>
                    <td class="text-center" colspan="3">No Data Found.</td>
                </tr>
            <?php endif; ?>
        </table>
    </div>
</div>
<?php
$_debitBalance = !empty($debitDataset) ? array_sum($sum_debit) : 0;
$_creditBalance = !empty($creditDataset) ? array_sum($sum_credit) : 0;
$_closingBalance = (($_openingBalance + $_debitBalance) - $_creditBalance);
?>
<table class="table table-bordered tbl_invoice_view">
    <tr>
        <th>Closing Balance : <?php echo $_closingBalance; ?></th>
    </tr>
</table>