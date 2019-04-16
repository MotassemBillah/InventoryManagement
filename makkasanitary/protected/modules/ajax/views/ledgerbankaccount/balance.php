<?php if (!empty($dataset) && count($dataset) > 0) : ?>
    <div class="table-responsive">
        <table class="table table-bordered">
            <tr id="r_checkAll">
                <th class="text-center" style="width:5%;"><?php echo Yii::t("strings", "SL#"); ?></th>
                <th><?php echo Yii::t("strings", "Description"); ?></th>
                <th class="text-right"><?php echo Yii::t("strings", "Debit"); ?></th>
                <th class="text-right"><?php echo Yii::t("strings", "Credit"); ?></th>
                <th class="text-right"><?php echo Yii::t("strings", "Balance"); ?></th>
                <th class="text-center"><?php echo Yii::t("strings", "Last Update"); ?></th>
                <!--<th class="text-center"><?php // echo Yii::t("strings", "Actions");         ?></th>-->
            </tr>
            <?php
            $counter = 0;
            foreach ($dataset as $data):
                $counter++;
                $trClass = '';
                if ($data->description == AppConstant::CASH_IN)
                    $trClass = ' class="bg-success"';
                else
                    $trClass = ' class="bg-warning"';
                if ($data->description !== AppConstant::INITIAL_BALANCE):
                    ?>
                    <tr <?php echo $trClass; ?>>
                        <td class="text-center" style="width:5%;"><?php echo $counter; ?></td>
                        <td><?php echo AppHelper::getCleanValue($data->description); ?></td>
                        <td class="text-right"><?php echo AppHelper::getFloat($data->debit); ?></td>
                        <td class="text-right"><?php echo AppHelper::getFloat($data->credit); ?></td>
                        <td class="text-right"><?php echo AppHelper::getFloat($data->balance); ?></td>
                        <td class="text-center"><?php echo date('j M, Y H:i A', strtotime($data->last_transaction_time)); ?></td>
            <!--                                <td class="text-center">
                        <?php // if ($data->description != AppConstant::INITIAL_BALANCE): ?>
                                <a class="btn btn-info btn-xs" href="<?php // echo $this->createUrl(AppUrl::URL_LEDGER_ACCOUNT_BALANCE_EDIT, array('id' => $data->id));          ?>"><?php // echo Yii::t("strings", "Edit");          ?></a>
                        <?php // endif; ?>
                        </td>-->
                    </tr>
                <?php endif; ?>
                <?php
            endforeach;
            $sum['debit'][] = $data->debit;
            $sum['crebit'][] = $data->credit;
            $sum['balance'][] = $data->balance;
            ?>
            <tr class="bg_gray">
                <th colspan="2" class="text-right"><?php echo Yii::t("strings", "Total"); ?></th>
                <th class="text-right"><?php echo AppHelper::getFloat(array_sum($sum['debit'])); ?></th>
                <th class="text-right"><?php echo AppHelper::getFloat(array_sum($sum['crebit'])); ?></th>
                <th class="text-right"><?php echo AppHelper::getFloat(array_sum($sum['balance'])); ?></th>
                <th colspan="1"></th>
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