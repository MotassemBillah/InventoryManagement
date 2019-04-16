<?php if (!empty($dataset) && count($dataset) > 0) : ?>
    <div class="table-responsive">
        <table class="table table-bordered">
            <tr id="r_checkAll">
                <th class="text-center" style="width:5%;"><input type="checkbox" id="checkAll" onclick="toggleCheckboxes(this)"></th>
                <th><?php echo Yii::t('strings', 'Date'); ?></th>
                <th><?php echo Yii::t('strings', 'Head'); ?></th>
                <th><?php echo Yii::t('strings', 'Invoice'); ?></th>
                <th class="text-center"><?php echo Yii::t('strings', 'Actions'); ?></th>
            </tr>
            <?php foreach ($dataset as $data): ?>
                <tr class="pro_cat pro_cat_">
                    <td class="text-center" style="width:5%;"><input type="checkbox" name="data[]" value="<?php echo $data->id; ?>" class="check"></td>
                    <td><?php echo date("j M Y", strtotime($data->pay_date)); ?></td>
                    <td><?php echo $data->head->name; ?></td>
                    <td><?php echo $data->invoice_no; ?></td>
                    <td class="text-center">
                        <!--<a class="btn btn-info btn-xs" href="<?php // echo $this->createUrl(AppUrl::URL_LEDGER_EDIT, array('id' => $data->id));     ?>"><?php // echo Yii::t('strings', 'Edit');     ?></a>-->
                        <a class="btn btn-primary btn-xs" href="<?php echo $this->createUrl(AppUrl::URL_LEDGER_VIEW, array('id' => $data->head_id)); ?>"><?php echo Yii::t('strings', 'View'); ?></a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
<?php else: ?>
    <div class="alert alert-info">No records found!</div>
<?php endif; ?>