<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
?>
<?
include(GetLangFileName(dirname(__FILE__)."/", "/ubrir.php"));
include(dirname(__FILE__)."/sdk/ubrir_autoload.php");
include(dirname(__FILE__)."/view/style.php");

$shouldPay = (strlen(CSalePaySystemAction::GetParamValue("SHOULD_PAY")) > 0) ? 
CSalePaySystemAction::GetParamValue("SHOULD_PAY") : $GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["SHOULD_PAY"];
$orderID = (strlen(CSalePaySystemAction::GetParamValue("ORDER_ID")) > 0) ? 
CSalePaySystemAction::GetParamValue("ORDER_ID") : $GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["ID"];

$arOrder = CSaleOrder::GetByID(CSalePaySystemAction::GetParamValue("ORDER_ID"));                                                            // получаем текущий заказ


if(!isset($_GET['status'])) {		

	/* ---------------- если операция еще не совершена -------------- */

	$readyToPay = false;                                                                                                                     // возможность платежа
	$bankHandler = new Ubrir(array(																											 // инициализируем объект операции в TWPG
							'shopId' => CSalePaySystemAction::GetParamValue("ID"), 
							'order_id' => CSalePaySystemAction::GetParamValue("ORDER_ID"), 
							'sert' => CSalePaySystemAction::GetParamValue("SERT"),
							'amount' => CSalePaySystemAction::GetParamValue("SHOULD_PAY")
							));                    
	$response_order = $bankHandler->prepare_to_pay();                                       												// что вернул банк

	include(dirname(__FILE__)."/include/twpg_db.php");	
	   
    if($readyToPay AND !empty($response_order)) { 
		$twpg_url = $response_order->URL[0].'?orderid='.$response_order->OrderID[0].'&sessionid='.$response_order->SessionID[0];
		echo '<INPUT TYPE="button" value="Оплатить Visa" onclick="document.location = \''.$twpg_url.'\'">';
	}
	 
	if(CSalePaySystemAction::GetParamValue("TWO") == 'Y') {                                                                               // если активны два процессинга, то работаем еще и с Uniteller
	   echo ' <INPUT TYPE="button" onclick="document.forms.uniteller.submit()" value="Оплатить MasterCard">';
           include(dirname(__FILE__)."/include/uni_form.php");
	  };

}

    /* ----------------- если она совершена ---------------------- */ 

else {             

	$status = htmlspecialchars(stripslashes($_GET['status']));                                                                                                    
	
	switch ($status) {
				case 'APPROVED':
					include(dirname(__FILE__)."/include/twpg_approved.php");
					break;
					
				case 'CANCELED':
					echo '<div class="ubr_f">Оплата отменена пользователем</div>';
					break;
					
				case 'DECLINED':
					echo '<div class="ubr_f">Оплата отклонена банком</div>';
					break;

				case '0':
					echo '<div class="ubr_f">Оплата не совершена</div>';                                                                                          //эти два пункта по Юнителлеру
					break;		
					
				case '1':
					echo '<div class="ubr_s">Оплата совершена успешно, ожидайте обработки заказа</div>';
					break;			
					
				default:
					# code...
					break;
			}
			
	}
	

?>
