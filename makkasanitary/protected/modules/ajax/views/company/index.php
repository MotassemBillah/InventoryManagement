<?php if (!empty($dataset) && count($dataset) > 0) : ?>
    <div class="table-responsive">
        <table class="table table-striped table-bordered tbl_invoice_view">
            <tr id="r_checkAll">
                <th class="text-center" style="width:4%;"><?php echo Yii::t('strings', 'SL#'); ?></th>
                <th><?php echo Yii::t('strings', 'Name'); ?></th>
                <th><?php echo Yii::t('strings', 'Email'); ?></th>
                <th><?php echo Yii::t('strings', 'Contacts'); ?></th>
                <th style="width: 24%;"><?php echo Yii::t('strings', 'Address'); ?></th>
                <th class="text-center"><?php echo Yii::t('strings', 'Actions'); ?></th>
                <?php if ($this->hasUserAccess('company_delete')): ?>
                    <th class="text-center" style="width:3%;"><input type="checkbox" id="checkAll" onclick="toggleCheckboxes(this)"/></th>
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
                <tr>
                    <td class="text-center"><?php echo $counter; ?></td>
                    <td><?php echo AppHelper::getCleanValue($data->name); ?></td>
                    <td><?php echo AppHelper::getCleanValue($data->email); ?></td>
                    <td>
                        <?php
                        echo "<u>Phone</u>: " . $data->phone;
                        if (!empty($data->mobile)) {
                            echo "<br>";
                            echo "<u>Mobile</u>: " . $data->mobile;
                        }
                        if (!empty($data->other_contacts)) {
                            echo "<br>";
                            echo "<u>Other Contacts</u>: " . $data->other_contacts;
                        }
                        ?>
                    </td>
                    <td><?php echo AppHelper::getCleanValue($data->address); ?></td>
                    <td class="text-center">
                        <?php if ($this->hasUserAccess('company_edit')): ?>
                            <a class="btn btn-info btn-xs" href="<?php echo $this->createUrl(AppUrl::URL_COMPANY_EDIT, array('id' => $data->_key)); ?>"><?php echo Yii::t('strings', 'Edit'); ?></a>
                        <?php endif; ?>
                        <a class="btn btn-success btn-xs detail" href="javascript:void(0);" data-info="<?php echo $data->_key; ?>"><?php echo Yii::t('strings', 'View'); ?></a>
                        <?php if ($this->hasUserAccess('customer_payment')): ?>
                            <a class="btn btn-primary btn-xs" href="<?php echo $this->createUrl(AppUrl::URL_COMPANY_PAYMENT, array('id' => $data->_key)); ?>" target="_blank"><?php echo Yii::t('strings', 'Payments'); ?></a>
                        <?php endif; ?>
                    </td>
                    <?php if ($this->hasUserAccess('company_delete')): ?>
                        <td class="text-center"><input type="checkbox" name="data[]" value="<?php echo $data->id; ?>" class="check"/></td>
                    <?php endif; ?>
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
            'nextPageLabel' => '>',
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
    <div class="alert alert-info">No records found!</div>
<?php endif; ?>