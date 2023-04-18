<?php
return array(
    'OnDocFormPrerender' => [
        'autentiments'
    ],
    'OnLoadWebDocument' => [
        'registerFrontVariables',
    ],
    'msOnBeforeCreateOrder' => [
        'roistat'
    ],
    'mSyncOnPrepareProduct' => [
        'mSyncModifications'
    ],
    'mSyncOnProductImport' => [
        'mSyncModifications'
    ],
    'mSyncOnProductOffers' => [
        'mSyncModifications'
    ],
    'mSyncOnSalesExport' => [
        'mSyncModifications'
    ],
    'SMSAfterCodeCheck' => [
        'loginAndRegister'
    ],
    'SMSCodeActivate' => [
        'loginAndRegister'
    ],
    'msOnCreateOrder' => [
        'loginAndRegister',
        'ModificationsRemains'
    ],
    'msOnBeforeGetOrderCustomer' => [
        'loginAndRegister'
    ],
    'msOnChangeOrderStatus' => [
        'certificate'
    ],
    'OnUserSave' => [
        'loginAndRegister'
    ],
    'OnModificationRemainsUpdate' => [
        'subscribersSend'
    ],
    'msOnAddToCart' => [
        'refreshPrices'
    ],
    'msOnChangeInCart' => [
        'refreshPrices'
    ],
    'msOnGetStatusCart' => [
        'refreshPrices'
    ],
    'msOnRemoveFromCart' => [
        'refreshPrices'
    ],
    'msOnValidateOrderValue' => [
        'ms2validate'
    ],
    "OnWebPageInit" => [
        "authMgrOnWeb"
    ],
    "msOnGetStatusCart" => [
        "realTotalCost"
    ]
);