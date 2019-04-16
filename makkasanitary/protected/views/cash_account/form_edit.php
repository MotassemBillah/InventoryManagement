<?php
$this->breadcrumbs = array(
    'Cash Account' => array(AppUrl::URL_CASH_ACCOUNT),
    'Deposit'
);
?>
<div class="row content-panel">
    <div class="col-md-6 col-sm-6">
        <?php
        $form = $this->beginWidget('CActiveForm', array(
            'id' => 'frmCashAccount',
            'enableClientValidation' => true,
            'clientOptions' => array('validateOnSubmit' => true),
        ));
        ?>
        <div class="clearfix">
            <div class="form-group clearfix">
                <?php echo $form->labelEx($model, 'Date', ['class' => 'col-md-6 col-xs-6 text-right']); ?>
                <?php echo $form->textField($model, 'created', array('class' => 'col-md-6 col-xs-6', 'readonly' => 'readonly', 'value' => date('d-m-Y', strtotime($model->created)))); ?>
            </div>
            <div class="form-group clearfix">
                <?php echo $form->labelEx($model, 'Head Name', ['class' => 'col-md-6 col-xs-6 text-right']); ?>
                <?php
                $headList = LedgerHead::model()->getList();
                $hlist = CHtml::listData($headList, 'id', 'name');
                echo $form->dropdownList($model, 'ledger_head_id', $hlist, array('empty' => 'Select', 'class' => 'col-md-6 col-xs-6'));
                ?>
            </div>
            <div class="form-group clearfix">
                <?php echo $form->labelEx($model, 'transaction_type', ['class' => 'col-md-6 col-xs-6 text-right']); ?>
                <label for="for_cash"><input type="radio" id="for_cash" class="transaction_type" name="CashAccount[transaction_type]" value="Cash"<?php if ($model->transaction_type == "Cash") echo ' checked'; ?>>&nbsp;Cash</label>
                <label for="for_bank" style="margin-left: 15px;"><input type="radio" id="for_bank" class="transaction_type" name="CashAccount[transaction_type]" value="Bank"<?php if ($model->transaction_type == "Bank") echo ' checked'; ?>>&nbsp;Bank</label>
            </div>
            <?php $_dval = $model->transaction_type == "Cash" ? "display:none" : "display:block"; ?>
            <div class="form-group clearfix bankoption" style="<?php echo $_dval; ?>">
                <?php echo $form->labelEx($model, 'Bank', ['class' => 'col-md-6 col-xs-6 text-right']); ?>
                <?php
                $bankList = Bank::model()->getList();
                $bnlist = CHtml::listData($bankList, 'id', 'name');
                echo $form->dropdownList($model, 'bank_id', $bnlist, array('empty' => 'Select', 'class' => 'col-md-6 col-xs-6'));
                ?>
            </div>
            <div class="form-group clearfix bankoption" style="<?php echo $_dval; ?>">
                <?php echo $form->labelEx($model, 'Account', ['class' => 'col-md-6 col-xs-6 text-right']); ?>
                <?php
                if (!empty($model->bank_id)) {
                    $accountList = Account::model()->getList($model->bank_id);
                } else {
                    $accountList = Account::model()->getList();
                }
                $aclist = CHtml::listData($accountList, 'id', 'name');
                echo $form->dropdownList($model, 'account_id', $aclist, array('empty' => 'Select', 'class' => 'col-md-6 col-xs-6'));
                ?>
            </div>
            <div class="form-group clearfix bankoption" style="<?php echo $_dval; ?>">
                <?php echo $form->labelEx($model, 'check_no', ['class' => 'col-md-6 col-xs-6 text-right']); ?>
                <?php echo $form->textField($model, 'check_no', array('class' => 'col-md-6 col-xs-6')); ?>
            </div>
            <div class="form-group clearfix">
                <?php echo $form->labelEx($model, 'by_whom', ['class' => 'col-md-6 col-xs-6 text-right']); ?>
                <?php echo $form->textField($model, 'by_whom', array('class' => 'col-md-6 col-xs-6')); ?>
            </div>
            <div class="form-group clearfix">
                <?php echo $form->labelEx($model, 'purpose', ['class' => 'col-md-6 col-xs-6 text-right']); ?>
                <?php echo $form->textArea($model, 'purpose', array('class' => 'col-md-6 col-xs-6')); ?>
            </div>
            <div class="form-group clearfix">
                <?php echo $form->labelEx($model, 'debit', ['class' => 'col-md-6 col-xs-6 text-right']); ?>
                <?php echo $form->textField($model, 'debit', array('class' => 'col-md-6 col-xs-6', 'required' => 'required')); ?>
            </div>
            <div class="form-group text-center">
                <?php echo CHtml::submitButton($model->isNewRecord ? 'Save' : 'Update', array('class' => 'btn btn-primary', 'style' => 'width:30%')); ?>
            </div>
        </div>
        <?php $this->endWidget(); ?>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        $("#CashAccount_created").datepicker({
            format: 'dd-mm-yyyy'
        });

        $(document).on("change", "#CashAccount_bank_id", function() {
            var _url = baseUrl + '/account/find_list';

            $.post(_url, {bid: $(this).val()}, function(res) {
                if (res.success === true) {
                    $("#CashAccount_account_id").html(res.html);
                } else {
                    $("#CashAccount_account_id").html(res.html);
                }
            }, "json");
        });

        $(document).on("change", ".transaction_type", function() {
            if ($(this).val() == "Bank") {
                $(".bankoption").show();
            } else {
                $(".bankoption").hide();
                document.getElementById('CashAccount_bank_id').selectedIndex = 0;
                document.getElementById('CashAccount_account_id').selectedIndex = 0;
                $("#CashAccount_check_no").val('');
            }
        });
    });
</script>