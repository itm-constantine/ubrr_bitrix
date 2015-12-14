<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?><?
include(GetLangFileName(dirname(__FILE__)."/", "/ubrir.php"));
include(dirname(__FILE__)."/sdk/ubrir_autoload.php");
include(dirname(__FILE__)."/view/style.php");

CJSCore::Init(array("jquery"));

$out = null;
$order_id = '';

include(dirname(__FILE__)."/include/description_post.php");
	

$psTitle = '';
$psDescription = $toprint;

$arPSCorrespondence = array(
	"ID" => array(
		"NAME" => GetMessage("BXMOD_ROBOX_PARAM_SHOP_ID"),
		"VALUE" => "",
		"TYPE" => ""
	),
	
	"SERT" => array(
		"NAME" => GetMessage("BXMOD_ROBOX_PARAM_SHOP_SERT"),
		"VALUE" => "",
		"TYPE" => ""
	),
	"UNI_ID" => array(
		"NAME" => GetMessage("BXMOD_ROBOX_PARAM_SHOP_UNI_ID"),
		"VALUE" => "",
		"TYPE" => ""
	),
	"UNI_LOGIN" => array(
		"NAME" => GetMessage("BXMOD_ROBOX_PARAM_SHOP_UNI_LOGIN"),
		"VALUE" => "",
		"TYPE" => ""
	),
	"UNI_PASS" => array(
		"NAME" => GetMessage("BXMOD_ROBOX_PARAM_SHOP_UNI_PASS"),
		"VALUE" => "",
		"TYPE" => ""
	),
	"UNI_EMP" => array(
		"NAME" => GetMessage("BXMOD_ROBOX_PARAM_SHOP_UNI_EMP"),
		"VALUE" => "",
		"TYPE" => ""
	),
	"TWO" => array(
		"NAME" => GetMessage("BXMOD_ROBOX_PARAM_SHOP_TWO"),
		"VALUE" => array(
			'Y' =>   array('NAME' => 'Включено'),
			'N' =>   array('NAME' => 'Отключено'),
		),
		"TYPE" => "SELECT",
		"DEFAULT" => "Y",
	),
	
	
	"ORDER_ID" => array(
		"NAME" => GetMessage("SALE_ORDER_ID_NAME"),
		"DESCR" => '',
		"VALUE" => "ID",
		"TYPE" => "ORDER"
	),
	
	"SHOULD_PAY" => array(
		"NAME" => GetMessage("SALE_SHOULD_PAY_NAME"),
		"DESCR" => '',
		"VALUE" => "SHOULD_PAY",
		"TYPE" => "ORDER"
	),
	"PAYMENT_DESCRIPTION" => array(
		"NAME" => GetMessage("SALE_DESCRIPTION_NAME"),
		"VALUE" => '',
		"TYPE" => ""
	),
);
?>
