<?php if (!empty($dataset) && count($dataset) > 0) : ?>
    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <tr id="r_checkAll">
                <?php if ($this->hasUserAccess('bank_delete')): ?>
                    <th class="text-center" style="width:5%;"><input type="checkbox" id="checkAll" onclick="toggleCheckboxes(this)"></th>
                <?php endif; ?>
                <th><?php echo Yii::t("strings", "Name"); ?></th>
                <th><?php echo Yii::t("strings", "Last Modified"); ?></th>
                <th class="text-center"><?php echo Yii::t("strings", "Actions"); ?></th>
            </tr>
            <?php foreach ($dataset as $data): ?>
                <tr>
                    <?php if ($this->hasUserAccess('bank_delete')): ?>
                        <td class="text-center" style="width:5%;"><input type="checkbox" name="data[]" value="<?php echo $data->id; ?>" class="check"/></td>
                    <?php endif; ?>
                    <td><?php echo AppHelper::getCleanValue($data->name); ?></td>
                    <td><?php echo date("j F Y", strtotime($data->last_modified)); ?></td>
                    <td class="text-center">
                        <?php if ($this->hasUserAccess('bank_edit')): ?>
                            <a class="btn btn-info btn-xs" href="<?php echo $this->createUrl(AppUrl::URL_BANK_EDIT, array('id' => $data->_key)); ?>"><?php echo Yii::t("strings", "Edit"); ?></a>
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
            'maxButtonCount' => 5,
            'htmlOptions' => array(
                'class' => 'pagination',
            )
        ));
        ?>
    </div>
    <script type="text/javascript">
        $(document).ready(function() {
            $(document).on("click", ".pagination li a", function(e) {
                showLoader("Processing...", true);
                var _form = $("#frmSearch");
                var _srcUrl = $(this).attr('href');

                $.ajax({
                    type: "POST",
                    url: _srcUrl,
                    data: _form.serialize(),
                    success: function(res) {
                        showLoader("", false);
                        $("#ajaxContent").html('');
                        $("#ajaxContent").html(res);
                    }
                });
                e.preventDefault();
            });
        });
    </script>
<?php else: ?>
    <div class="alert alert-info">No records found!</div>
<?php endif; ?>