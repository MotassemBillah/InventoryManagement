<?php
$this->breadcrumbs = array(
    'Size'
);
?>
<form id="deleteForm" action="<?php echo $this->createUrl(AppUrl::URL_SIZE_DELETEALL); ?>" method="post">
    <div class="input-group">
        <div class="input-group-btn clearfix">
            <input type="text" name="name" class="form-control" placeholder="search by name" style="width: 30%"/>
            <input type="submit" value="<?php echo Yii::t("strings", "Search"); ?>" class="btn btn-warning"/>
            <input type="submit" value="<?php echo Yii::t("strings", "Delete"); ?>" class="btn btn-danger" id="admin_del_btn" disabled="disabled" onclick="return confirm('Are you sure about this action? This cannot be undone!')">
            <button type="button" class="btn btn-success" onclick="redirectTo('<?php echo Yii::app()->createUrl(AppUrl::URL_SIZE_CREATE); ?>')"><i class="fa fa-plus"></i> <?php echo Yii::t("strings", "Size"); ?></button>
        </div>
    </div>
    <?php if (!empty($dataset) && count($dataset) > 0) : ?>
        <div class="table-responsive">
            <table class="table table-bordered">
                <tr id="r_checkAll">
                    <th class="text-center" style="width:5%;"><input type="checkbox" id="checkAll" onclick="toggleCheckboxes(this)"/></th>
                    <th><?php echo Yii::t("strings", "Name"); ?></th>
                    <th><?php echo Yii::t("strings", "Pack Type"); ?></th>
                    <th><?php echo Yii::t("strings", "Pack Size"); ?></th>
                    <th class="text-center" style=""><?php echo Yii::t("strings", "Actions"); ?></th>
                </tr>
                <?php foreach ($dataset as $data): ?>
                    <tr>
                        <td class="text-center" style="width:5%;"><input type="checkbox" name="data[]" value="<?php echo $data->id; ?>" class="check"/></td>
                        <td><?php echo AppHelper::getCleanValue($data->name); ?></td>
                        <td><?php echo AppHelper::getCleanValue($data->packtype); ?></td>
                        <td><?php echo AppHelper::getCleanValue($data->packsize); ?></td>
                        <td class="text-center" style="">
                            <div class="actions">
                                <a class="btn btn-info btn-xs" href="<?php echo $this->createUrl(AppUrl::URL_SIZE_EDIT, array('y' => $data->id)); ?>"><?php echo Yii::t("strings", "Edit"); ?></a>
                                <a class="btn btn-danger btn-xs" href="<?php echo $this->createUrl(AppUrl::URL_SIZE_DELETE, array('y' => $data->id)); ?>" onclick="return confirm('Are you sure about delete? This cannot be undone!');"><?php echo Yii::t("strings", "Delete"); ?></a>
                            </div>
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
                'maxButtonCount' => 4,
                'htmlOptions' => array(
                    'class' => 'pagination',
                )
            ));
            ?>
        </div>
    <?php else: ?>
        <div class="alert alert-info">No records found!</div>
    <?php endif; ?>
</form>