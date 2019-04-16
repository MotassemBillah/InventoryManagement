<nav class="navbar navbar-default" style="margin-bottom: 15px;">
    <ul class="nav navbar-nav">
        <?php foreach ($menuitems as $menu) : ?>
            <li>
                <a href="<?php echo Yii::app()->createUrl(AppUrl::URL_LEDGER_JOURNAL_VIEW, array('id' => $menu->id)); ?>"><?php echo $menu->name; ?></a>
            </li>
        <?php endforeach; ?>
    </ul>
</nav>
