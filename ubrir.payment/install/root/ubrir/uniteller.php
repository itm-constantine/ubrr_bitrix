<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/include.php");

if (isset($_POST['SIGN'])) {
				$sign = strtoupper(md5(md5($_POST['SHOP_ID']).'&'.md5($_POST["ORDER_ID"]).'&'.md5($_POST['STATE'])));
				if ($_POST['SIGN'] == $sign) {
					switch ($_POST['STATE']) {
						case 'paid':
						 CSaleOrder::Update($_POST["ORDER_ID"], array("PAYED" => "Y"));
						 CSaleOrder::StatusOrder($_POST["ORDER_ID"], "P");
						 echo '<div class="ubr_s">Оплата успешно совершена</div>';
						  break;
					  }
			    }
			}  

?>