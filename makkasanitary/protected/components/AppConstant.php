<?php

class AppConstant {

    const MAIL_SENDER_EMAIL = 'rakibhasan880@gmail.com';
    const MAIL_MANDRILL_API = 'yaWRn4R15FlrlfYq9dZe0A';
    const PASSWORD_LENGTH = 6;
    const GENDER_MALE = "Male";
    const GENDER_FEMALE = "Female";
    const INITIAL_AMOUNT = 500;
    const INITIAL_BALANCE = 'Opening Balance';
    const CASH_IN = 'Debit';
    const CASH_OUT = 'Credit';
    const CASH_DEPOSIT = 'diposit';
    const CASH_WITHDRAW = 'withdraw';
    const PAYMENT_CASH = 'Cash Payment';
    const PAYMENT_CHECK = 'Cheque Payment';
    const PAYMENT_NO = 'No Payment';
    //Status
    const USER_STATUS_ACTIVE = 1;
    const USER_STATUS_INACTIVE = 0;
    const USER_STATUS_BLOCKED = 2;
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;
    //Roles
    const ROLE_SUPERADMIN = 1;
    const ROLE_ADMIN = 2;
    const ROLE_CUSTOMER = 3;
    const BRANCH_INACTIVE = 0;
    const BRANCH_ACTIVE = 1;
    //Report Type
    const TYPE_ADVANCE = 'advance';
    const TYPE_INVOICE = 'invoice';
    const TYPE_DUE = 'due';
    const TYPE_DUE_PAID = 'due paid';
    const TYPE_PREVIOUS_DUE = 'previous due';
    const STOK_TYPE_PURCHASE = 'Purchase';
    const STOK_TYPE_PURCHASE_RETURN = 'Purchase Return';
    const STOK_TYPE_SALE = 'Sale';
    const STOK_TYPE_SALE_RETURN = 'Sale Return';
    const ORDER_COMPLETE = 'Complete';
    const ORDER_DELIVERED = 'Delivered';
    const ORDER_PENDING = 'Pending';
    const ORDER_RETURNED = 'Returned';
    const PRODUCT_RETURN = 'Product Return';
    // Customer Type
    const CTYPE_ADVANCE = 'Advance';
    const CTYPE_DUE = 'Due';
    const CTYPE_REGULAR = 'Regular';
    // Fixed Heads
    const HEAD_PURCHASE = 1;
    const HEAD_SALE = 2;
    const HEAD_SALARY = 3;
    const HEAD_DOKAN_VARA = 12;
    const HEAD_CASH_TO_BANK = 5;

}
