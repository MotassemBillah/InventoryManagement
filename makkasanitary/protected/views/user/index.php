<?php $this->breadcrumbs = array('Users'); ?>
<div class="well">
    <table width="100%">
        <tr>
            <td class="wmd_70 wxs_100">
                <form class="search-form" method="post" name="frmSearch" id="frmSearch">
                    <div class="input-group">
                        <div class="input-group-btn clearfix">
                            <select id="itemCount" class="form-control" name="itemCount" style="width:55px;">
                                <?php
                                for ($i = 10; $i <= 100; $i+=10) {
                                    if ($i == $this->settings->page_size) {
                                        echo "<option value='{$i}' selected='selected'>{$i}</option>";
                                    } else {
                                        echo "<option value='{$i}'>{$i}</option>";
                                    }
                                }
                                ?>
                            </select>
                            <input type="text" name="q" id="q" class="form-control" placeholder="search name or email" size="30"/>
                            <button type="button" id="search" class="btn btn-info"><?php echo Yii::t("strings", "Search"); ?></button>
                        </div>
                    </div>
                </form>
            </td>
            <td class="text-right wmd_30 wxs_100">
                <a class="btn btn-success btn-xs" href="<?php echo Yii::app()->createUrl(AppUrl::URL_USER_CREATE); ?>"><i class="fa fa-plus"></i>&nbsp;<?php echo Yii::t("strings", "New"); ?></a>
                <?php if ($this->hasUserAccess('user_delete')): ?>
                    <button type="button" class="btn btn-danger btn-xs" id="admin_del_btn" disabled="disabled" ><i class="fa fa-trash-o"></i>&nbsp;<?php echo Yii::t("strings", "Delete"); ?></button>
                <?php endif; ?>
            </td>
        </tr>
    </table>
</div>
<form id="deleteForm" action="" method="post">
    <div id="ajaxContent">
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
                    )
                ));
                ?>
            </div>
        <?php else: ?>
            <div class="alert alert-info">No records found!</div>
        <?php endif; ?>
    </div>
</form>
<script type="text/javascript">
    $(document).ready(function() {
        $(document).on("click", "#search", function(e) {
            showLoader("Processing...", true);
            var _form = $("#frmSearch");

            $.ajax({
                type: "POST",
                url: baseUrl + "/user/search",
                data: _form.serialize(),
                success: function(res) {
                    showLoader("", false);
                    $("#ajaxContent").html('');
                    $("#ajaxContent").html(res);
                }
            });
            e.preventDefault();
        });

        $(document).on('click', '#admin_del_btn', function(e) {
            var _rc = confirm('Are you sure about this action? This cannot be undone!');

            if (_rc === true) {
                showLoader("Processing...", true);
                var _form = $("#deleteForm");
                var _url = baseUrl + '/user/deleteall';

                $.post(_url, _form.serialize(), function(res) {
                    if (res.success === true) {
                        $("#ajaxMessage").showAjaxMessage({html: res.message, type: 'success'});
                        $("#search").trigger('click');
                    } else {
                        $("#ajaxMessage").showAjaxMessage({html: res.message, type: 'error'});
                    }
                    reset_index();
                    showLoader("", false);
                }, "json");
            } else {
                return false;
            }
            e.preventDefault();
        });
    });
</script>