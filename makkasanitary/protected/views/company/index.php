<?php
$this->breadcrumbs = array(
    'Company'
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
                            <select id="itemSort" class="form-control" name="itemSort" style="width:55px;">
                                <?php
                                $a2zlist = AppHelper::a2zlist();
                                for ($i = 0; $i < count($a2zlist); $i++) {
                                    echo "<option value='{$a2zlist[$i]}'>{$a2zlist[$i]}</option>";
                                }
                                ?>
                            </select>
                            <input type="text" name="q" id="q" class="form-control" placeholder="search name or mobile" size="30">
                            <button type="button" id="search" class="btn btn-info"><?php echo Yii::t("strings", "Search"); ?></button>
                        </div>
                    </div>
                </form>
            </td>
            <td class="text-right wmd_30" style="position: relative;">
                <a class="btn btn-success btn-xs" href="<?php echo Yii::app()->createUrl(AppUrl::URL_COMPANY_CREATE); ?>"><i class="fa fa-plus"></i>&nbsp;<?php echo Yii::t("strings", "New"); ?></a>
                <?php if ($this->hasUserAccess('company_delete')): ?>
                    <button type="button" class="btn btn-danger btn-xs" id="admin_del_btn" disabled="disabled"><i class="fa fa-trash-o"></i>&nbsp;<?php echo Yii::t("strings", "Delete"); ?></button>
                <?php endif; ?>
                <?php if (in_array(Yii::app()->user->id, [1, 4, 5])): ?>
                    <a class="btn btn-primary btn-xs" href="<?php echo Yii::app()->createUrl(AppUrl::URL_COMPANY_DELETED_LIST); ?>"><i class="fa fa-trash-o"></i>&nbsp;<?php echo Yii::t("strings", "Deleted List"); ?></a>
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
                        <th><?php echo Yii::t('strings', 'Name'); ?></th>
                        <th><?php echo Yii::t('strings', 'Email'); ?></th>
                        <th><?php echo Yii::t('strings', 'Contacts'); ?></th>
                        <th style="width: 24%;"><?php echo Yii::t('strings', 'Address'); ?></th>
                        <th class="text-center"><?php echo Yii::t('strings', 'Actions'); ?></th>
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
                            <td><?php echo AppHelper::getCleanValue($data->name); ?></td>
                            <td><?php echo AppHelper::getCleanValue($data->email); ?></td>
                            <td>
                                <?php
                                echo "<u>Phone</u>: " . $data->phone;
                                if (!empty($data->mobile)) {
                                    echo "<br>";
                                    echo "<u>Mobile</u>: " . $data->mobile;
                                }
                                if (!empty($data->other_contacts)) {
                                    echo "<br>";
                                    echo "<u>Other Contacts</u>: " . $data->other_contacts;
                                }
                                ?>
                            </td>
                            <td><?php echo AppHelper::getCleanValue($data->address); ?></td>
                            <td class="text-center">
                                <?php if ($this->hasUserAccess('company_edit')): ?>
                                    <a class="btn btn-info btn-xs" href="<?php echo $this->createUrl(AppUrl::URL_COMPANY_EDIT, array('id' => $data->_key)); ?>"><?php echo Yii::t('strings', 'Edit'); ?></a>
                                <?php endif; ?>
                                <a class="btn btn-success btn-xs detail" href="javascript:void(0);" data-info="<?php echo $data->_key; ?>"><?php echo Yii::t('strings', 'View'); ?></a>
                                <?php if ($this->hasUserAccess('customer_payment')): ?>
                                    <a class="btn btn-primary btn-xs" href="<?php echo $this->createUrl(AppUrl::URL_COMPANY_PAYMENT, array('id' => $data->_key)); ?>" target="_blank"><?php echo Yii::t('strings', 'Payments'); ?></a>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <?php if ($this->hasUserAccess('company_delete')): ?>
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
                    'nextPageLabel' => '>',
                    'prevPageLabel' => '<',
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