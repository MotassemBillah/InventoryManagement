<nav class="navbar navbar-default navbar-fixed-top" id="header_nav">
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
            <ul class="nav navbar-nav">
                <?php if (in_array(Yii::app()->user->id, [1, 4])): ?>
                    <li>
                        <a href="<?php echo Yii::app()->createUrl(AppUrl::URL_HISTORY); ?>"><?php echo Yii::t('strings', 'History'); ?></a>
                    </li>
                <?php endif; ?>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"><?php echo Yii::t('strings', 'Product'); ?>&nbsp;<span class="caret"></span></a>
                    <ul class="dropdown-menu">
                        <?php if ($this->hasUserAccess('product_list')): ?>
                            <li<?php if ($this->currentPage == AppUrl::URL_PRODUCT_LIST) echo ' class="active"'; ?>>
                                <a href="<?php echo Yii::app()->createUrl(AppUrl::URL_PRODUCT_LIST); ?>"><?php echo Yii::t('strings', 'List'); ?></a>
                            </li>
                        <?php endif; ?>
                        <?php if ($this->hasUserAccess('product_stock')): ?>
                            <li<?php if ($this->currentPage == AppUrl::URL_PRODUCT) echo ' class="active"'; ?>>
                                <a href="<?php echo Yii::app()->createUrl(AppUrl::URL_PRODUCT); ?>"><?php echo Yii::t('strings', 'Stock'); ?></a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"><?php echo Yii::t('strings', 'Accounts'); ?>&nbsp;<span class="caret"></span></a>
                    <ul class="dropdown-menu">
                        <?php if ($this->hasUserAccess('account_list')): ?>
                            <li<?php if ($this->currentPage == AppUrl::URL_ACCOUNT) echo ' class="active"'; ?>>
                                <a href="<?php echo Yii::app()->createUrl(AppUrl::URL_ACCOUNT); ?>"><?php echo Yii::t('strings', 'Bank Account'); ?></a>
                            </li>
                        <?php endif; ?>
                        <?php if ($this->hasUserAccess('cash_account_list')): ?>
                            <li<?php if ($this->currentPage == AppUrl::URL_CASH_ACCOUNT) echo ' class="active"'; ?>>
                                <a href="<?php echo Yii::app()->createUrl(AppUrl::URL_CASH_ACCOUNT); ?>"><?php echo Yii::t('strings', 'Cash Account'); ?></a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"><?php echo Yii::t('strings', 'Manage Orders'); ?>&nbsp;<span class="caret"></span></a>
                    <ul class="dropdown-menu">
                        <?php if ($this->hasUserAccess('purchase_list')): ?>
                            <li<?php if ($this->currentPage == AppUrl::URL_PURCHASE) echo ' class="active"'; ?>>
                                <a href="<?php echo Yii::app()->createUrl(AppUrl::URL_PURCHASE); ?>"><?php echo Yii::t('strings', 'Purchase'); ?></a>
                            </li>
                        <?php endif; ?>
                        <?php if ($this->hasUserAccess('sale_list')): ?>
                            <li<?php if ($this->currentPage == AppUrl::URL_SALE) echo ' class="active"'; ?>>
                                <a href="<?php echo Yii::app()->createUrl(AppUrl::URL_SALE); ?>"><?php echo Yii::t('strings', 'Sales'); ?></a>
                            </li>
                        <?php endif; ?>
                        <?php if ($this->hasUserAccess('view_cart')): ?>
                            <li<?php if ($this->currentPage == AppUrl::URL_SALE_CART) echo ' class="active"'; ?>>
                                <a href="<?php echo Yii::app()->createUrl(AppUrl::URL_SALE_CART); ?>"><?php echo Yii::t('strings', 'View Cart'); ?></a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"><?php echo Yii::t('strings', 'Return Orders'); ?>&nbsp;<span class="caret"></span></a>
                    <ul class="dropdown-menu">
                        <?php if ($this->hasUserAccess('purchase_return_list')): ?>
                            <li>
                                <a onclick="alert('Under Construction!');" href="javascript:void(0);"><?php echo Yii::t('strings', 'Purchase Return'); ?></a>
                            </li>
                        <?php endif; ?>
                        <?php if ($this->hasUserAccess('sale_return_list')): ?>
                            <li<?php if ($this->currentPage == AppUrl::URL_SALERETURN) echo ' class="active"'; ?>>
                                <a href="<?php echo Yii::app()->createUrl(AppUrl::URL_SALERETURN); ?>"><?php echo Yii::t('strings', 'Sales Return'); ?></a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"><?php echo Yii::t('strings', 'Ledger'); ?>&nbsp;<span class="caret"></span></a>
                    <ul class="dropdown-menu">
                        <?php if ($this->hasUserAccess('head_list')): ?>
                            <li<?php if ($this->currentPage == AppUrl::URL_LEDGER_HEAD) echo ' class="active"'; ?>>
                                <a href="<?php echo Yii::app()->createUrl(AppUrl::URL_LEDGER_HEAD); ?>"><?php echo Yii::t('strings', 'Account Heads'); ?></a>
                            </li>
                        <?php endif; ?>
                        <?php if ($this->hasUserAccess('expense_list')): ?>
                            <li<?php if ($this->currentPage == AppUrl::URL_LEDGER_EXPENSE) echo ' class="active"'; ?>>
                                <a href="<?php echo Yii::app()->createUrl(AppUrl::URL_LEDGER_EXPENSE); ?>"><?php echo Yii::t('strings', 'Expense List'); ?></a>
                            </li>
                        <?php endif; ?>
                        <?php if ($this->hasUserAccess('income_list')): ?>
                            <li<?php if ($this->currentPage == AppUrl::URL_PROFIT) echo ' class="active"'; ?>>
                                <a href="<?php echo Yii::app()->createUrl(AppUrl::URL_PROFIT); ?>"><?php echo Yii::t('strings', 'Income Statement'); ?></a>
                            </li>
                        <?php endif; ?>
                        <li<?php if ($this->currentPage == AppUrl::URL_LEDGER_FINANCE_STATEMENT) echo ' class="active"'; ?>>
                            <a href="<?php echo Yii::app()->createUrl(AppUrl::URL_LEDGER_FINANCE_STATEMENT); ?>"><?php echo Yii::t('strings', 'Financial Statement'); ?></a>
                        </li>
                        <?php if ($this->hasUserAccess('balance_sheet')): ?>
                            <li<?php if ($this->currentPage == AppUrl::URL_LEDGER_BALANCE_SHEET) echo ' class="active"'; ?>>
                                <a href="<?php echo Yii::app()->createUrl(AppUrl::URL_LEDGER_BALANCE_SHEET); ?>"><?php echo Yii::t('strings', 'Balance Sheet'); ?></a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"><?php echo Yii::t('strings', 'Manage Lists'); ?>&nbsp;<span class="caret"></span></a>
                    <ul class="dropdown-menu">
                        <?php if ($this->hasUserAccess('category_list')): ?>
                            <li<?php if ($this->currentPage == AppUrl::URL_CATEGORIES) echo ' class="active"'; ?>>
                                <a href="<?php echo Yii::app()->createUrl(AppUrl::URL_CATEGORIES); ?>"><?php echo Yii::t('strings', 'Category List'); ?></a>
                            </li>
                        <?php endif; ?>
                        <?php if ($this->hasUserAccess('company_list')): ?>
                            <li<?php if ($this->currentPage == AppUrl::URL_COMPANY) echo ' class="active"'; ?>>
                                <a href="<?php echo Yii::app()->createUrl(AppUrl::URL_COMPANY); ?>"><?php echo Yii::t('strings', 'Company List'); ?></a>
                            </li>
                        <?php endif; ?>
                        <?php if ($this->hasUserAccess('customer_list')): ?>
                            <li<?php if ($this->currentPage == AppUrl::URL_CUSTOMER) echo ' class="active"'; ?>>
                                <a href="<?php echo Yii::app()->createUrl(AppUrl::URL_CUSTOMER); ?>"><?php echo Yii::t('strings', 'Customer List'); ?></a>
                            </li>
                        <?php endif; ?>
                        <?php if ($this->hasUserAccess('user_list')): ?>
                            <li<?php if ($this->currentPage == AppUrl::URL_USERLIST) echo ' class="active"'; ?>>
                                <a href="<?php echo Yii::app()->createUrl(AppUrl::URL_USERLIST); ?>"><?php echo Yii::t('strings', 'User List'); ?></a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </li>
                <?php if ($this->hasUserAccess('settings')): ?>
                    <li<?php if ($this->currentPage == AppUrl::URL_SETTINGS) echo ' class="active"'; ?>>
                        <a href="<?php echo Yii::app()->createUrl(AppUrl::URL_SETTINGS); ?>"><?php echo Yii::t('strings', 'Setting'); ?></a>
                    </li>
                <?php endif; ?>
            </ul>

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