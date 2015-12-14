<?php
					$id = trim(CSalePaySystemAction::GetParamValue("UNI_ID"));
	  $login = trim(CSalePaySystemAction::GetParamValue("UNI_LOGIN"));
	  $pass = trim(CSalePaySystemAction::GetParamValue("UNI_PASS")); 
	  $orderid = CSalePaySystemAction::GetParamValue("ORDER_ID");   
	  $amount = CSalePaySystemAction::GetParamValue("SHOULD_PAY");    
	  $callbackurl = "http://".$_SERVER["HTTP_HOST"].'/ubrir/bank.php';
	  $sign = strtoupper(md5(md5($id).'&'.md5($login).'&'.md5($pass).'&'.md5($orderid).'&'.md5($amount)));
	  echo '<form action="https://91.208.121.201/estore_listener.php" name="uniteller" method="post">
		<input type="hidden" name="SHOP_ID" value="'.$id.'" >
		<input type="hidden" name="LOGIN" value="'.$login.'" >
		<input type="hidden" name="ORDER_ID" value="'.$orderid.'">
		<input type="hidden" name="PAY_SUM" value="'.$amount.'" >
		<input type="hidden" name="VALUE_1" value="'.$orderid.'" >
		<input type="hidden" name="URL_OK" value="'.$callbackurl.'?status=1&oid='.$orderid.'&" >
		<input type="hidden" name="URL_NO" value="'.$callbackurl.'?status=0&oid='.$orderid.'&" >
		<input type="hidden" name="SIGN" value="'.$sign.'" >
		<input type="hidden" name="LANG" value="RU" >
	  </form>';
					
?>

