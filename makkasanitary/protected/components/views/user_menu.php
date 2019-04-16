
<?php
$personalArr = array(AppUrl::URL_USER_PROFILE, AppUrl::URL_PASSWORD_CHANGE);
$accArr = array(AppUrl::URL_ACCOUNT, AppUrl::URL_CASH_ACCOUNT);
$listArr = array(AppUrl::URL_CATEGORIES, AppUrl::URL_COMPANY, AppUrl::URL_CUSTOMER, AppUrl::URL_PRODUCT_LIST, AppUrl::URL_USERLIST);
$orderArr = array(AppUrl::URL_PURCHASE, AppUrl::URL_SALE, AppUrl::URL_SALE_CART);
$ledgerArr = array(AppUrl::URL_LEDGER, AppUrl::URL_LEDGER_HEAD, AppUrl::URL_LEDGER_EXPENSE, AppUrl::URL_LEDGER_BALANCE_SHEET, AppUrl::URL_LEDGER_INCOME, AppUrl::URL_LEDGER_FINANCE_STATEMENT);
$retArr = array(AppUrl::URL_SALERETURN);
$payArr = array(AppUrl::URL_PAYMENT);
if (!Yii::app()->user->isGuest):
    ?>
    <ul class="list-group">
        <?php if (in_array(Yii::app()->user->id, [1, 4])): ?>
            <li class="list-group-item<?php if ($this->currentPage == AppUrl::URL_HISTORY) echo ' active'; ?>">
                <a href="<?php echo Yii::app()->createUrl(AppUrl::URL_HISTORY); ?>"><?php echo Yii::t('strings', 'History'); ?></a>
            </li>
        <?php endif; ?>
        <?php if ($this->hasUserAccess('dashboard')): ?>
            <li class="list-group-item<?php if ($this->currentPage == AppUrl::URL_DASHBOARD) echo ' active'; ?>">
                <a href="<?php echo Yii::app()->createUrl(AppUrl::URL_DASHBOARD); ?>"><?php echo Yii::t('strings', 'Dashboard'); ?></a>
            </li>
        <?php endif; ?>
    </ul>

    <div class="panel panel-default<?php if (in_array($this->currentPage, $personalArr)) echo ' in'; ?>">
        <div class="panel-heading" role="tab" id="heading1">
            <h4 class="panel-title">
                <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse1" aria-expanded="true" aria-controls="collapse1">
                    <?php echo Yii::t('strings', 'Personal Info'); ?>&nbsp;<span class="caret"></span>
                </a>
            </h4>
        </div>
        <div id="collapse1" class="panel-collapse collapse<?php if (in_array($this->currentPage, $personalArr)) echo ' in'; ?>" role="tabpanel" aria-labelledby="heading1">
            <div class="panel-body no_pad">
                <ul class="list-group sidebar">
                    <li<?php if ($this->currentPage == AppUrl::URL_USER_PROFILE) echo ' class="active"'; ?>>
                        <a href="<?php echo Yii::app()->createUrl(AppUrl::URL_USER_PROFILE); ?>"><?php echo Yii::t('strings', 'Profile'); ?></a>
                    </li>
                    <li<?php if ($this->currentPage == AppUrl::URL_PASSWORD_CHANGE) echo ' class="active"'; ?>>
                        <a href="<?php echo Yii::app()->createUrl(AppUrl::URL_PASSWORD_CHANGE); ?>"><?php echo Yii::t('strings', 'Change Password'); ?></a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="panel panel-default<?php if (in_array($this->currentPage, $accArr)) echo ' in'; ?>">
        <div class="panel-heading" role="tab" id="heading10">
            <h4 class="panel-title">
                <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse10" aria-expanded="true" aria-controls="collapse10">
                    <?php echo Yii::t('strings', 'Manage Accounts'); ?>&nbsp;<span class="caret"></span>
                </a>
            </h4>
        </div>
        <div id="collapse10" class="panel-collapse collapse<?php if (in_array($this->currentPage, $accArr)) echo ' in'; ?>" role="tabpanel" aria-labelledby="heading10">
            <div class="panel-body no_pad">
                <ul class="list-group sidebar">
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
            </div>
        </div>
    </div>

    <ul class="list-group">
        <?php if ($this->hasUserAccess('product_stock')): ?>
            <li class="list-group-item<?php if ($this->currentPage == AppUrl::URL_PRODUCT) echo ' active'; ?>">
                <a href="<?php echo Yii::app()->createUrl(AppUrl::URL_PRODUCT); ?>"><?php echo Yii::t('strings', 'Product Stocks'); ?></a>
            </li>
        <?php endif; ?>
    </ul>

    <div class="panel panel-default<?php if (in_array($this->currentPage, $listArr)) echo ' in'; ?>">
        <div class="panel-heading" role="tab" id="heading11">
            <h4 class="panel-title">
                <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse11" aria-expanded="true" aria-controls="collapse11">
                    <?php echo Yii::t('strings', 'Manage Lists'); ?>&nbsp;<span class="caret"></span>
                </a>
            </h4>
        </div>
        <div id="collapse11" class="panel-collapse collapse<?php if (in_array($this->currentPage, $listArr)) echo ' in'; ?>" role="tabpanel" aria-labelledby="heading11">
            <div class="panel-body no_pad">
                <ul class="list-group">
                    <?php if ($this->hasUserAccess('category_list')): ?>
                        <li class="list-group-item<?php if ($this->currentPage == AppUrl::URL_CATEGORIES) echo ' active'; ?>">
                            <a href="<?php echo Yii::app()->createUrl(AppUrl::URL_CATEGORIES); ?>"><?php echo Yii::t('strings', 'Category List'); ?></a>
                        </li>
                    <?php endif; ?>
                    <?php if ($this->hasUserAccess('company_list')): ?>
                        <li class="list-group-item<?php if ($this->currentPage == AppUrl::URL_COMPANY) echo ' active'; ?>">
                            <a href="<?php echo Yii::app()->createUrl(AppUrl::URL_COMPANY); ?>"><?php echo Yii::t('strings', 'Company List'); ?></a>
                        </li>
                    <?php endif; ?>
                    <?php if ($this->hasUserAccess('customer_list')): ?>
                        <li class="list-group-item<?php if ($this->currentPage == AppUrl::URL_CUSTOMER) echo ' active'; ?>">
                            <a href="<?php echo Yii::app()->createUrl(AppUrl::URL_CUSTOMER); ?>"><?php echo Yii::t('strings', 'Customer List'); ?></a>
                        </li>
                    <?php endif; ?>
                    <?php if ($this->hasUserAccess('product_list')): ?>
                        <li<?php if ($this->currentPage == AppUrl::URL_PRODUCT_LIST) echo ' class="active"'; ?>>
                            <a href="<?php echo Yii::app()->createUrl(AppUrl::URL_PRODUCT_LIST); ?>"><?php echo Yii::t('strings', 'Product List'); ?></a>
                        </li>
                    <?php endif; ?>
                    <?php if ($this->hasUserAccess('user_list')): ?>
                        <li class="list-group-item<?php if ($this->currentPage == AppUrl::URL_USERLIST) echo ' active'; ?>">
                            <a href="<?php echo Yii::app()->createUrl(AppUrl::URL_USERLIST); ?>"><?php echo Yii::t('strings', 'User List'); ?></a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>

    <div class="panel panel-default<?php if (in_array($this->currentPage, $orderArr)) echo ' in'; ?>">
        <div class="panel-heading" role="tab" id="heading2">
            <h4 class="panel-title">
                <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse2" aria-expanded="true" aria-controls="collapse2">
                    <?php echo Yii::t('strings', 'Manage Orders'); ?>&nbsp;<span class="caret"></span>
                </a>
            </h4>
        </div>
        <div id="collapse2" class="panel-collapse collapse<?php if (in_array($this->currentPage, $orderArr)) echo ' in'; ?>" role="tabpanel" aria-labelledby="heading2">
            <div class="panel-body no_pad">
                <ul class="list-group sidebar">
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
            </div>
        </div>
    </div>

    <div class="panel panel-default<?php if (in_array($this->currentPage, $retArr)) echo ' in'; ?>">
        <div class="panel-heading" role="tab" id="heading3">
            <h4 class="panel-title">
                <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse3" aria-expanded="true" aria-controls="collapse3">
                    <?php echo Yii::t('strings', 'Return Orders'); ?>&nbsp;<span class="caret"></span>
                </a>
            </h4>
        </div>
        <div id="collapse3" class="panel-collapse collapse<?php if (in_array($this->currentPage, $retArr)) echo ' in'; ?>" role="tabpanel" aria-labelledby="heading3">
            <div class="panel-body no_pad">
                <ul class="list-group sidebar">
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
            </div>
        </div>
    </div>

    <div class="panel panel-default<?php if (in_array($this->currentPage, $ledgerArr)) echo ' in'; ?>">
        <div class="panel-heading" role="tab" id="heading4">
            <h4 class="panel-title">
                <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse4" aria-expanded="true" aria-controls="collapse4">
                    <?php echo Yii::t('strings', 'Ledger'); ?>&nbsp;<span class="caret"></span>
                </a>
            </h4>
        </div>
        <div id="collapse4" class="panel-collapse collapse<?php if (in_array($this->currentPage, $ledgerArr)) echo ' in'; ?>" role="tabpanel" aria-labelledby="heading4">
            <div class="panel-body no_pad">
                <ul class="list-group sidebar">
                    <?php if ($this->hasUserAccess('head_list')): ?>
                        <li class="list-group-item<?php if ($this->currentPage == AppUrl::URL_LEDGER_HEAD) echo ' active'; ?>">
                            <a href="<?php echo Yii::app()->createUrl(AppUrl::URL_LEDGER_HEAD); ?>"><?php echo Yii::t('strings', 'Account Heads'); ?></a>
                        </li>
                    <?php endif; ?>
                    <?php if ($this->hasUserAccess('expense_list')): ?>
                        <li class="list-group-item<?php if ($this->currentPage == AppUrl::URL_LEDGER_EXPENSE) echo ' active'; ?>">
                            <a href="<?php echo Yii::app()->createUrl(AppUrl::URL_LEDGER_EXPENSE); ?>"><?php echo Yii::t('strings', 'Expense List'); ?></a>
                        </li>
                    <?php endif; ?>
                    <?php if ($this->hasUserAccess('balance_sheet')): ?>
                        <li class="list-group-item<?php if ($this->currentPage == AppUrl::URL_LEDGER_BALANCE_SHEET) echo ' active'; ?>">
                            <a href="<?php echo Yii::app()->createUrl(AppUrl::URL_LEDGER_BALANCE_SHEET); ?>"><?php echo Yii::t('strings', 'Balance Sheet'); ?></a>
                        </li>
                    <?php endif; ?>
                    <?php if ($this->hasUserAccess('income_list')): ?>
                        <li class="list-group-item<?php if ($this->currentPage == AppUrl::URL_PROFIT) echo ' active'; ?>">
                            <a href="<?php echo Yii::app()->createUrl(AppUrl::URL_PROFIT); ?>"><?php echo Yii::t('strings', 'Income Statement'); ?></a>
                        </li>
                    <?php endif; ?>
                    <li class="list-group-item<?php if ($this->currentPage == AppUrl::URL_LEDGER_FINANCE_STATEMENT) echo ' active'; ?>">
                        <a href="<?php echo Yii::app()->createUrl(AppUrl::URL_LEDGER_FINANCE_STATEMENT); ?>"><?php echo Yii::t('strings', 'Financial Statement'); ?></a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <ul class="list-group">
        <?php if ($this->hasUserAccess('settings')): ?>
            <li class="list-group-item<?php if ($this->currentPage == AppUrl::URL_SETTINGS) echo ' active'; ?>">
                <a href="<?php echo Yii::app()->createUrl(AppUrl::URL_SETTINGS); ?>"><?php echo Yii::t('strings', 'Setting'); ?></a>
            </li>
        <?php endif; ?>
        <li class="list-group-item">
            <a href="<?php echo Yii::app()->createUrl(AppUrl::URL_USER_LOGOUT); ?>"><?php echo Yii::t('strings', 'Log Out'); ?></a>
        </li>
    </ul>
<?php endif; ?>