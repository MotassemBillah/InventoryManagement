<?php $this->breadcrumbs = array('Customer'); ?>
<div class="well">
    <table width="100%">
        <tr>
            <td class="wmd_70">
                <form class="search-form" method="post" name="frmSearch" id="frmSearch">
                    <div class="input-group">
                        <div class="input-group-btn clearfix">
                            <select id="itemCount" class="form-control" name="itemCount" style="width:55px;">
                                <?php
                                for ($i = 10; $i <= 500; $i+=10) {
                                    if ($i > 100)
                                        $i+=40;
                                    if ($i > 200)
                                        $i+=50;
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
                            <div class="col-md-2 col-sm-3 no_pad">
                                <select id="itemType" class="form-control" name="itemType" style="">
                                    <option value="">Type</option>
                                    <?php
                                    $ctyleList = Customer::model()->typeList();
                                    foreach ($ctyleList as $_ctk => $_ctv) {
                                        echo "<option value='{$_ctk}' style='text-transform:capitalize'>{$_ctv}</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <input type="text" name="search" id="q" class="form-control" placeholder="search name or mobile" size="30">
                            <div class="col-md-2 col-sm-3 no_pad">
                                <?php $schemaInfo = Customer::model()->schemaInfo(); ?>
                                <select id="sortBy" class="form-control" name="sort_by">
                                    <?php
                                    foreach ($schemaInfo->columns as $_key => $columns) {
                                        $_nice_key = str_replace("_", " ", $_key);
                                        echo "<option value='{$_key}' style='text-transform:capitalize'>" . ucfirst($_nice_key) . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-2 col-sm-3 no_pad">
                                <select id="sortType" class="form-control" name="sort_type">
                                    <option value="ASC">Ascending</option>
                                    <option value="DESC">Descending</option>
                                </select>
                            </div>
                            <button type="button" id="search" class="btn btn-info"><?php echo Yii::t("strings", "Search"); ?></button>
                            <button type="button" id="clear_from" class="btn btn-primary" data-info="/customer">Clear</button>
                        </div>
                    </div>
                </form>
            </td>
            <td class="text-right wmd_30" style="">
                <a class="btn btn-success btn-xs" href="<?php echo Yii::app()->createUrl(AppUrl::URL_CUSTOMER_CREATE); ?>"><i class="fa fa-plus"></i>&nbsp;<?php echo Yii::t("strings", "New"); ?></a>
                <?php if ($this->hasUserAccess('customer_delete')): ?>
                    <button type="button" class="btn btn-danger btn-xs" id="admin_del_btn" disabled="disabled" ><i class="fa fa-trash-o"></i>&nbsp;<?php echo Yii::t("strings", "Delete"); ?></button>
                <?php endif; ?>
                <br>
                <button type="button" class="btn btn-primary btn-xs" onclick="printDiv('printDiv')"><i class="fa fa-print"></i>&nbsp;<?php echo Yii::t("strings", "Print"); ?></button>
            </td>
        </tr>
    </table>
</div>
<form id="deleteForm" action="" method="post">
    <div id="printDiv">
        <div class="form-group clearfix text-center txt_left_xs mp_center media_print show_in_print mp_mt">
            <?php if (!empty($this->settings->title)): ?>
                <h1 style="font-size:20px;margin:0;"><?php echo $this->settings->title; ?></h1>
            <?php endif; ?>
            <?php if (!empty($this->settings->author_address)): ?>
                <?php echo $this->settings->author_address; ?><br>
            <?php endif; ?>
            <h3 class="inv_title" style="font-size:17px;"><u><?php echo Yii::t("strings", "Customer List"); ?></u></h3>
        </div>
        <div id="ajaxContent">
            <?php if (!empty($dataset) && count($dataset) > 0) : ?>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered tbl_invoice_view">
                        <tr id="r_checkAll">
                            <th class="text-center" style="width:4%;"><?php echo Yii::t('strings', 'SL#'); ?></th>
                            <th><?php echo Yii::t('strings', 'Name'); ?></th>
                            <th><?php echo Yii::t('strings', 'Company'); ?></th>
                            <th><?php echo Yii::t('strings', 'Email'); ?></th>
                            <th><?php echo Yii::t('strings', 'Phone'); ?></th>
                            <th><?php echo Yii::t('strings', 'Address'); ?></th>
                            <th class="text-center dis_print"><?php echo Yii::t('strings', 'Actions'); ?></th>
                            <th class="text-right"><?php echo Yii::t('strings', 'Balance'); ?></th>
                            <th class="text-center dis_print" style="width:3%;">
                                <?php if ($this->hasUserAccess('customer_delete')): ?>
                                    <input type="checkbox" id="checkAll" onclick="toggleCheckboxes(this)">
                                <?php endif; ?>
                            </th>
                        </tr>
                        <?php
                        $counter = 0;
                        if (isset($_GET['page']) && $_GET['page'] > 1) {
                            $counter = ($_GET['page'] - 1) * $pages->pageSize;
                        }
                        foreach ($dataset as $data):
                            $counter++;
                            $_cbalance = AppObject::sumBalanceAmount($data->id);
                            ?>
                            <tr class="pro_cat pro_cat_">
                                <td class="text-center"><?php echo $counter; ?></td>
                                <td><?php echo AppHelper::getCleanValue($data->name); ?></td>
                                <td><?php echo AppHelper::getCleanValue($data->company); ?></td>
                                <td><?php echo AppHelper::getCleanValue($data->email); ?></td>
                                <td><?php echo AppHelper::getCleanValue($data->phone); ?></td>
                                <td><?php echo AppHelper::getCleanValue($data->address); ?></td>
                                <td class="text-center dis_print">
                                    <?php if ($this->hasUserAccess('customer_edit')): ?>
                                        <a class="btn btn-info btn-xs" href="<?php echo $this->createUrl(AppUrl::URL_CUSTOMER_EDIT, array('id' => $data->_key)); ?>"><?php echo Yii::t('strings', 'Edit'); ?></a>
                                    <?php endif; ?>
                                    <?php if ($this->hasUserAccess('customer_payment')): ?>
                                        <a class="btn btn-primary btn-xs" href="<?php echo $this->createUrl(AppUrl::URL_CUSTOMER_PAYMENT, array('id' => $data->_key)); ?>" target="_self"><?php echo Yii::t('strings', 'Payments'); ?></a>
                                    <?php endif; ?>
                                </td>
                                <td class="text-right<?php echo!empty($_cbalance) ? (($_cbalance > 0) ? " color_green" : " color_red") : " bg_gray"; ?>"><?php echo!empty($_cbalance) ? $_cbalance : ""; ?></td>
                                <td class="text-center dis_print">
                                    <?php if ($this->hasUserAccess('customer_delete')): ?>
                                        <input type="checkbox" name="data[]" value="<?php echo $data->id; ?>" class="check">
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php
                            $sumCbalance[] = $_cbalance;
                        endforeach;
                        ?>
                        <tr class="bg_gray">
                            <th colspan="6" class="text-right">Total</th>
                            <th class="dis_print"></th>
                            <th class="text-right<?php echo (array_sum($sumCbalance) > 0) ? " color_green" : " color_red"; ?>"><?php echo AppHelper::getFloat(array_sum($sumCbalance)); ?></th>
                            <th class="dis_print"></th>
                        </tr>
                    </table>
                </div>

                <div class="paging dis_print">
                    <?php
                    $this->widget('CLinkPager', array(
                        'pages' => $pages,
                        'header' => ' ',
                        'firstPageLabel' => '<<',
                        'lastPageLabel' => '>>',
                        'nextPageLabel' => '> ',
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
    </div>
</form>