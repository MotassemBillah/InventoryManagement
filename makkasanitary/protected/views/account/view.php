<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close" title="Close"><span aria-hidden="true">x</span></button>
            <h4 class="modal-title"><u><?php echo $data->name; ?></u></h4>
        </div>
        <div class="modal-body" style="overflow-y: auto;min-height: 200px;max-height: 440px;">
            <div class="clearfix">
                <?php if (!empty($data->email)): ?>
                    <p>
                        <strong><?php echo Yii::t("strings", "Email"); ?></strong> :
                        <span><?php echo $data->email; ?></span>
                    </p>
                <?php endif; ?>
                <p>
                    <strong><?php echo Yii::t("strings", "Phone"); ?></strong> :
                    <span><?php echo $data->phone; ?></span>
                </p>
                <?php if (!empty($data->mobile)): ?>
                    <p>
                        <strong><?php echo Yii::t("strings", "Mobile"); ?></strong> :
                        <span><?php echo $data->mobile; ?></span>
                    </p>
                <?php endif; ?>
                <?php if (!empty($data->other_contacts)): ?>
                    <p>
                        <strong><?php echo Yii::t("strings", "Other Contacts"); ?></strong> :
                        <span>
                            <?php
                            $contacts = explode(",", $data->other_contacts);
                            foreach ($contacts as $contact) {
                                echo $contact . ",";
                            }
                            ?>
                        </span>
                    </p>
                <?php endif; ?>
                <?php if (!empty($data->fax)): ?>
                    <p>
                        <strong><?php echo Yii::t("strings", "Fax"); ?></strong> :
                        <span><?php echo $data->fax; ?></span>
                    </p>
                <?php endif; ?>
                <?php if (!empty($data->address)): ?>
                    <p>
                        <strong><?php echo Yii::t("strings", "Address"); ?></strong> :
                        <span><?php echo $data->address; ?></span>
                    </p>
                <?php endif; ?>
                <table class="table table-bordered table-hover" style="width: 70%">
                    <tr>
                        <th class="text-center" style="width: 5%"><?php echo Yii::t("strings", "SL#"); ?></th>
                        <th class="text-center"><?php echo Yii::t("strings", "Head/Type"); ?></th>
                    </tr>
                    <?php
                    if (!empty($data->heads)):
                        $counter = 0;
                        foreach ($data->heads as $heads):
                            $counter++;
                            ?>
                            <tr>
                                <td class="text-center" style="width: 5%"><?php echo $counter; ?></td>
                                <td class="text-center"><?php echo $heads->value; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="2" class="text-center"><em><?php echo Yii::t("strings", "This company has no head yet."); ?></em></td>
                        </tr>
                    <?php endif; ?>
                </table>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-info" data-dismiss="modal" aria-label="Close" title="Close"><?php echo Yii::t("strings", "Close"); ?></button>
        </div>
    </div>
</div>