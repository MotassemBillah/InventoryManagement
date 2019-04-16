<?php
$this->breadcrumbs = array(
    'Sales'
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
                                <option value="<?php echo AppConstant::ORDER_COMPLETE; ?>"><?php echo AppConstant::ORDER_COMPLETE; ?></option>
                                <option value="<?php echo AppConstant::ORDER_PENDING; ?>"><?php echo AppConstant::ORDER_PENDING; ?></option>
                            </select>
                            <div class="col-md-2 col-sm-3 no_pad">
                                <div class="input-group xsw_100">
                                    <input type="text" id="from_date" class="form-control" name="from_date" placeholder="(dd-mm-yyyy)" readonly>
                                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                                </div>
                            </div>
                            <div class="col-md-1 col-sm-1 text-center" style="font-size:14px;width: 5%;">
                                <b style="color: rgb(0, 0, 0); vertical-align: middle; display: block; padding: 6px 0px;">TO</b>
                            </div>
                            <div class="col-md-2 col-sm-3 no_pad">
                                <div class="input-group xsw_100">
                                    <input type="text" id="to_date" class="form-control" name="to_date" placeholder="(dd-mm-yyyy)" readonly>
                                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                                </div>
                            </div>
                            <input type="text" id="customer" name="customer" class="form-control" placeholder="customer" style="width:15%;">
                            <input type="number" id="invoice" name="invoice" class="form-control" placeholder="invoice" style="width:10%;">
                            <button type="button" id="search" class="btn btn-info"><?php echo Yii::t("strings", "Search"); ?></button>
                            <button type="button" id="printMemo" class="btn btn-primary"><?php echo Yii::t("strings", "Export"); ?></button>
                        </div>
                    </div>
                    <?php if (in_array(Yii::app()->user->id, [1, 4, 5])): ?>
                        <div class="col-md-2 col-sm-3 no_pad">
                            <?php
                            $users = User::model()->getList();
                            $userList = CHtml::listData($users, 'id', 'login');
                            echo CHtml::dropDownList('user', 'user', $userList, array('empty' => 'User', 'class' => 'form-control'));
                            ?>
                        </div>
                    <?php endif; ?>
                </form>
            </td>
            <td class="text-right wmd_30" style="position: relative;">
                <a class="btn btn-success btn-xs" href="<?php echo Yii::app()->createUrl(AppUrl::URL_SALE_CREATE); ?>"><i class="fa fa-plus"></i>&nbsp;<?php echo Yii::t("strings", "New"); ?></a>
                <?php if ($this->hasUserAccess('sale_delete')): ?>
                    <button type="button" class="btn btn-danger btn-xs" id="admin_del_btn" disabled="disabled"><i class="fa fa-trash-o"></i>&nbsp;<?php echo Yii::t("strings", "Delete"); ?></button>
                <?php endif; ?>
                <?php if ($this->hasUserAccess('reset_invoice')): ?>
                    <button type="button" class="btn btn-warning btn-xs" id="admin_reset_btn" disabled="disabled"><i class="fa fa-wrench"></i>&nbsp;<?php echo Yii::t("strings", "Reset"); ?></button>
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
                        <th class="text-center" style="width:4%;"><?php echo Yii::t("strings", "SL#"); ?></th>
                        <th><?php echo Yii::t("strings", "Date"); ?></th>
                        <th><?php echo Yii::t("strings", "Customer"); ?></th>
                        <th><?php echo Yii::t("strings", "Sales Person"); ?></th>
                        <th class="text-center"><?php echo Yii::t("strings", "Items"); ?></th>
                        <th class="text-center"><?php echo Yii::t("strings", "Status"); ?></th>
                        <th class="text-center"><?php echo Yii::t("strings", "Actions"); ?></th>
                        <?php if ($this->hasUserAccess('sale_delete') || $this->hasUserAccess('reset_invoice')): ?>
                            <th class="text-center" style="width:3%;"><input type="checkbox" id="checkAll" onclick="toggleCheckboxes(this)"></th>
                        <?php endif; ?>
                    </tr>
                    <?php
                    $counter = 0;
                    if (isset($_GET['page']) && $_GET['page'] > 1) {
                        $counter = ($_GET['page'] - 1) * $pages->pageSize;
                    }
                    foreach ($dataset as $data) :
                        $counter++;
                        ?>
                        <tr id="row_<?php echo $data->id; ?>">
                            <td class="text-center"><?php echo $counter; ?></td>
                            <td><?php echo date('j M Y', strtotime($data->created)); ?></td>
                            <td><?php echo AppObject::customerName($data->customer_id); ?></td>
                            <td><?php echo AppObject::displayNameByUser($data->created_by); ?></td>
                            <td class="text-center"><?php echo count($data->items); ?></td>
                            <td class="text-center">
                                <?php
                                if ($data->status == AppConstant::ORDER_PENDING) {
                                    echo '<span class="label label-danger">' . Yii::t("strings", $data->status) . '</span>';
                                } else {
                                    echo '<span class="label label-success">' . Yii::t("strings", AppConstant::ORDER_COMPLETE) . '</span>';
                                }
                                ?>
                            </td>
                            <td class="text-center">
                                <?php if ($data->status == AppConstant::ORDER_PENDING): ?>
                                    <?php if ($this->hasUserAccess('sale_edit')): ?>
                                        <a class="btn btn-info btn-xs" href="<?php echo $this->createUrl(AppUrl::URL_SALE_EDIT, array('id' => $data->_key)); ?>"><?php echo Yii::t("strings", "Edit"); ?></a>
                                    <?php endif; ?>
                                    <?php if ($this->hasUserAccess('sale_process')): ?>
                                        <a class="btn btn-primary btn-xs process_order" href="javascript://" data-info="<?php echo $data->_key; ?>"><?php echo Yii::t("strings", "Process"); ?></a>
                                    <?php endif; ?>
                                <?php endif; ?>
                                <?php if ($this->hasUserAccess('sale_view')): ?>
                                    <a class="btn btn-primary btn-xs" href="<?php echo $this->createUrl(AppUrl::URL_SALE_VIEW, array('id' => $data->invoice_no)); ?>"><?php echo Yii::t("strings", "View"); ?></a>
                                <?php endif; ?>
                            </td>
                            <?php if ($this->hasUserAccess('sale_delete') || $this->hasUserAccess('reset_invoice')): ?>
                                <td class="text-center"><input type="checkbox" name="data[]" value="<?php echo $data->id; ?>" class="check"></td>
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
                    )
                ));
                ?>
            </div>
        <?php else: ?>
            <div class="alert alert-info">No records found!</div>
        <?php endif; ?>
    </div>
</form>
<div id="container_for_detail" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" style="width: 60%;">
        <div class="modal-content" id="modalContent">
            <img class="ajaxLoader" src="<?php echo Yii::app()->request->baseUrl; ?>/img/md_loading.gif" alt="Loading...">
            <div class="modal-content" id="modalAjaxContent"></div>
        </div>
    </div>
</div>
<div id="container_for_sale" class="modal fade" tabindex="-1" role="dialog"></div>
<script type="text/javascript">
    $(document).ready(function() {
        $("#from_date, #to_date").datepicker({
            format: 'dd-mm-yyyy'
        });
    });
</script>