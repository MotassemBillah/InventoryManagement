<?php
$this->breadcrumbs = array(
    'Users' => array(AppUrl::URL_USERLIST),
    'Permissions'
);
?>
<div class="content-panel">
    <?php if ($user->deletable == 1 && Yii::app()->user->id !== $user->id): ?>
        <form action="" method="post">
            <div class="row clearfix">
                <div class="col-sm-12">
                    <h3 style="margin: 0 0 15px;">Access Items For <u><?php echo $user->login; ?></u></h3>
                    <ul id="access_list" class="list-group">
                        <?php
                        $accessItems = AppHelper::userAccessItems();
                        $items = json_decode($user->access_item->items);

                        for ($i = 0; $i < count($accessItems); $i++):
                            if (in_array($accessItems[$i], $items)) {
                                $selected = ' checked="checked"';
                            } else {
                                $selected = '';
                            }
                            ?>
                            <li class="list-group-item <?php if (in_array($accessItems[$i], $items)) echo ' list-group-item-success'; ?>">
                                <label for="permission_<?php echo $i; ?>">
                                    <input type="checkbox" id="permission_<?php echo $i; ?>" name="permission[]" value="<?php echo $accessItems[$i]; ?>" <?php echo $selected; ?>>&nbsp;<?php echo ucfirst(str_replace('_', ' ', $accessItems[$i])); ?>
                                </label>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </div>
            </div>
            <div class="col-md-9 col-sm-9 form-group text-center">
                <label for="check_all">
                    <input type="checkbox" id="check_all" class="chk_no_mvam">&nbsp;<?php echo Yii::t('strings', 'Check All'); ?>
                </label>
                <input type="submit" class="btn btn-primary" id="savePermission" name="savePermission" value="<?php echo Yii::t('strings', 'Save'); ?>"/>
            </div>
        </form>
    <?php else: ?>
        <div class="alert alert-warning">You are not authorized to set permission for <strong><?php echo $user->login; ?></strong></div>
    <?php endif; ?>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        if ($("#access_list input[type='checkbox']:checked").length > 0) {
            $("#check_all").prop("checked", true);
        }

        $(document).on("click", "#check_all", function() {
            var _elm = $("#access_list input[type='checkbox']");
            if ($(_elm).is(":checked")) {
                $(_elm).prop("checked", false);
                $(_elm).closest('li').removeClass("list-group-item-success");
            } else {
                $(_elm).prop("checked", true);
                $(_elm).closest('li').addClass("list-group-item-success");
            }
        });

        $(document).on("change", "#access_list input[type='checkbox']", function() {
            if ($("#access_list input[type='checkbox']:checked").length > 0) {
                $("#check_all").prop("checked", true);
            }
            if ($(this).is(":checked")) {
                $(this).closest('li').addClass("list-group-item-success");
            } else {
                $(this).closest('li').removeClass("list-group-item-success");
            }
        });
    });
</script>

