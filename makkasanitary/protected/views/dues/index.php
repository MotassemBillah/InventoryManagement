<?php $this->breadcrumbs = array('Dues'); ?>
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
                            <div class="col-md-2 col-sm-3 no_pad">
                                <?php
                                $customerList = Customer::model()->getList();
                                $custList = CHtml::listData($customerList, 'id', 'name');
                                echo CHtml::dropDownList('customer', 'customer_id', $custList, array('empty' => 'Customer', 'class' => 'form-control'));
                                ?>
                            </div>
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
                            <button type="button" id="search" class="btn btn-info"><?php echo Yii::t("strings", "Search"); ?></button>
                            <button type="button" id="clear_from" class="btn btn-primary" data-info="/dues"><?php echo Yii::t("strings", "Clear"); ?></button>
                        </div>
                    </div>
                </form>
            </td>
        </tr>
    </table>
</div>

<form id="deleteForm" action="<?php echo $this->createUrl(AppUrl::URL_CUSTOMER_DELETEALL); ?>" method="post">
    <div id="ajaxContent">
        <?php if (!empty($dataset) && count($dataset) > 0) : ?>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <tr id="r_checkAll">
                        <th class="text-center" style="width:5%;"><?php echo Yii::t('strings', '#SL'); ?></th>
                        <th><?php echo Yii::t('strings', 'Invoice Date'); ?></th>
                        <th><?php echo Yii::t('strings', 'Customer'); ?></th>
                        <th><?php echo Yii::t('strings', 'Contact No'); ?></th>
                        <th><?php echo Yii::t('strings', 'Address'); ?></th>
                        <th class="text-right"><?php echo Yii::t('strings', 'Advance'); ?></th>
                        <th class="text-right"><?php echo Yii::t('strings', 'Due'); ?></th>
                    </tr>
                    <?php
                    $counter = 0;
                    foreach ($dataset as $data):
                        $counter++;
                        $_cls = !empty($data->due_amount) ? " bg_gray" : "";
                        ?>
                        <tr class="pro_cat pro_cat_">
                            <td class="text-center" style="width:5%;"><?php echo $counter; ?></td>
                            <td><?php echo date('j M Y', strtotime($data->pay_date)); ?></td>
                            <td><?php echo AppHelper::getCleanValue($data->customer->name); ?></td>
                            <td>
                                <?php
                                echo!empty($data->customer->phone) ? "<span class='dis_blok'><u>Phone</u>: " . $data->customer->phone . "</span>" : "";
                                echo!empty($data->customer->mobile) ? "<span class='dis_blok'><u>Mobile</u>: " . $data->customer->mobile . "</span>" : "";
                                ?>
                            </td>
                            <td><?php echo AppHelper::getCleanValue($data->customer->address); ?></td>
                            <td class="text-right<?php echo empty($data->advance_amount) ? " bg_gray" : ""; ?>"><?php echo AppHelper::getFloat($data->advance_amount); ?></td>
                            <td class="text-right<?php echo empty($data->due_amount) ? " bg_gray" : ""; ?>"><?php echo AppHelper::getFloat($data->due_amount); ?></td>
                        </tr>
                        <?php
                        $sum['adv'][] = $data->advance_amount;
                        $sum['due'][] = $data->due_amount;
                        $sum['totaldue'][] = ($data->due_amount - $data->advance_amount);
                    endforeach;
                    ?>
                    <tr class="bg_gray">
                        <th colspan="5" class="text-right"><?php echo Yii::t('strings', 'Total'); ?></th>
                        <th class="text-right"><?php echo AppHelper::getFloat(array_sum($sum['adv'])); ?></th>
                        <th class="text-right"><?php echo AppHelper::getFloat(array_sum($sum['due'])); ?></th>
                    </tr>
                    <tr class="bg_gray">
                        <th colspan="5" class="text-right"><?php echo Yii::t('strings', 'Total Due'); ?></th>
                        <th colspan="2" class="text-center"><?php echo AppHelper::getFloat(array_sum($sum['totaldue'])); ?></th>
                    </tr>
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
            <div class="alert alert-info"><?php echo Yii::t('strings', 'No records found!'); ?></div>
        <?php endif; ?>
    </div>
</form>