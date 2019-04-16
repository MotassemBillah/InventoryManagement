<?php if (!empty($dataset) && count($dataset) > 0) : ?>
    <div class="table-responsive">
        <table class="table table-striped table-bordered tbl_invoice_view">
            <tr id="r_checkAll">
                <th class="text-center" style="width:4%;"><?php echo Yii::t('strings', 'SL#'); ?></th>
                <th><?php echo Yii::t('strings', 'Name'); ?></th>
                <th><?php echo Yii::t('strings', 'Company'); ?></th>
                <th><?php echo Yii::t('strings', 'Email'); ?></th>
                <th><?php echo Yii::t('strings', 'Phone'); ?></th>
                <th><?php echo Yii::t('strings', 'Address'); ?></th>
                <th class="text-center dis_print"><?php echo Yii::t('strings', 'Actions'); ?></th>
                <th class="text-right"><?php echo Yii::t('strings', 'Balance'); ?></th>
                <th class="text-center dis_print" style="width:3%;">
                    <?php if ($this->hasUserAccess('customer_delete')): ?>
                        <input type="checkbox" id="checkAll" onclick="toggleCheckboxes(this)">
                    <?php endif; ?>
                </th>
            </tr>
            <?php
            $counter = 0;
            if (isset($_GET['page']) && $_GET['page'] > 1) {
                $counter = ($_GET['page'] - 1) * $pages->pageSize;
            }
            foreach ($dataset as $data):
                $counter++;
                $_cbalance = AppObject::sumBalanceAmount($data->id);
                ?>
                <tr class="pro_cat pro_cat_">
                    <td class="text-center"><?php echo $counter; ?></td>
                    <td><?php echo AppHelper::getCleanValue($data->name); ?></td>
                    <td><?php echo AppHelper::getCleanValue($data->company); ?></td>
                    <td><?php echo AppHelper::getCleanValue($data->email); ?></td>
                    <td><?php echo AppHelper::getCleanValue($data->phone); ?></td>
                    <td><?php echo AppHelper::getCleanValue($data->address); ?></td>
                    <td class="text-center dis_print">
                        <?php if ($this->hasUserAccess('customer_edit')): ?>
                            <a class="btn btn-info btn-xs" href="<?php echo $this->createUrl(AppUrl::URL_CUSTOMER_EDIT, array('id' => $data->_key)); ?>"><?php echo Yii::t('strings', 'Edit'); ?></a>
                        <?php endif; ?>
                        <?php if ($this->hasUserAccess('customer_payment')): ?>
                            <a class="btn btn-primary btn-xs" href="<?php echo $this->createUrl(AppUrl::URL_CUSTOMER_PAYMENT, array('id' => $data->_key)); ?>" target="_self"><?php echo Yii::t('strings', 'Payments'); ?></a>
                        <?php endif; ?>
                    </td>
                    <td class="text-right<?php echo!empty($_cbalance) ? (($_cbalance > 0) ? " color_green" : " color_red") : " bg_gray"; ?>"><?php echo!empty($_cbalance) ? $_cbalance : ""; ?></td>
                    <td class="text-center dis_print">
                        <?php if ($this->hasUserAccess('customer_delete')): ?>
                            <input type="checkbox" name="data[]" value="<?php echo $data->id; ?>" class="check">
                        <?php endif; ?>
                    </td>
                </tr>
                <?php
                $sumCbalance[] = $_cbalance;
            endforeach;
            ?>
            <tr class="bg_gray">
                <th colspan="6" class="text-right">Total</th>
                <th class="dis_print"></th>
                <th class="text-right<?php echo (array_sum($sumCbalance) > 0) ? " color_green" : " color_red"; ?>"><?php echo AppHelper::getFloat(array_sum($sumCbalance)); ?></th>
                <th class="dis_print"></th>
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