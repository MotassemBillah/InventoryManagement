<?php if (!empty($dataset) && count($dataset) > 0) : ?>
    <div class="table-responsive">
        <table class="table table-striped table-bordered tbl_invoice_view">
            <tr id="r_checkAll">
                <th class="text-center" style="width:4%;"><?php echo Yii::t('strings', 'SL#'); ?></th>
                <th><?php echo Yii::t('strings', 'Username'); ?></th>
                <th><?php echo Yii::t('strings', 'Email'); ?></th>
                <th class="text-center"><?php echo Yii::t('strings', 'Status'); ?></th>
                <th class="text-center"><?php echo Yii::t('strings', 'Loggedin'); ?></th>
                <th class="text-center" style="width:15%;"><?php echo Yii::t('strings', 'Actions'); ?></th>
                <th class="text-center" style="width:3%;"><input type="checkbox" id="checkAll" onclick="toggleCheckboxes(this)"></th>
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
                    <td><?php echo AppHelper::getCleanValue($data->login); ?></td>
                    <td><?php echo AppHelper::getCleanValue($data->email); ?></td>
                    <td class="text-center">
                        <?php
                        $userStatus = AppObject::userStatus($data->status);
                        if ($userStatus == "Active") {
                            $btn_class = "btn btn-success btn-xs";
                            $_link = $this->createUrl(AppUrl::URL_USER_DEACTIVATE, array('id' => $data->_key));
                        } else {
                            $btn_class = "btn btn-warning btn-xs";
                            $_link = $this->createUrl(AppUrl::URL_USER_ACTIVATE, array('id' => $data->_key));
                        }
                        if ($this->hasUserAccess('user_activate')):
                            echo "<a class='{$btn_class}' href='{$_link}'>" . Yii::t('strings', $userStatus) . "</a>";
                        else:
                            echo "<span class='{$btn_class}'>" . Yii::t('strings', $userStatus) . "</span>";
                        endif;
                        ?>
                    </td>
                    <td class="text-center"><?php echo ($data->is_loggedin == 1) ? "Yes" : "No"; ?></td>
                    <td class="text-center">
                        <?php if ($this->hasUserAccess('admin_user_edit')): ?>
                            <a class="btn btn-info btn-xs" href="<?php echo $this->createUrl(AppUrl::URL_USER_ADMIN_EDIT, array('id' => $data->_key)); ?>"><?php echo Yii::t('strings', 'Edit'); ?></a>
                        <?php endif; ?>
                        <a class="btn btn-primary btn-xs" href="<?php echo $this->createUrl(AppUrl::URL_USER_PROFILE, array('id' => $data->_key)); ?>"><?php echo Yii::t('strings', 'View'); ?></a>
                        <?php if ($this->hasUserAccess('access_control') && $data->deletable == 1): ?>
                            <a class="btn btn-warning btn-xs" href="<?php echo $this->createUrl(AppUrl::URL_USER_PERMISSION, array('id' => $data->_key)); ?>"><?php echo Yii::t('strings', 'Access Control'); ?></a>
                        <?php endif; ?>
                    </td>
                    <td class="text-center">
                        <?php if ($this->hasUserAccess('user_delete')): ?>
                            <input type="checkbox" name="data[]" value="<?php echo $data->id; ?>" class="check">
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