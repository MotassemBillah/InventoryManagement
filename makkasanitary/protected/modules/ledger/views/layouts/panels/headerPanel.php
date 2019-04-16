<nav class="navbar navbar-inverse navbar-fixed-top" id="header_nav">
    <div class="container-fluid">
        <div class="navbar-header">
            <?php if (!Yii::app()->user->isGuest): ?>
                <button type="button" class="navbar-toggle admin_nav_toggle" data-target="#admin_nav">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
            <?php endif; ?>
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app_nav_collapse" aria-expanded="false">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="<?php echo Yii::app()->getBaseUrl(true); ?>">
                <?php if (!empty($this->settings->logo)): ?>
                    <img alt="" class="img-responsive" src="<?php echo Yii::app()->request->baseUrl . '/uploads/' . $this->settings->logo; ?>" style="max-height: 40px;">
                <?php else: ?>
                    <?php echo Yii::t('strings', 'Logo'); ?>
                <?php endif; ?>
            </a>
        </div>

        <div class="collapse navbar-collapse" id="app_nav_collapse">
            <ul class="nav navbar-nav navbar-right">
                <?php if (Yii::app()->user->isGuest): ?>
                    <li<?php if ($this->currentPage == AppUrl::URL_LOGIN) echo ' class="active"'; ?>>
                        <a href="<?php echo $this->createUrl(AppUrl::URL_LOGIN); ?>"><?php echo Yii::t('strings', 'Login'); ?></a>
                    </li>
                <?php else: ?>
                    <li class="dropdown" id="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"><i class="fa fa-user"></i>&nbsp;<?php echo ucfirst(UserIdentity::displayname()); ?> <span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            <li<?php if ($this->currentPage == AppUrl::URL_DASHBOARD) echo ' class="active"'; ?>>
                                <a href="<?php echo $this->createUrl(AppUrl::URL_DASHBOARD); ?>"><?php echo Yii::t('strings', 'Dashboard'); ?></a>
                            </li>
                            <li class="divider"></li>
                            <li<?php if ($this->currentPage == AppUrl::URL_USER_PROFILE) echo ' class="active"'; ?>>
                                <a href="<?php echo $this->createUrl(AppUrl::URL_USER_PROFILE); ?>"><?php echo Yii::t('strings', 'Profile'); ?></a>
                            </li>
                            <li<?php if ($this->currentPage == AppUrl::URL_PASSWORD_CHANGE) echo ' class="active"'; ?>>
                                <a href="<?php echo $this->createUrl(AppUrl::URL_PASSWORD_CHANGE); ?>"><?php echo Yii::t('strings', 'Change Password'); ?></a>
                            </li>
                            <li class="divider"></li>
                            <li>
                                <a href="<?php echo $this->createUrl(AppUrl::URL_USER_LOGOUT); ?>"><?php echo Yii::t('strings', 'Log Out'); ?></a>
                            </li>
                        </ul>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>