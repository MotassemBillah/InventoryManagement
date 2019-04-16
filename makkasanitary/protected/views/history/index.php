<?php $this->breadcrumbs = array('History'); ?>
<div class="well">
    <table width="100%">
        <tr>
            <td class="text-right">
                <button type="button" class="btn btn-danger btn-xs" id="admin_del_btn" disabled="disabled"><i class="fa fa-trash-o"></i>&nbsp;<?php echo Yii::t("strings", "Delete"); ?></button>
                <a class="btn btn-warning btn-xs" href="<?php echo $this->createUrl(AppUrl::URL_HISTORY_CLEAR); ?>"><?php echo Yii::t('strings', 'Clear History'); ?></a>
            </td>
        </tr>
    </table>
</div>

<form id="deleteForm" action="" method="post">
    <div id="ajaxContent">
        <?php if (!empty($dataset) && count($dataset) > 0) : ?>
            <div class="table-responsive">
                <table class="table table-striped table-bordered tbl_invoice_view">
                    <tr class="bg_gray" id="r_checkAll">
                        <th class="text-center" style="width:4%;"><?php echo Yii::t('strings', 'SL#'); ?></th>
                        <th style="width:10%;"><?php echo Yii::t('strings', 'User'); ?></th>
                        <th><?php echo Yii::t('strings', 'Url'); ?></th>
                        <th style="width:18%;"><?php echo Yii::t('strings', 'Date/Time'); ?></th>
                        <th class="text-center" style="width:10%;"><?php echo Yii::t('strings', 'Actions'); ?></th>
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
                            <td><?php echo User::model()->displayname($data->user_id); ?></td>
                            <td><?php echo "<a href='{$data->url}' target='_blank'>{$data->url}</a>"; ?></td>
                            <td><?php echo date("j F Y, h:i:s A", strtotime($data->date_time)); ?></td>
                            <td class="text-center">
                                <a class="btn btn-info btn-xs" href="<?php echo $this->createUrl(AppUrl::URL_HISTORY_VIEW, ['id' => $data->_key]); ?>"><?php echo Yii::t('strings', 'View'); ?></a>
                                <a class="btn btn-danger btn-xs" href="<?php echo $this->createUrl(AppUrl::URL_HISTORY_DELETE, ['id' => $data->_key]); ?>"><?php echo Yii::t('strings', 'Delete'); ?></a>
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
<script type="text/javascript">
    $(document).ready(function() {
        $(document).on('click', '#admin_del_btn', function(e) {
            var _rc = confirm('Are you sure about this action? This cannot be undone!');

            if (_rc === true) {
                showLoader("Processing...", true);
                var _form = $("#deleteForm");
                var _url = baseUrl + '/history/deleteall';

                $.post(_url, _form.serialize(), function(res) {
                    if (res.success === true) {
                        $("#ajaxMessage").showAjaxMessage({html: res.message, type: 'success'});
                        setTimeout(function() {
                            location.reload();
                        }, 1000);
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