<?php if (!empty($dataset) && count($dataset) > 0): ?>
    <div class="table-responsive">
        <table class="table table-striped table-bordered tbl_invoice_view">
            <tr id="r_checkAll">
                <th class="text-center" style="width:4%;"><?php echo Yii::t("strings", "SL#"); ?></th>
                <th><?php echo Yii::t("strings", "Date"); ?></th>
                <th><?php echo Yii::t("strings", "Customer"); ?></th>
                <th><?php echo Yii::t("strings", "Sales Person"); ?></th>
                <th class="text-center"><?php echo Yii::t("strings", "Items"); ?></th>
                <th class="text-center"><?php echo Yii::t("strings", "Status"); ?></th>
                <th class="text-center"><?php echo Yii::t("strings", "Actions"); ?></th>
                <?php if ($this->hasUserAccess('sale_delete') || $this->hasUserAccess('reset_invoice')): ?>
                    <th class="text-center" style="width:3%;"><input type="checkbox" id="checkAll" onclick="toggleCheckboxes(this)"></th>
                <?php endif; ?>
            </tr>
            <?php
            $counter = 0;
            if (isset($_GET['page']) && $_GET['page'] > 1) {
                $counter = ($_GET['page'] - 1) * $pages->pageSize;
            }
            foreach ($dataset as $data) :
                $counter++;
                ?>
                <tr id="row_<?php echo $data->id; ?>">
                    <td class="text-center"><?php echo $counter; ?></td>
                    <td><?php echo date('j M Y', strtotime($data->created)); ?></td>
                    <td><?php echo AppObject::customerName($data->customer_id); ?></td>
                    <td><?php echo AppObject::displayNameByUser($data->created_by); ?></td>
                    <td class="text-center"><?php echo count($data->items); ?></td>
                    <td class="text-center">
                        <?php
                        if ($data->status == AppConstant::ORDER_PENDING) {
                            echo '<span class="label label-danger">' . Yii::t("strings", $data->status) . '</span>';
                        } else {
                            echo '<span class="label label-success">' . Yii::t("strings", AppConstant::ORDER_COMPLETE) . '</span>';
                        }
                        ?>
                    </td>
                    <td class="text-center">
                        <?php if ($data->status == AppConstant::ORDER_PENDING): ?>
                            <?php if ($this->hasUserAccess('sale_edit')): ?>
                                <a class="btn btn-info btn-xs" href="<?php echo $this->createUrl(AppUrl::URL_SALE_EDIT, array('id' => $data->_key)); ?>"><?php echo Yii::t("strings", "Edit"); ?></a>
                            <?php endif; ?>
                            <?php if ($this->hasUserAccess('sale_process')): ?>
                                <a class="btn btn-primary btn-xs process_order" href="javascript://" data-info="<?php echo $data->_key; ?>"><?php echo Yii::t("strings", "Process"); ?></a>
                            <?php endif; ?>
                        <?php endif; ?>
                        <?php if ($this->hasUserAccess('sale_view')): ?>
                            <a class="btn btn-primary btn-xs" href="<?php echo $this->createUrl(AppUrl::URL_SALE_VIEW, array('id' => $data->invoice_no)); ?>"><?php echo Yii::t("strings", "View"); ?></a>
                        <?php endif; ?>
                    </td>
                    <?php if ($this->hasUserAccess('sale_delete') || $this->hasUserAccess('reset_invoice')): ?>
                        <td class="text-center"><input type="checkbox" name="data[]" value="<?php echo $data->id; ?>" class="check"></td>
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
            'nextPageLabel' => '> ',
            'prevPageLabel' => '< ',
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