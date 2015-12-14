﻿<?php

class Ubrir

{

    const STATUS_CANCELED = 'CANCELED';

 
    const STATUS_DECLINED = 'DECLINED';

	
    const STATUS_APPROVED = 'APPROVED';

    
	
    public $shopId;
	
	private $order_id;
	
	private $sert;
	
	private $twpg_order_id;
	
	private $twpg_session_id;

    private $amount;

    private $paymentUrl;

    private $paymentStatus;
  
	private $uni_login;

    private $uni_pass;
  
  
  
    public function __construct($props) 
	{
		foreach ($props as $k => $v) {
			if (property_exists($this, $k))
        $this->$k = $v;
		} 

    }
	
	
	
	/*-------------------- основные методы ----------------------*/
	
	
	
	public function prepare_to_pay()
	{
	   $url = htmlspecialchars('http://'.$_SERVER[HTTP_HOST].'/ubrir/bank.php?oid='.$this->order_id);
       $amount = $this->amount*100;
	   $data = '<?xml version = "1.0" encoding = "UTF-8"?>
              <TKKPG>
                <Request>
                  <Operation>CreateOrder</Operation>
                  <Language>RU</Language>
                  <Order>
                    <OrderType>Purchase</OrderType>
                    <Merchant>'.$this->shopId.'</Merchant>
                    <Amount>'.$amount.'</Amount>
                    <Currency>643</Currency>
                    <Description>РўРѕРІР°СЂС‹</Description>
                    <ApproveURL>'.$url.'</ApproveURL>
                    <CancelURL>'.$url.'</CancelURL>
                    <DeclineURL>'.$url.'</DeclineURL>
                  </Order>
                </Request>
              </TKKPG>
              ';	
			
			return $this->xml_extract_result($this->send_xml($data));
			
   }
	
	
	
	public function check_status($status=null) 
	{
		$data = '<?xml version = "1.0" encoding = "UTF-8"?>
              <TKKPG>
                <Request>
                  <Operation>GetOrderStatus</Operation>
                  <Language>RU</Language>
                  <Order>
                    <Merchant>'.$this->shopId.'</Merchant>
                    <OrderID>'.$this->twpg_order_id.'</OrderID>
                  </Order>
                  <SessionID>'.$this->twpg_session_id.'</SessionID>
                </Request>
              </TKKPG>
          ';
		 return $this->xml_extract_status_result($this->send_xml($data), $status);
    }
	
	
	
	
	public function detailed_status() 
	{
		$data = '<?xml version = "1.0" encoding = "UTF-8"?>
                  <TKKPG>
                    <Request>
                      <Operation>GetOrderInformation</Operation>
                      <Language>RU</Language>
                      <Order>
                        <Merchant>'.$this->shopId.'</Merchant>
                        <OrderID>'.$this->twpg_order_id.'</OrderID>
                      </Order>
                      <SessionID>'.$this->twpg_session_id.'</SessionID>
                      <ShowParams>true</ShowParams>
                      <ShowOperations>true</ShowOperations>
                      <ClassicView>true</ClassicView>
                    </Request>
                  </TKKPG>
          ';
		 return $this->xml_extract_detailed_result($this->send_xml($data));
    }
	
	
	
	
	public function reverse_order() 
	{
		$tranid_req = '<?xml version = "1.0" encoding = "UTF-8"?>
            <TKKPG>
              <Request>
                <Operation>GetOrderInformation</Operation>
                <Language>RU</Language>
                <Order>
                  <Merchant>'.$this->shopId.'</Merchant>
                  <OrderID>'.$this->twpg_order_id.'</OrderID>
                </Order>
                <SessionID>'.$this->twpg_session_id.'</SessionID>
                <ShowParams>true</ShowParams>
                <ShowOperations>true</ShowOperations>
                <ClassicView>true</ClassicView>
              </Request>
            </TKKPG>
          ';
          $tranid_resp = $this->send_xml($tranid_req);
          $tranid_parse = simplexml_load_string($tranid_resp);
          $tran_rows = $tranid_parse->Response->Order->row->OrderOperations->row;
          for ($i=0; $i < sizeof($tran_rows); $i++) { 
            if ($tran_rows[$i]->OperName[0] == 'Purchase') {
              $tranid = $tran_rows[$i]->twoId[0];
            }
          }
          $data = '<?xml version = "1.0" encoding = "UTF-8"?>
            <TKKPG>
              <Request>
                <Operation>Reverse</Operation>
                <Language>RU</Language>
                <Order>
                  <Merchant>'.$this->shopId.'</Merchant>
                  <OrderID>'.$this->twpg_order_id.'</OrderID>
                </Order>
                <SessionID>'.$this->twpg_session_id.'</SessionID>
                <TranID>'.$tranid.'</TranID>
              </Request>
            </TKKPG>
            ';
          return $this->xml_extract_reverse_result($this->send_xml($data));
    }
	
	
	
	
	public function reconcile() 
	{
		$data = '<?xml version = "1.0" encoding = "UTF-8"?>
                <TKKPG>
                  <Request>
                    <Operation>Reconcile</Operation>
                    <Language>RU</Language>
                    <Merchant>'.$this->shopId.'</Merchant>
                  </Request>
                </TKKPG>
                ';
		 return $this->xml_extract_reconcile_result($this->send_xml($data));
    }
	

	
	
	public function extract_journal() 
	{
		  $data = '<?xml version = "1.0" encoding = "UTF-8"?>
                <TKKPG>
                  <Request>
                    <Operation>TransactionLog</Operation>
                    <Language>RU</Language>
                    <Merchant>'.$this->shopId.'</Merchant>
                  </Request>
                </TKKPG>
                ';
		 return $this->xml_extract_journal_result($this->send_xml($data));
    }
	
	
	
	
	public function uni_journal() 
	{
		 $data="LOGIN=".$this->uni_login;
		 $data.="&PSWD=".$this->uni_pass;
         $uni_ch = curl_init(); 
         curl_setopt($uni_ch, CURLOPT_URL, "https://91.208.121.201/estore_result.php");
         curl_setopt($uni_ch, CURLOPT_POST, 1);
         curl_setopt($uni_ch, CURLOPT_SSL_VERIFYPEER, false);
         curl_setopt($uni_ch, CURLOPT_SSL_VERIFYHOST, false);
         curl_setopt($uni_ch, CURLOPT_RETURNTRANSFER, 1);
         curl_setopt($uni_ch, CURLOPT_POSTFIELDS, $data);
         if( ! $curl_answer = curl_exec($uni_ch)) { 
           echo curl_error($uni_ch)."<br>";
           echo curl_errno($uni_ch);
         } 
        curl_close($uni_ch);
                
		 return $this->xml_extract_uni_journal($curl_answer);
    }
	
	
	
	
	public function create_file($url) 
	{	
		$fp = fopen($_SERVER["DOCUMENT_ROOT"]."/ubrir/bank".$this->order_id.".php", "w");   // создаем обработчик
		fwrite($fp, '<?php	
		require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
		$shop_order = '.$this->order_id.';
		$sid = '.$this->shopId.';
		if (isset($_POST["xmlmsg"])) {
		$url = "'.$url.'";
		$order = '.$this->twpg_order_id.';
		$session = "'.$this->twpg_session_id.'";
		if(stripos($url, "?")) $amp = "&"; else $amp = "?";
		if(stripos($_POST["xmlmsg"], "CANCELED") != false)  header("Location: ".$url.$amp."status=CANCELED");
		else {
			
		  $xml_string = base64_decode($_POST["xmlmsg"]);
		  $parse_it = simplexml_load_string($xml_string);
		   
		  if ($parse_it->OrderStatus[0]=="DECLINED" OR $parse_it->OrderStatus[0]=="APPROVED") header("Location: ".$url.$amp."status=".$parse_it->OrderStatus[0]);
		 
		};
		};
		if(isset($_GET["ORDER_IDP"])) {
			header("Location: http://".$_SERVER["HTTP_HOST"]."/personal/order/make/?ORDER_ID=".$shop_order."&status=".$_GET["status"]);
		};
		require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
		?>');
		fclose($fp);
    }
	
	
	
	
	/* ------------------ вспомогательные -------------------- */
	
	
	
	
	
	protected function xml_extract_result($xml_string) 
	{
			$parse_it = simplexml_load_string($xml_string);
			 
			$response_status = $parse_it->Response->Status[0];
			switch ($response_status) {
				case '00': {
					$this->twpg_order_id = $parse_it->Response->Order->OrderID[0];
					$this->twpg_session_id = $parse_it->Response->Order->SessionID[0];
					return $parse_it->Response->Order;
					break;
				  };
					
				case '30';
					throw new UbrirException(sprintf('Неверные данные или заполнены не все поля'));
					break;
					
				default:
					# code...
					break;
			}
		}
					
		
	protected function xml_extract_status_result($xml, $o_status) 
	{
		$parse_it = simplexml_load_string($xml);
		if(!empty($parse_it->Response->Order->OrderStatus[0])) {
			$status = $parse_it->Response->Order->OrderStatus[0];
			if($o_status == null) return $this->status_decode($status);
			elseif($status == $o_status) return true;
			else return false;
			}
		else return false;
	  }
	  
	  
	  
	 protected function status_decode($status)
	{
		  switch ($status) {
          case 'APPROVED':
            return 'Одобрен (оплата прошла успешно)';
            break;
          case 'CANCELED':
            return 'Отменен (клиент прервал выполнение операции)';
            break;
          case 'DECLINED':
            return 'Отказ в оплате';
            break;
          case 'REVERSED':
            return 'Реверсирован';
            break;    
          case 'REFUNDED':
			return 'Осуществлен возврат товара';
            break;    
          case 'PREAUTH-APPROVED':
			return 'Выполнена предавторизация';
            break;     
		 case 'EXPIRED':
			return 'Истек срок действия заказа';
            break; 
		 case 'ON-PAYMENT':
			return 'На оплате';
            break; 		
		 default:
			return $status;
			break;
        }
	  } 
	  
	  
	protected function type_decode($type)
	{
		 switch ($type) {
          case 'Purchase':
            return 'Покупка';
            break;	
		  	case 'Reverse':
            return 'Реверс оплаты';
            break;	
		 default:
			return $type;
			break;
        }
	  } 
	  
	  
	  protected function oper_decode($oper)
	{
		 switch ($oper) {
          case 'CreateOrder':
            return 'Создание заказа на покупку';
            break;	
		  case 'GetOrderStatus':
            return 'Получение статуса заказа';
            break;
		  case 'Purchase':
            return 'Покупка';
            break;		
		  case 'Reverse':
            return 'Реверс оплаты';
            break;	
		 case 'Reconcile':
            return 'Сверка итогов';
            break;	
		 case 'TransactionLog':
            return 'Журнал операций';
            break;	
		case 'GetOrderInformation':
            return 'Получение информации о заказе';
            break;	
		 default:
			return $oper;
			break;
        }
        }
	  
	  
	  
	protected function xml_extract_detailed_result($xml) {
        $parse_it = simplexml_load_string($xml);
        $status = $parse_it->Response->Status[0];
        $row = $parse_it->Response->Order->row;
		$out = '';
          switch ($status) {
          case '00':
            $out .=  "<table class='twpgdt' style='border: 1px dashed #999;'><tr><td>ID</td><td>".$row->id[0]."</td></tr>";
            $out .=  "<tr><td>SessionID</td><td>".$row->SessionID[0]."</td></tr>";
            $out .=  "<tr><td>createDate</td><td>".$row->createDate[0]."</td></tr>";
            $out .=  "<tr><td>lastUpdateDate</td><td>".$row->lastUpdateDate[0]."</td></tr>";
            $out .=  "<tr><td>payDate</td><td>".$row->payDate[0]."</td></tr>";
            $out .=  "<tr><td>MerchantID</td><td>".$row->MerchantID[0]."</td></tr>";
            $out .=  "<tr><td>Amount</td><td>".$row->Amount[0]."</td></tr>";
            $out .=  "<tr><td>Currency</td><td>".$row->Currency[0]."</td></tr>";
            $out .=  "<tr><td>OrderLanguage</td><td>".$row->OrderLanguage[0]."</td></tr>";
            $out .=  "<tr><td>Description</td><td>".$row->Description[0]."</td></tr>";
            $out .=  "<tr><td>ApproveURL</td><td>".$row->ApproveURL[0]."</td></tr>";
            $out .=  "<tr><td>CancelURL</td><td>".$row->CancelURL[0]."</td></tr>";
            $out .=  "<tr><td>DeclineURL</td><td>".$row->DeclineURL[0]."</td></tr>";
            $out .=  "<tr><td>Orderstatus</td><td>".$this->status_decode($row->Orderstatus[0])."</td></tr>";
            $out .=  "<tr><td>RefundAmount</td><td>".$row->RefundAmount[0]."</td></tr>";
            $out .=  "<tr><td>RefundCurrency</td><td>".$row->RefundCurrency[0]."</td></tr>";
            $out .=  "<tr><td>ExtSystemProcess</td><td>".$row->ExtSystemProcess[0]."</td></tr>";
            $out .=  "<tr><td>OrderType</td><td>".$this->type_decode($row->OrderType[0])."</td></tr>";
            $out .=  "<tr><td>Fee</td><td>".$row->Fee[0]."</td></tr>";
            $out .=  "<tr><td>RefundDate</td><td>".$row->RefundDate[0]."</td></tr>";
            $out .=  "<tr><td>TWODate</td><td>".$row->TWODate[0]."</td></tr>";
            $out .=  "<tr><td>TWOTime</td><td>".$row->TWOTime[0]."</td></tr>";
            $operations = $row->OrderOperations->row;
            for ($i=0; $i < count($operations) ; $i++) { 
              $out .=  "<tr><td>id</td><td>".$operations[$i]->id[0]."</td></tr>";
              $out .=  "<tr><td>PackageId</td><td>".$operations[$i]->PackageId[0]."</td></tr>";
              $out .=  "<tr><td>createDate</td><td>".$operations[$i]->createDate[0]."</td></tr>";
              $out .=  "<tr><td>MerchantID</td><td>".$operations[$i]->MerchantID[0]."</td></tr>";
              $out .=  "<tr><td>OperType</td><td>".$operations[$i]->OperType[0]."</td></tr>";
              $out .=  "<tr><td>OperName</td><td>".$this->oper_decode($operations[$i]->OperName[0])."</td></tr>";
              $out .=  "<tr><td>OrderId</td><td>".$operations[$i]->OrderId[0]."</td></tr>";
              $out .=  "<tr><td>Amount</td><td>".$operations[$i]->Amount[0]."</td></tr>";
              $out .=  "<tr><td>Currency</td><td>".$operations[$i]->Currency[0]."</td></tr>";
              $out .=  "<tr><td>Approval</td><td>".$operations[$i]->Approval[0]."</td></tr>";
              $out .=  "<tr><td>twoId</td><td>".$operations[$i]->twoId[0]."</td></tr>";
            }
            $out .=  "</table>";
            break;
          case '30':
            $out .=  '<div class="ubr_f">Код:30 - Неверный формат запроса</div>';
            break;
          case '95':
            $out .=  '<div class="ubr_f">Код:95 - статус данного заказа в TWPG не позволяет провести данную операцию</div>';
            break;
          case '10':
            $out .=  '<div class="ubr_f">Код:10 - ИМ не имеет доступа к этой операции</div>';
            break;    
          case '54':
            $out .=  '<div class="ubr_f">Код:54 - Недопустимая операция</div>';
            break;    
          case '96':
            $out .=  '<div class="ubr_f">Код:96 - Системная ошибка</div>';
            break;              
          default:
            $out .=  '<div class="ubr_f">Ошибка модуля</div>';
            break;
        }
		return $out;
      }
	  
	  
	 protected function xml_extract_reverse_result($xml_string) 
	  {
	    $parse_it = simplexml_load_string($xml_string);
        $status = $parse_it->Response->Status[0];
		$out = '';
        switch ($status) {
          case '00':
            $out .= 'OK';
            break;
		  case '30':
            $out .=  '<div class="ubr_f">Код:30 - Неверный формат запроса</div>';
            break;
          case '95':
            $out .=  '<div class="ubr_f">Код:95 - статус данного заказа в TWPG не позволяет провести данную операцию</div>';
            break;
          case '10':
            $out .=  '<div class="ubr_f">Код:10 - ИМ не имеет доступа к этой операции</div>';
            break;    
          case '54':
            $out .=  '<div class="ubr_f">Код:54 - Недопустимая операция</div>';
            break;    
          case '96':
            $out .=  '<div class="ubr_f">Код:96 - Системная ошибка</div>';
            break;              
          default:
            $out .=  '<div class="ubr_f">Ошибка модуля</div>';
            break;
        }
		return $out;
		}
		
		
		
	  protected function xml_extract_reconcile_result($xml) {
        $parse_it = simplexml_load_string($xml);
        $status = $parse_it->Response->Status[0];
        switch ($status) {
          case '00':
            $totals = $parse_it->Response->Totals;
            $out .= '<div class="ubr_s">Успешно</div>';
            $out .= '<p>Итоги совпали - '.$parse_it->Response->Reconcilation[0].'</p>';
            $out .= '<p>Дебит:</p><p>Количество операций: '.$totals->Debit->Count[0].'</p>';
            $out .= '<p>Общая сумма: '.number_format(((int)$totals->Debit->Amount[0])/100,2,'.','').'</p>';
            $out .= '<p>Кредит:</p><p>Количество операций: '.$totals->Credit->Count[0].'</p>';
            $out .= '<p>Общая сумма: '.number_format(((int)$totals->Credit->Amount[0])/100,2,'.','').'</p>';
            break;
		  case '30':
            $out .=  '<div class="ubr_f">Код:30 - Неверный формат запроса</div>';
            break;
          case '95':
            $out .=  '<div class="ubr_f">Код:95 - TWPG не позволяет провести данную операцию</div>';
            break;
          case '10':
            $out .=  '<div class="ubr_f">Код:10 - ИМ не имеет доступа к этой операции</div>';
            break;    
          case '54':
            $out .=  '<div class="ubr_f">Код:54 - Недопустимая операция</div>';
            break;    
          case '96':
            $out .=  '<div class="ubr_f">Код:96 - Системная ошибка</div>';
            break;              
          default:
            $out .=  '<div class="ubr_f">Ошибка модуля</div>';
            break;
        }
		return $out;
		}
		
		
		
	protected function xml_extract_journal_result($xml) {
        $parse_it = simplexml_load_string($xml);
        $status = $parse_it->Response->Status[0];
        switch ($status) {
          case '00':
            $count = $parse_it->Response->Operations->Count[0];
            $out .= '<div class="ubr_s">Операций в журнале:'.$count.'</div>';
            $orders = $parse_it->Response->Operations->Order;
             $out .= '<table class="twpgdt" style="border: 1px dashed #999;">
                <tr>
                  <td>ID</td>
                  <td>Время</td>
                  <td>Сумма</td>
                  <td>Тип Валюты</td>
                  <td>Тип</td>
                  <td>Статус</td>
                  <td>Доп. ID</td>
                  <td>Код операции</td>
                  <td>Название Операции</td>
                </tr>
              ';
            foreach ($orders as $order) {
              $out .= '<tr>
                  <td>'.$order['ID'].'</td>
                  <td>'.$this->parse_time($order->Time[0]).'</td>
                  <td>'.(($order->Amount[0])/100).'</td>
                  <td>'.$this->currency_set($order->Currency[0]).'</td>
                  <td>'.$this->type_decode($order->Type[0]).'</td>
                  <td>'.$this->status_decode($order->Status[0]).'</td>
                  <td>'.$order->twoId[0].'</td>
                  <td>'.$order->OperType[0].'</td>
                  <td>'.$this->oper_decode($order->OperName[0]).'</td>
                </tr>';
            }
            $out .= '</table>';
            break;
            $out .=  '<div class="ubr_f">Код:30 - Неверный формат запроса</div>';
            break;
          case '95':
            $out .=  '<div class="ubr_f">Код:95 - TWPG не позволяет провести данную операцию</div>';
            break;
          case '10':
            $out .=  '<div class="ubr_f">Код:10 - ИМ не имеет доступа к этой операции</div>';
            break;    
          case '54':
            $out .=  '<div class="ubr_f">Код:54 - Недопустимая операция</div>';
            break;    
          case '96':
            $out .=  '<div class="ubr_f">Код:96 - Системная ошибка</div>';
            break;              
          default:
            $out .=  '<div class="ubr_f">Ошибка модуля</div>';
            break;
        }
		return $out;
      }
	  
	  
	  
	  
	 protected function xml_extract_uni_journal($xml) {
        $parse_it = simplexml_load_string($xml);
        $count = $parse_it['count'];
        $out = '<table class="twpgdt" style="border: 1px dashed #999;"><tr>
        <td>Номер заказа</td>
        <td>Время начала обработки</td>
        <td>Код состояния платежа</td>
        <td>Расшифровка кода</td>
        <td>Код результата операции</td>
        <td>Расшифровка кода результата операции</td>
        <td>Дата последней операции по платежу</td>
        <td>Идентификатор платежа</td>
        <td>Имя держателя карты</td>
        <td>Маскированный номер карты</td>
        <td>Код подтверждения транзакции</td>
        <td>Идентификатор платежа в ПС</td>
        <td>Сумма платежа</td>
        </tr>';
        for ($i=0; $i < $count; $i++) { 
          $order=$parse_it->estore->order[$i];
          $out .= "<tr>
        <td>".$order['estore_order']."</td>
        <td>".$order['start_dt']."</td>
        <td>".$order['state_code']."</td>
        <td>".$order['state_msg']."</td>
        <td>".$order['oper_code']."</td>
        <td>".$order['oper_msg']."</td>
        <td>".$order['last_dt']."</td>
        <td>".$order['rrn']."</td>
        <td>".$order['cardholder']."</td>
        <td>".$order['pan']."</td>
        <td>".$order['app_code']."</td>
        <td>".$order['pay_trans']."</td>
        <td>".$order['pay_sum']."</td>
        </tr>";
        }
        $out .= "</table>";
		return $out;
      }
	  
	  
	 
	protected function parse_time($string) {
        //date
        $str = substr($string, 0,2);
        //month
        $str .= '/'.substr($string, 2,2);
        //year
        $str .= '/'.substr($string, 4,4);
        //hour
        $str .= ' '.substr($string, 8,2);
        //minuts
        $str .= ':'.substr($string, 10,2);
        //seconds
        $str .= ':'.substr($string, 12,2);
        return $str;
      }
	  
	  
     
    protected function currency_set($bd_value) {
        switch ($bd_value) {
          case '643':
            return 'RUB';
            break;
          case '810':
            return 'RUB';
            break;
          case '840';
            return 'USD';
            break;
          default:
            return 'unknown';
            break;
        }
      }
	  
	  
	  

	 protected function send_xml($xml) 
	   {
		$ch = curl_init("https://twpg.ubrr.ru:8443/Exec");    // initialize curl handle
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_CAINFO, dirname(__FILE__)."/certs/bank.crt");
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSLCERT, dirname(__FILE__)."/certs/user.pem");
		curl_setopt($ch, CURLOPT_SSLKEY, dirname(__FILE__)."/certs/user.key");
		curl_setopt($ch, CURLOPT_SSLKEYPASSWD, $this->sert);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_VERBOSE, 1);
		// curl_setopt($ch, CURLOPT_STDERR, $stdout);
		if( ! $answer = curl_exec($ch)) { 
			echo "<div class='ubr_f'>Ошибка соединения с банком #".curl_errno($ch)."</div>"; die;
							} 
		curl_close($ch);

		return $answer;
	  }

}