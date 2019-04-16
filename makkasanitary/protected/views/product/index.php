<?php
$this->breadcrumbs = array(
    'Product'
);
?>
<div class="well">
    <table width="100%">
        <tr>
            <td class="wmd_70">
                <form class="search-form" method="post" name="frmSearch" id="frmSearch">
                    <div class="clearfix">
                        <div class="col-md-3 col-sm-3 no_pad_lft">
                            <label class="label_absolute" for="damaged">
                                <input type="checkbox" class="chk_no_mvam" id="damaged" name="damaged" value="1">&nbsp;<?php echo Yii::t("strings", "Damaged Products"); ?>
                            </label>
                        </div>
                    </div>
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
                            <div class="col-md-2 col-sm-2 no_pad">
                                <?php
                                $companyList = Company::model()->getList();
                                $comlist = CHtml::listData($companyList, 'id', 'name');
                                echo CHtml::dropDownList('company_id', 'company_id', $comlist, array('empty' => 'Company', 'class' => 'form-control'));
                                ?>
                            </div>
                            <div class="col-md-2 col-sm-2 no_pad">
                                <select id="type" class="form-control" name="type">
                                    <option value=""><?php echo Yii::t("strings", "Company Group"); ?></option>
                                </select>
                            </div>
                            <div class="col-md-2 col-sm-2 no_pad">
                                <?php
                                $categoryList = Category::model()->getList();
                                $catlist = CHtml::listData($categoryList, 'id', 'name');
                                echo CHtml::dropDownList('category_id', 'category_id', $catlist, array('empty' => 'Category', 'class' => 'form-control'));
                                ?>
                            </div>
                            <input type="text" name="q" id="q" class="form-control" placeholder="search by name" size="30"/>
                            <button type="button" id="search" class="btn btn-info"><?php echo Yii::t("strings", "Search"); ?></button>
                        </div>
                    </div>
                </form>
            </td>
            <td class="text-right wmd_30">
                <button type="button" class="btn btn-info btn-xs" id="admin_del_btn"><?php echo Yii::t("strings", "History"); ?></button>
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
                        <th><?php echo Yii::t("strings", "Product Name"); ?></th>
                        <th><?php echo Yii::t("strings", "Size"); ?></th>
                        <th><?php echo Yii::t("strings", "Head"); ?></th>
                        <th class="w25"><?php echo Yii::t("strings", "Code"); ?></th>
                        <th><?php echo Yii::t("strings", "Color"); ?></th>
                        <th><?php echo Yii::t("strings", "Grade"); ?></th>
                        <th class="text-center"><?php echo Yii::t("strings", "Stock"); ?></th>
                        <th class="text-center" style="width:3%;"><input type="checkbox" id="checkAll" onclick="toggleCheckboxes(this)" disabled="disabled"></th>
                    </tr>
                    <?php
                    $counter = 0;
                    if (isset($_GET['page']) && $_GET['page'] > 1) {
                        $counter = ($_GET['page'] - 1) * $pages->pageSize;
                    }
                    foreach ($dataset as $data):
                        $counter++;
                        ?>
                        <tr class="pro_cat pro_cat_<?php echo $data->category_id; ?>">
                            <td class="text-center"><?php echo $counter; ?></td>
                            <td><?php echo $data->name . " ( " . AppObject::categoryName($data->category_id) . " )"; ?></td>
                            <td<?php if (empty($data->size)) echo ' style="background-color:#ebebeb"'; ?>><?php echo!empty($data->size) ? $data->size : "N/A"; ?></td>
                            <td<?php if (empty($data->type)) echo ' style="background-color:#ebebeb"'; ?>><?php echo!empty($data->type) ? AppObject::companyHeadName($data->type) : "N/A"; ?></td>
                            <td class="w25"<?php if (empty($data->code)) echo ' style="background-color:#ebebeb"'; ?>><?php echo!empty($data->code) ? $data->code : "N/A"; ?></td>
                            <td<?php if (empty($data->color)) echo ' style="background-color:#ebebeb"'; ?>><?php echo!empty($data->color) ? $data->color : "N/A"; ?></td>
                            <td<?php if (empty($data->grade)) echo ' style="background-color:#ebebeb"'; ?>><?php echo!empty($data->grade) ? $data->grade : "N/A"; ?></td>
                            <td class="text-center">
                                <?php
                                $productStok = AppObject::stokByProduct($data->id);

                                if (!empty($data->unit) && $data->unit == "Foot") {
                                    $_stockAvailable = "<strong>" . $productStok . "</strong> <em style='color:blue'>({$data->unit})</em>";
                                } else {
                                    $_stockAvailable = "<strong>" . $productStok . "</strong>";
                                }

                                if ($productStok > 5) {
                                    $_style = ' style="color:green"';
                                } elseif ($productStok < 0) {
                                    $_style = ' style="color:red"';
                                } else {
                                    $_style = ' style="color:black"';
                                }
                                echo "<span{$_style}>{$_stockAvailable}</span>";
                                ?>
                            </td>
                            <td class="text-center"><input type="checkbox" name="data[]" value="<?php echo $data->id; ?>" class="check"></td>
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
<div class="modal fade" id="containerForHistory" tabindex="-1" role="dialog" aria-labelledby="containerForHistoryLabel"></div>