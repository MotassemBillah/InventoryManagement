<?php
$this->breadcrumbs = array(
    'Customer' => array(AppUrl::URL_CUSTOMER),
    'Message'
);
?>
<div class="well">
    <table width="100%">
        <tr>
            <td class="wmd_70">
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
                            <select id="status" class="form-control" name="status" style="width: auto;">
                                <option value="All">All</option>
                                <option value="<?php echo AppConstant::CONTACT_PENDING; ?>"><?php echo AppConstant::CONTACT_PENDING; ?></option>
                                <option value="<?php echo AppConstant::CONTACT_PROGRESS; ?>"><?php echo AppConstant::CONTACT_PROGRESS; ?></option>
                                <option value="<?php echo AppConstant::CONTACT_SOLVED; ?>"><?php echo AppConstant::CONTACT_SOLVED; ?></option>
                            </select>
                            <input type="text" name="q" id="q" class="form-control" placeholder="search by name" size="30"/>
                            <button type="button" id="search" class="btn btn-info">
                                <?php echo Yii::t("strings", "Search"); ?>&nbsp;<img class="ajaxLoader" src="<?php echo Yii::app()->request->baseUrl; ?>/img/loading.gif" alt="Loading..." style="display: none;">
                            </button>
                        </div>
                    </div>
                </form>
            </td>
            <td class="text-right wmd_30" style="">
                <?php if (in_array(Yii::app()->user->role, array(AppConstant::ROLE_SUPERADMIN))): ?>
                    <button type="button" class="btn btn-danger" id="admin_del_btn" disabled="disabled" >
                        <?php echo Yii::t("strings", "Delete"); ?>&nbsp;<img class="ajaxLoader" src="<?php echo Yii::app()->request->baseUrl; ?>/img/loading.gif" alt="Loading..." style="display: none;">
                    </button>
                <?php endif; ?>
            </td>
        </tr>
    </table>
</div>

<form id="deleteForm" action="" method="post">
    <div id="ajaxContent">
        <?php if (!empty($dataset) && count($dataset) > 0) : ?>
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <tr id="r_checkAll">
                        <?php if (in_array(Yii::app()->user->role, array(AppConstant::ROLE_SUPERADMIN))): ?>
                            <th class="text-center" style="width:5%;"><input type="checkbox" id="checkAll" onclick="toggleCheckboxes(this)"/></th>
                        <?php endif; ?>
                        <th><?php echo Yii::t('strings', 'Name'); ?></th>
                        <th><?php echo Yii::t('strings', 'Email'); ?></th>
                        <th><?php echo Yii::t('strings', 'Phone'); ?></th>
                        <th><?php echo Yii::t('strings', 'Subject'); ?></th>
                        <th><?php echo Yii::t('strings', 'Message'); ?></th>
                        <th class="text-center"><?php echo Yii::t('strings', 'Status'); ?></th>
                        <th class="text-center"><?php echo Yii::t('strings', 'Actions'); ?></th>
                    </tr>
                    <?php foreach ($dataset as $data): ?>
                        <tr class="pro_cat pro_cat_<?php echo strtolower($data->id); ?>">
                            <?php if (in_array(Yii::app()->user->role, array(AppConstant::ROLE_SUPERADMIN))): ?>
                                <td class="text-center" style="width:5%;"><input type="checkbox" name="data[]" value="<?php echo $data->id; ?>" class="check"/></td>
                            <?php endif; ?>
                            <td><?php echo AppHelper::getCleanValue($data->name); ?></td>
                            <td><?php echo AppHelper::getCleanValue($data->email); ?></td>
                            <td><?php echo AppHelper::getCleanValue($data->phone); ?></td>
                            <td><?php echo AppHelper::getCleanValue($data->subject); ?></td>
                            <td><?php echo AppHelper::limitText($data->message, 40); ?></td>
                            <td class="text-center">
                                <?php
                                if ($data->status == AppConstant::CONTACT_PENDING) {
                                    $btn_class = "label label-info btn-xs";
                                } elseif ($data->status == AppConstant::CONTACT_PROGRESS) {
                                    $btn_class = "label label-primary btn-xs";
                                } else {
                                    $btn_class = "label label-success btn-xs";
                                }
                                echo "<span class='{$btn_class}'>" . Yii::t('strings', $data->status) . "</span>";
                                ?>
                            </td>
                            <td class="text-center">
                                <a class="btn btn-info btn-xs" href="<?php echo $this->createUrl(AppUrl::URL_CUSTOMER_MESSAGE_VIEW, array('id' => $data->key)); ?>"><?php echo Yii::t('strings', 'View'); ?></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <tr class="well">
                        <?php if (in_array(Yii::app()->user->role, array(AppConstant::ROLE_SUPERADMIN))): ?>
                            <td colspan="8" class="well"><?php echo $countText; ?></td>
                        <?php else: ?>
                            <td colspan="7" class="well"><?php echo $countText; ?></td>
                        <?php endif; ?>
                    </tr>
                </table>
            </div>

            <div class="paging">
                <?php
                $this->widget('CLinkPager', array(
                    'pages' => $pages,
                    'header' => ' ',
                    'firstPageLabel' => '<< First',
                    'lastPageLabel' => 'Last >>',
                    'nextPageLabel' => 'Next > ',
                    'prevPageLabel' => '< Prev',
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
    </div>
</form>
<script type="text/javascript">
    $(document).ready(function() {
        $(document).on("click", "#admin_del_btn", function(e) {
            var _rc = confirm('Are you sure about this action? This cannot be undone!');

            if (_rc === true) {
                showLoader("Processing...", true);
                var _form = $("#deleteForm");
                var _url = ajaxUrl + '/customer/message_deleteall';
                $.post(_url, _form.serialize(), function(res) {
                    if (res.success === true) {
                        $("#ajaxMessage").removeClass('alert-danger').addClass('alert-success').html("");
                        $("#ajaxMessage").html(res.message).show();
                        $("tr.bg-danger").remove();
                        setTimeout(hide_ajax_message, 3000);
                    } else {
                        $("#ajaxMessage").removeClass('alert-success').addClass('alert-danger').html("");
                        $("#ajaxMessage").html(res.message).show();
                    }
                    showLoader("", false);
                }, "json");
            } else {
                return false;
            }
            e.preventDefault();
        });
    });
</script>