<?php
$this->breadcrumbs = array(
    'Location'
);
?>
<form id="deleteForm" action="<?php echo $this->createUrl(AppUrl::URL_LOCATION_DELETEALL); ?>" method="post">
    <div class="input-group">
        <div class="input-group-btn clearfix">
            <input type="text" name="name" class="form-control" placeholder="search by name" style="width: 30%"/>
            <input type="submit" value="<?php echo Yii::t("strings", "Search"); ?>" class="btn btn-warning"/>
            <?php if ($this->hasUserAccess('location_delete')): ?>
                <input type="submit" value="<?php echo Yii::t("strings", "Delete"); ?>" class="btn btn-danger" id="admin_del_btn" disabled="disabled" onclick="return confirm('Are you sure about this action? This cannot be undone!')">
            <?php endif; ?>
            <button type="button" class="btn btn-success" onclick="redirectTo('<?php echo Yii::app()->createUrl(AppUrl::URL_LOCATION_CREATE); ?>')"><i class="fa fa-plus"></i> <?php echo Yii::t("strings", "New"); ?></button>
        </div>
    </div>
    <?php if (!empty($dataset) && count($dataset) > 0) : ?>
        <div class="table-responsive">
            <table class="table table-bordered">
                <tr id="r_checkAll">
                    <?php if ($this->hasUserAccess('location_delete')): ?>
                        <th class="text-center" style="width:5%;"><input type="checkbox" id="checkAll" onclick="toggleCheckboxes(this)"/></th>
                    <?php endif; ?>
                    <th><?php echo Yii::t("strings", "Location"); ?></th>
                    <th class="text-center" style=""><?php echo Yii::t("strings", "Actions"); ?></th>
                </tr>
                <?php foreach ($dataset as $data): ?>
                    <tr>
                        <?php if ($this->hasUserAccess('location_delete')): ?>
                            <td class="text-center" style="width:5%;"><input type="checkbox" name="data[]" value="<?php echo $data->location_id; ?>" class="check"/></td>
                        <?php endif; ?>
                        <td><?php echo AppHelper::getCleanValue($data->location_name); ?></td>
                        <td class="text-center" style="">
                            <div class="actions">
                                <?php if ($this->hasUserAccess('location_edit')): ?>
                                    <a class="btn btn-info btn-xs" href="<?php echo $this->createUrl(AppUrl::URL_LOCATION_EDIT . '/?key=' . $data->location_key); ?>"><?php echo Yii::t("strings", "Edit"); ?></a>
                                <?php endif; ?>
                                <?php if ($this->hasUserAccess('location_delete')): ?>
                                    <a class="btn btn-danger btn-xs" href="<?php echo $this->createUrl(AppUrl::URL_LOCATION_DELETE . '/?key=' . $data->location_key); ?>" onclick="return confirm('Are you sure about delete? This cannot be undone!');"><?php echo Yii::t("strings", "Delete"); ?></a>
                                <?php endif; ?>
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
</form>