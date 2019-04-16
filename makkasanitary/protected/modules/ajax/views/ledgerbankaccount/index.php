<?php if (!empty($dataset) && count($dataset) > 0) : ?>
    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <tr id="r_checkAll">
                <?php if ($this->hasUserAccess('account_delete')): ?>
                    <th class="text-center" style="width:5%;"><input type="checkbox" id="checkAll" onclick="toggleCheckboxes(this)"></th>
                <?php endif; ?>
                <th><?php echo Yii::t("strings", "Bank Name"); ?></th>
                <th><?php echo Yii::t("strings", "Account Name"); ?></th>
                <th><?php echo Yii::t("strings", "Account Number"); ?></th>
                <th><?php echo Yii::t("strings", "Account Type"); ?></th>
                <th class="text-center"><?php echo Yii::t("strings", "Actions"); ?></th>
            </tr>
            <?php foreach ($dataset as $data): ?>
                <tr>
                    <?php if ($this->hasUserAccess('account_delete')): ?>
                        <td class="text-center" style="width:5%;"><input type="checkbox" name="data[]" value="<?php echo $data->id; ?>" class="check"/></td>
                    <?php endif; ?>
                    <td><?php echo AppObject::getBankName($data->bank_id); ?></td>
                    <td><?php echo AppHelper::getCleanValue($data->account_name); ?></td>
                    <td><?php echo AppHelper::getCleanValue($data->account_number); ?></td>
                    <td><?php echo AppHelper::getCleanValue($data->account_type); ?></td>
                    <td class="text-center">
                        <?php if ($this->hasUserAccess('account_edit')): ?>
                            <a class="btn btn-info btn-xs" href="<?php echo $this->createUrl(AppUrl::URL_ACCOUNT_EDIT, array('id' => $data->_key)); ?>"><?php echo Yii::t("strings", "Edit"); ?></a>
                        <?php endif; ?>
                        <?php if ($this->hasUserAccess('account_balance')): ?>
                            <a class="btn btn-primary btn-xs" href="<?php echo $this->createUrl(AppUrl::URL_ACCOUNT_BALANCE, array('id' => $data->id)); ?>"><?php echo Yii::t('strings', 'Balance'); ?></a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
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
            'prevPageLabel' => '< ',
            'selectedPageCssClass' => 'active ',
            'hiddenPageCssClass' => 'disabled ',
            'maxButtonCount' => 5,
            'htmlOptions' => array(
                'class' => 'pagination',
            )
        ));
        ?>
    </div>
<?php else: ?>
    <div class="alert alert-info">No records found!</div>
<?php endif; ?>