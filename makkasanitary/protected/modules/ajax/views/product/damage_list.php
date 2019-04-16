<?php if (!empty($dataset) && count($dataset) > 0) : ?>
    <div class="table-responsive">
        <table class="table table-striped table-bordered tbl_invoice_view">
            <tr id="r_checkAll">
                <?php if (in_array(Yii::app()->user->role, array(AppConstant::ROLE_SUPERADMIN))): ?>
                    <th class="text-center" style="width:4%;"><input type="checkbox" id="checkAll" onclick="toggleCheckboxes(this)"/></th>
                <?php else: ?>
                    <th class="text-center" style="width:4%;"><?php echo Yii::t("strings", "SL#"); ?></th>
                <?php endif; ?>
                <th><?php echo Yii::t("strings", "Product Name"); ?></th>
                <th><?php echo Yii::t("strings", "Head"); ?></th>
                <th><?php echo Yii::t("strings", "Code"); ?></th>
                <th><?php echo Yii::t("strings", "Color"); ?></th>
                <th><?php echo Yii::t("strings", "Grade"); ?></th>
                <th class="text-center"><?php echo Yii::t("strings", "Actions"); ?></th>
            </tr>
            <?php
            $counter = 0;
            foreach ($dataset as $data):
                $counter++;
                ?>
                <tr>
                    <?php if (in_array(Yii::app()->user->role, array(AppConstant::ROLE_SUPERADMIN))): ?>
                        <td class="text-center"><input type="checkbox" name="data[]" value="<?php echo $data->product->id; ?>" class="check"/></td>
                    <?php else: ?>
                        <td class="text-center"><?php echo $counter; ?></td>
                    <?php endif; ?>
                    <td>
                        <?php
                        if (!empty($highlight)) {
                            $text = "<span class='highlight'>" . $highlight . "</span>";
                            $productName = str_replace($highlight, $text, strtolower($data->product->name));
                        } else {
                            $productName = AppHelper::getCleanValue($data->product->name);
                        }
                        echo $productName . " ( " . AppObject::categoryName($data->product->category_id) . " )";
                        ?>
                    </td>
                    <td<?php if (empty($data->product->type)) echo ' style="background-color:#ebebeb"'; ?>><?php echo!empty($data->product->type) ? AppObject::companyHeadName($data->product->type) : "N/A"; ?></td>
                    <td<?php if (empty($data->product->code)) echo ' style="background-color:#ebebeb"'; ?>><?php echo!empty($data->product->code) ? $data->product->code : "N/A"; ?></td>
                    <td<?php if (empty($data->product->color)) echo ' style="background-color:#ebebeb"'; ?>><?php echo!empty($data->product->color) ? ucfirst($data->product->color) : "N/A"; ?></td>
                    <td<?php if (empty($data->product->grade)) echo ' style="background-color:#ebebeb"'; ?>><?php echo!empty($data->product->grade) ? $data->product->grade : "N/A"; ?></td>
                    <td class="text-center">
                        <?php if ($this->hasUserAccess('product_edit')): ?>
                            <a class="btn btn-info btn-xs" href="<?php echo $this->createUrl(AppUrl::URL_PRODUCT_EDIT, array('id' => $data->product->_key)); ?>"><?php echo Yii::t("strings", "Edit"); ?></a>
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