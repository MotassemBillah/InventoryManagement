<?php

class AppUrl {

    //Error
    const URL_ERROR = '/error';
    const URL_ERROR_MESSAGE = '/error/message';
    //URL Account
    const URL_ACCOUNT = '/account';
    const URL_ACCOUNT_CREATE = '/account/create';
    const URL_ACCOUNT_EDIT = '/account/edit';
    const URL_ACCOUNT_BALANCE = '/account/balance';
    const URL_ACCOUNT_BALANCE_ADD = '/account/balance_add';
    const URL_ACCOUNT_BALANCE_EDIT = '/account/balance_edit';
    const URL_ACCOUNT_DELETE = '/account/delete';
    const URL_ACCOUNT_DELETEALL = '/account/deleteall';
    //URL Cash Account
    const URL_CASH_ACCOUNT = '/cash_account';
    const URL_CASH_ACCOUNT_DEPOSIT = '/cash_account/deposit';
    const URL_CASH_ACCOUNT_DEPOSIT_EDIT = '/cash_account/deposit_edit';
    const URL_CASH_ACCOUNT_WITHDRAW = '/cash_account/withdraw';
    const URL_CASH_ACCOUNT_WITHDRAW_EDIT = '/cash_account/withdraw_edit';
    const URL_CASH_ACCOUNT_VOUCHER = '/cash_account/view';
    //URL Branch
    const URL_BRANCH = '/branch';
    const URL_BRANCH_CREATE = '/branch/create';
    const URL_BRANCH_EDIT = '/branch/edit';
    const URL_BRANCH_VIEW = '/branch/view';
    const URL_BRANCH_ACTIVATE = '/branch/activate';
    const URL_BRANCH_DEACTIVATE = '/branch/deactivate';
    // Dashboard And Settings
    const URL_DASHBOARD = '/dashboard';
    const URL_VISITORS = '/dashboard/visitors';
    //URL Password
    const URL_PASSWORD_FORGET = '/password';
    const URL_PASSWORD_ACTIVATE = '/password/activate';
    const URL_PASSWORD_CHANGE = '/password/change';
    const URL_PASSWORD_RECOVER = '/password/recover';
    const URL_PASSWORD_RESET = '/password/reset';
    //URL Users
    const URL_LOGIN = '/login';
    const URL_USERLIST = '/user';
    const URL_USER_CREATE = '/user/create';
    const URL_USER_EDIT = '/user/edit';
    const URL_USER_ADMIN_EDIT = '/user/admin_edit';
    const URL_USER_DELETE = '/user/delete';
    const URL_USER_DELETEALL = '/user/deleteall';
    const URL_USER_ACTIVATE = '/user/activate';
    const URL_USER_DEACTIVATE = '/user/deactivate';
    const URL_USER_PROFILE = '/user/profile';
    const URL_USER_PERMISSION = '/user/permission';
    const URL_USER_DETAILS = '/user/details';
    const URL_USER_LOGOUT = '/user/logout';
    const URL_USER_ACTIVATION = '/user/activation';
    const URL_USER_REMOVE_LOGIN = '/user/remove_login';
    //URL Site
    const URL_SITE = '/site';
    const URL_SITE_MESSAGE = '/site/message';
    const URL_SITE_CONTACT = '/site/contact';
    const URL_CLEAR_CACHE = '/site/clear_cache';
    //URL Banks
    const URL_BANK = '/bank';
    const URL_BANK_CREATE = '/bank/create';
    const URL_BANK_EDIT = '/bank/edit';
    const URL_BANK_DELETE = '/bank/delete';
    const URL_BANK_DELETEALL = '/bank/deleteall';
    //URL Categories
    const URL_CATEGORIES = '/category';
    const URL_CATEGORIES_CREATE = '/category/create';
    const URL_CATEGORIES_EDIT = '/category/edit';
    const URL_CATEGORIES_DELETE = '/category/delete';
    const URL_CATEGORIES_DELETEALL = '/category/deleteall';
    // Dues
    const URL_DUES = '/dues';
    const URL_DUES_DELETE = '/dues/delete';
    const URL_DUES_DELETEALL = '/dues/deleteall';
    //URL Company
    const URL_COMPANY = '/company';
    const URL_COMPANY_CREATE = '/company/create';
    const URL_COMPANY_EDIT = '/company/edit';
    const URL_COMPANY_PAYMENT = '/company/payment';
    const URL_COMPANY_PAYMENT_CREATE = '/company/payment_create';
    const URL_COMPANY_PAYMENT_EDIT = '/company/payment_edit';
    const URL_COMPANY_DELETE = '/company/delete';
    const URL_COMPANY_DELETEALL = '/company/deleteall';
    const URL_COMPANY_ACTIVATE = '/company/activate';
    const URL_COMPANY_META_DELETE = '/company/deletemeta';
    const URL_COMPANY_DELETED_LIST = '/company/deleted_list';
    //URL Dealer
    const URL_CUSTOMER = '/customer';
    const URL_CUSTOMER_CREATE = '/customer/create';
    const URL_CUSTOMER_EDIT = '/customer/edit';
    const URL_CUSTOMER_DELETE = '/customer/delete';
    const URL_CUSTOMER_DELETEALL = '/customer/deleteall';
    const URL_CUSTOMER_PAYMENT = '/customer/payment';
    const URL_CUSTOMER_PAYMENT_CREATE = '/customer/payment_create';
    const URL_CUSTOMER_PAYMENT_EDIT = '/customer/payment_edit';
    //URL Permission
    const URL_PERMISSION = '/permission';
    const URL_PERMISSION_CREATE = '/permission/create';
    const URL_PERMISSION_EDIT = '/permission/edit';
    const URL_PERMISSION_DELETE = '/permission/delete';
    const URL_PERMISSION_DELETEALL = '/permission/deleteall';
    //URL Products
    const URL_PRODUCT = '/product';
    const URL_PRODUCT_LIST = '/product/list';
    const URL_PRODUCT_CREATE = '/product/create';
    const URL_PRODUCT_EDIT = '/product/edit';
    const URL_PRODUCT_DELETE = '/product/delete';
    const URL_PRODUCT_DELETE_ALL = '/product/deleteall';
    const URL_PRODUCT_DAMAGED = '/product/damaged';
    //URL Products Sizess
    const URL_SIZE = '/size';
    const URL_SIZE_CREATE = '/size/create';
    const URL_SIZE_EDIT = '/size/edit';
    const URL_SIZE_DELETE = '/size/delete';
    const URL_SIZE_DELETEALL = '/size/deleteall';
    //URL Products Location
    const URL_LOCATION = '/location';
    const URL_LOCATION_CREATE = '/location/create';
    const URL_LOCATION_EDIT = '/location/edit';
    const URL_LOCATION_DELETE = '/location/delete';
    const URL_LOCATION_DELETEALL = '/location/deleteall';
    //URL Role
    const URL_ROLE = '/role';
    const URL_ROLE_CREATE = '/role/create';
    const URL_ROLE_EDIT = '/role/edit';
    const URL_ROLE_DELETE = '/role/delete';
    const URL_ROLE_DELETEALL = '/role/deleteall';
    const URL_ROLE_ACTIVATE = '/role/activate';
    //URL Purchases
    const URL_PURCHASE = '/purchase';
    const URL_PURCHASE_CREATE = '/purchase/create';
    const URL_PURCHASE_EDIT = '/purchase/edit';
    const URL_PURCHASE_DELETE = '/purchase/delete';
    const URL_PURCHASE_DELETE_ALL = '/purchase/deleteall';
    const URL_PURCHASE_PROCESS = '/purchase/process';
    const URL_PURCHASE_VIEW = '/purchase/view';
    const URL_PURCHASE_ITEMS = '/purchase/items';
    const URL_PURCHASE_PAYMENT = '/purchase/payment';
    const URL_PURCHASE_ITEM_STATUS = '/purchase/change_item_status';
    const URL_PURCHASE_TRUNCATE = '/purchase/truncate_data';
    const URL_PURCHASE_RESET = '/purchase/reset';
    //URL Sales
    const URL_SALE = '/sales';
    const URL_SALE_CREATE = '/sales/create';
    const URL_SALE_EDIT = '/sales/edit';
    const URL_SALE_DELETE = '/sales/delete';
    const URL_SALE_DELETE_ALL = '/sales/deleteall';
    const URL_SALE_PROCESS = '/sales/process';
    const URL_SALE_VIEW = '/sales/view';
    const URL_SALE_ITEMS = '/sales/items';
    const URL_SALE_PAYMENT = '/sales/payment';
    const URL_SALE_ITEM_STATUS = '/sales/change_item_status';
    const URL_SALE_CART = '/sales/cart';
    const URL_SALE_RESET = '/sales/reset';
    const URL_SALERETURN = '/sales_return';
    const URL_SALERETURN_CREATE = '/sales_return/create';
    const URL_SALERETURN_EDIT = '/sales_return/edit';
    const URL_SALERETURN_VIEW = '/sales_return/view';
    //URL Reports
    const URL_REPORT = '/report';
    //URL Payment
    const URL_PAYMENT = '/payments';
    const URL_PAYMENT_CREATE = '/payments/create';
    const URL_PAYMENT_EDIT = '/payments/edit';
    const URL_PAYMENT_DELETE = '/payments/delete';
    const URL_PAYMENT_DELETEALL = '/payments/deleteall';
    const URL_PAYMENT_OPTIONS = '/payments/options';
    //URL Settings
    const URL_SETTINGS = '/settings';
    //URL Ledger
    const URL_LEDGER = '/ledger';
    const URL_LEDGER_HEAD = '/ledger/head';
    const URL_LEDGER_HEAD_CREATE = '/ledger/head/create';
    const URL_LEDGER_HEAD_EDIT = '/ledger/head/edit';
    const URL_LEDGER_HEAD_VIEW = '/ledger/head/view';
    const URL_LEDGER_INCOME = '/ledger/income';
    const URL_LEDGER_EXPENSE = '/ledger/expense';
    const URL_LEDGER_EXPENSE_CREATE = '/ledger/expense/create';
    const URL_LEDGER_EXPENSE_EDIT = '/ledger/expense/edit';
    const URL_LEDGER_EXPENSE_VIEW = '/ledger/expense/view';
    const URL_LEDGER_BALANCE_SHEET = '/ledger/balancesheet';
    const URL_LEDGER_BALANCE_SHEET_UPDATE = '/ledger/balancesheet/update';
    const URL_LEDGER_SETTINGS = '/ledger/settings';
    const URL_LEDGER_FINANCE_STATEMENT = '/ledger/statement';
    //URL Profit
    const URL_PROFIT = '/ledger/profit';
    const URL_PROFIT_CREATE = '/ledger/profit/create';
    const URL_PROFIT_EDIT = '/ledger/profit/edit';
    const URL_PROFIT_DELETEALL = '/ledger/profit/deleteall';
    // History url
    const URL_HISTORY = '/history';
    const URL_HISTORY_VIEW = '/history/view';
    const URL_HISTORY_CLEAR = '/history/clear';
    const URL_HISTORY_DELETE = '/history/delete';

}
