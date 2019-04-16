<?php
$this->breadcrumbs = array(
    'Account'
);
?>
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
                            <input type="text" name="q" id="q" class="form-control" placeholder="search by name" size="30">
                            <button type="button" id="search" class="btn btn-info"><?php echo Yii::t("strings", "Search"); ?></button>
                        </div>
                    </div>
                </form>
            </td>
            <td class="text-right wmd_30" style="position: relative;">
                <a class="btn btn-success btn-xs" href="<?php echo Yii::app()->createUrl(AppUrl::URL_ACCOUNT_CREATE); ?>"><i class="fa fa-plus"></i>&nbsp;<?php echo Yii::t("strings", "New"); ?></a>
                <?php if ($this->hasUserAccess('account_delete')): ?>
                    <button type="button" class="btn btn-danger btn-xs" id="admin_del_btn" disabled="disabled"><i class="fa fa-trash-o"></i>&nbsp;<?php echo Yii::t("strings", "Delete"); ?></button>
                <?php endif; ?>
            </td>
        </tr>
    </table>
</div>
<form id="deleteForm" action="" method="post">
    <div id="ajaxContent">
        <?php if (!empty($dataset) && count($dataset) > 0) : ?>
            <div class="table-responsive">
                <table class="table table-bordered table-striped tbl_invoice_view">
                    <tr id="r_checkAll">
                        <th class="text-center" style="width:4%;"><?php echo Yii::t("strings", "#SL"); ?></th>
                        <th><?php echo Yii::t("strings", "Bank Name"); ?></th>
                        <th><?php echo Yii::t("strings", "Account Name"); ?></th>
                        <th><?php echo Yii::t("strings", "Account Number"); ?></th>
                        <th><?php echo Yii::t("strings", "Account Type"); ?></th>
                        <th class="text-center"><?php echo Yii::t("strings", "Actions"); ?></th>
                        <th class="text-center" style="width:3%;">
                            <?php if ($this->hasUserAccess('account_delete')): ?>
                                <input type="checkbox" id="checkAll" onclick="toggleCheckboxes(this)">
                            <?php endif; ?>
                        </th>
                    </tr>
                    <?php
                    $counter = 0;
                    foreach ($dataset as $data):
                        $counter++;
                        ?>
                        <tr>
                            <td class="text-center"><?php echo $counter; ?></td>
                            <td><?php echo AppObject::getBankName($data->bank_id); ?></td>
                            <td><?php echo AppHelper::getCleanValue($data->account_name); ?></td>
                            <td><?php echo AppHelper::getCleanValue($data->account_number); ?></td>
                            <td><?php echo AppHelper::getCleanValue($data->account_type); ?></td>
                            <td class="text-center">
                                <?php if ($this->hasUserAccess('account_edit')): ?>
                                    <a class="btn btn-info btn-xs" href="<?php echo $this->createUrl(AppUrl::URL_ACCOUNT_EDIT, array('id' => $data->_key)); ?>"><?php echo Yii::t("strings", "Edit"); ?></a>
                                <?php endif; ?>
                                <?php if ($this->hasUserAccess('account_balance')): ?>
                                    <a class="btn btn-primary btn-xs" href="<?php echo $this->createUrl(AppUrl::URL_ACCOUNT_BALANCE, array('id' => $data->_key)); ?>"><?php echo Yii::t('strings', 'Balance'); ?></a>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <?php if ($this->hasUserAccess('account_delete')): ?>
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
<div class="modal fade" id="containerForDetailInfo" tabindex="-1" role="dialog" aria-labelledby="containerForDetailInfoLabel"></div>