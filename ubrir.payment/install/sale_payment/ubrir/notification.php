<?php

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/bx_root.php");

if(file_exists($_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/html_pages/.enabled"))
{
    define("BITRIX_STATIC_PAGES", true);
    require_once(dirname(__FILE__)."/../classes/general/cache_html.php");
    CHTMLPagesCache::startCaching();
}

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

CModule::IncludeModule('sale');

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
include(GetLangFileName(dirname(__FILE__)."/", "/ubrir.php"));

include(dirname(__FILE__)."/sdk/ubrir_autoload.php");

$orderID = $_REQUEST['OrderId'];
$order = CSaleOrder::GetByID($orderID);

if(!$order){
    // ORDER NOT FOUND
    die('NOT FOUND');
}

CSalePaySystemAction::InitParamArrays($orderID, $orderID);

$bankHandler = new ubrir(CSalePaySystemAction::GetParamValue("TERMINAL_ID"), CSalePaySystemAction::GetParamValue("SHOP_SECRET_WORD"), CSalePaySystemAction::GetParamValue("ubrir_PAYMENT_URL"));

try{
    $bankHandler->checkNotification($_POST);
} catch(ubrirException $e){
    die($e->getMessage());
}

if($bankHandler->isOrderFailed()){
    CSaleOrder::PayOrder($orderID, 'N');
} elseif($bankHandler->isOrderPaid()){
    CSaleOrder::PayOrder($orderID, 'Y');
}
?>
OK
