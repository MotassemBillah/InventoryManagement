<?php if (!empty($dataset) && count($dataset) > 0) : ?>
    <div class="table-responsive">
        <table class="table table-bordered">
            <tr id="r_checkAll">
                <th class="text-center" style="width:5%;"><?php echo Yii::t('strings', 'SL#'); ?></th>
                <th><?php echo Yii::t('strings', 'Head'); ?></th>
                <th><?php echo Yii::t('strings', 'Sub Head'); ?></th>
                <th><?php echo Yii::t('strings', 'Particuler'); ?></th>
                <th class="text-center"><?php echo Yii::t('strings', 'Actions'); ?></th>
                <th class="text-center" style="width:5%;"><input type="checkbox" id="checkAll" onclick="toggleCheckboxes(this)"></th>
            </tr>
            <?php
            $counter = 0;
            foreach ($dataset as $data):
                $counter++;
                ?>
                <tr class="pro_cat pro_cat_">
                    <td class="text-center" style="width:5%;"><?php echo $counter; ?></td>
                    <td><?php echo AppHelper::getCleanValue($data->head->name); ?></td>
                    <td><?php echo AppHelper::getCleanValue($data->sub_head->name); ?></td>
                    <td><?php echo AppHelper::getCleanValue($data->particuler); ?></td>
                    <td class="text-center">
                        <a class="btn btn-info btn-xs" href="<?php echo $this->createUrl(AppUrl::URL_LEDGER_HEAD_PARTICULER_EDIT, array('id' => $data->id)); ?>"><?php echo Yii::t('strings', 'Edit'); ?></a>
                        <a class="btn btn-danger btn-xs" href="<?php echo $this->createUrl(AppUrl::URL_LEDGER_HEAD_PARTICULER_DELETE, array('id' => $data->id)); ?>" onclick="return confirm('Are you sure about deletion? This cannot be undon.')"><?php echo Yii::t('strings', 'Delete'); ?></a>
                    </td>
                    <td class="text-center" style="width:5%;"><input type="checkbox" name="data[]" value="<?php echo $data->id; ?>" class="check"></td>
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