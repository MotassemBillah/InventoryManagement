<form id="deleteForm" action="<?php echo $this->createUrl(AppUrl::URL_ROLE_DELETEALL); ?>" method="post">
    <div class="well">
        <table width="100%">
            <tr>
                <td style="width: 70%">
                    <div class="input-group">
                        <div class="input-group-btn clearfix">
                            <input type="text" name="q" id="q" class="form-control" placeholder="search name or email" size="30"/>
                            <button type="button" id="search" class="btn btn-info"><?php echo Yii::t("strings", "Search"); ?></button>
                        </div>
                    </div>
                </td>
                <td class="text-right" style="width: 30%">
                    <a class="btn btn-success" href="<?php echo Yii::app()->createUrl(AppUrl::URL_ROLE_CREATE); ?>"><i class="fa fa-plus"></i>&nbsp;<?php echo Yii::t("strings", "New"); ?></a>
                    <?php if (in_array(Yii::app()->user->role, array(AppConstant::ROLE_SUPERADMIN))): ?>
                        <button type="submit" class="btn btn-danger" id="admin_del_btn" disabled="disabled" onclick="return confirm('Are you sure about this action? This cannot be undone!')"><?php echo Yii::t("strings", "Delete"); ?></button>
                    <?php endif; ?>
                </td>
            </tr>
        </table>
    </div>
    <?php if (!empty($dataset) && count($dataset) > 0) : ?>
        <div class="table-responsive">
            <table class="table table-bordered">
                <tr id="r_checkAll">
                    <th class="text-center" style="width:5%;"><input type="checkbox" id="checkAll" onclick="toggleCheckboxes(this)"/></th>
                    <th>Name</th>
                    <th>Status</th>
                    <th class="text-center">Actions</th>
                </tr>
                <?php foreach ($dataset as $data): ?>
                    <tr>
                        <td class="text-center" style="width:5%;"><input type="checkbox" name="data[]" value="<?php echo $data->id; ?>" class="check"/></td>
                        <td><?php echo AppHelper::getCleanValue($data->name); ?></td>
                        <td><?php echo ($data->is_deleted != 1) ? "Active" : "Deleted"; ?></td>
                        <td class="text-center">
                            <a class="btn btn-info btn-xs" href="<?php echo $this->createUrl(AppUrl::URL_ROLE_EDIT . '/?key=' . $data->_key); ?>">Edit</a>
                            <?php if (in_array(Yii::app()->user->role, array(AppConstant::ROLE_SUPERADMIN))): ?>
                                <?php if ($data->is_deleted == 1) : ?>
                                    <a class="btn btn-warning btn-xs" href="<?php echo $this->createUrl(AppUrl::URL_ROLE_ACTIVATE . '/?key=' . $data->role_key); ?>">Activate</a>
                                <?php endif; ?>
                            <?php endif; ?>
                            <a class="btn btn-danger btn-xs" href="<?php echo $this->createUrl(AppUrl::URL_ROLE_DELETE . '/?key=' . $data->_key); ?>" onclick="return confirm('Are you sure about delete? This cannot be undone!');">Delete</a>
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