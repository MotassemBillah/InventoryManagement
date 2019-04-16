<?php if (!empty($dataset) && count($dataset) > 0) : ?>
    <div class="table-responsive">
        <table class="table table-striped table-bordered tbl_invoice_view">
            <tr id="r_checkAll">
                <th class="text-center" style="width:4%;"><?php echo Yii::t("strings", "SL#"); ?></th>
                <th><a class="sort" href="javascript://" data-column="name" data-info="DESC"><?php echo Yii::t("strings", "Product Name"); ?></a></th>
                <th><?php echo Yii::t("strings", "Size"); ?></th>
                <th><?php echo Yii::t("strings", "Group"); ?></th>
                <th class="w25"><a class="sort" href="javascript://" data-column="code" data-info="DESC"><?php echo Yii::t("strings", "Code"); ?></a></th>
                <th><a class="sort" href="javascript://" data-column="color" data-info="DESC"><?php echo Yii::t("strings", "Color"); ?></a></th>
                <th><a class="sort" href="javascript://" data-column="grade" data-info="DESC"><?php echo Yii::t("strings", "Grade"); ?></a></th>
                <th class="text-center"><?php echo Yii::t("strings", "Actions"); ?></th>
                <?php if ($this->hasUserAccess('product_delete')): ?>
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
                $unitInfo = !empty($data->unit) ? " [ " . $data->unitsize . " " . $data->unit . " ]" : "";
                ?>
                <tr>
                    <td class="text-center"><?php echo $counter; ?></td>
                    <td><?php echo $data->name . " ( " . AppObject::categoryName($data->category_id) . " )" . $unitInfo; ?></td>
                    <td<?php if (empty($data->size)) echo ' style="background-color:#ebebeb"'; ?>><?php echo!empty($data->size) ? $data->size : "N/A"; ?></td>
                    <td<?php if (empty($data->type)) echo ' style="background-color:#ebebeb"'; ?>><?php echo!empty($data->type) ? AppObject::companyHeadName($data->type) : "N/A"; ?></td>
                    <td class="w25"<?php if (empty($data->code)) echo ' style="background-color:#ebebeb"'; ?>><?php echo!empty($data->code) ? $data->code : "N/A"; ?></td>
                    <td<?php if (empty($data->color)) echo ' style="background-color:#ebebeb"'; ?>><?php echo!empty($data->color) ? ucfirst($data->color) : "N/A"; ?></td>
                    <td<?php if (empty($data->grade)) echo ' style="background-color:#ebebeb"'; ?>><?php echo!empty($data->grade) ? $data->grade : "N/A"; ?></td>
                    <td class="text-center">
                        <?php if ($this->hasUserAccess('product_edit')): ?>
                            <a class="btn btn-info btn-xs" href="<?php echo $this->createUrl(AppUrl::URL_PRODUCT_EDIT, array('id' => $data->_key)); ?>"><?php echo Yii::t("strings", "Edit"); ?></a>
                        <?php endif; ?>
                    </td>
                    <?php if ($this->hasUserAccess('product_delete')): ?>
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