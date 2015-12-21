<?php	
		require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
		if (isset($_POST["xmlmsg"])) {
		$url = 'http://'.$_SERVER["HTTP_HOST"]."/personal/order/make/?ORDER_ID=".$_GET['oid'];
		if(stripos($url, "?")) $amp = "&"; else $amp = "?";
		if(stripos($_POST["xmlmsg"], "CANCELED") != false) {
		header("Location: ".$url.$amp."status=CANCELED");
		}
		else {
			
		  $xml_string = base64_decode($_POST["xmlmsg"]);
		  $parse_it = simplexml_load_string($xml_string);
		   $desc = (string)$parse_it->ResponseDescription;
		  if ($parse_it->OrderStatus[0]=="DECLINED" OR $parse_it->OrderStatus[0]=="APPROVED") header("Location: ".$url.$amp."status=".$parse_it->OrderStatus[0]."&desc=".$desc);
		 
		};
		};
		if(isset($_GET["ORDER_IDP"])) {
			header("Location: http://".$_SERVER["HTTP_HOST"]."/personal/order/make/?ORDER_ID=".$_GET["oid"]."&status=".$_GET["status"]);
		};
		require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
		?>