
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
                $sum['totaldue'][] = ($data->advance_amount - $data->due_amount);
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
    <div class="alert alert-info"><?php echo Yii::t('strings', 'No records found!'); ?></div>
<?php endif; ?>