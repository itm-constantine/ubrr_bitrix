﻿<?php
    if(!empty($_POST['task_ubrir']))
	switch ($_POST['task_ubrir']) {
				case '1':
					if(!empty($_POST['shoporderidforstatus']) AND !empty($_POST["VALUE2_ID_1"])  AND !empty($_POST["VALUE2_SERT_1"])) {
						$order_id = $_POST['shoporderidforstatus']*1;
						$arOrder = CSaleOrder::GetByID($order_id);
						if(!empty($arOrder['PS_STATUS_MESSAGE'])) {
							$bankHandler = new Ubrir(array(																									// для статуса
								'shopId' => $_POST["VALUE2_ID_1"],
								'order_id' => $order_id, 
								'sert' => $_POST["VALUE2_SERT_1"],
								'twpg_order_id' => $arOrder['PS_STATUS_DESCRIPTION'], 
								'twpg_session_id' => $arOrder['PS_STATUS_MESSAGE']
								));
							$out = '<div class="ubr_s">Статус заказа - '.$bankHandler->check_status().'</div>';	
						}
						else $out = '<div class="ubr_f">Получить статус данного заказа невозможно. Либо его не существует, либо он был оплачен через Uniteller</div>';	
					}
					if(empty($_POST['shoporderidforstatus'])) $out = "<div class='ubr_f'>Вы не ввели номер заказа</div>";
					break;
					
				case '2':
					if(!empty($_POST['shoporderidforstatus']) AND !empty($_POST["VALUE2_ID_1"])  AND !empty($_POST["VALUE2_SERT_1"])) {
						$order_id = $_POST['shoporderidforstatus']*1;
						$arOrder = CSaleOrder::GetByID($order_id);
						if(!empty($arOrder['PS_STATUS_MESSAGE'])) {
							$bankHandler = new Ubrir(array(																												   // для детализации
								'shopId' => $_POST["VALUE2_ID_1"],
								'order_id' => $order_id, 
								'sert' => $_POST["VALUE2_SERT_1"],
								'twpg_order_id' => $arOrder['PS_STATUS_DESCRIPTION'], 
								'twpg_session_id' => $arOrder['PS_STATUS_MESSAGE']
								));
							$out = $bankHandler->detailed_status();	
						}
						else $out = '<div class="ubr_f">Получить детализацию данного заказа невозможно. Либо его не существует, либо он был оплачен через Uniteller</div>';	
					}
					if(empty($_POST['shoporderidforstatus'])) $out = "<div class='ubr_f'>Вы не ввели номер заказа</div>";
					break;
					
				case '3':
					if(!empty($_POST['shoporderidforstatus']) AND !empty($_POST["VALUE2_ID_1"]) AND !empty($_POST["VALUE2_SERT_1"])) {
						$order_id = $_POST['shoporderidforstatus']*1;
						$arOrder = CSaleOrder::GetByID($order_id);
						if($arOrder['PAYED'] == 'Y') {
							if(!empty($arOrder['PS_STATUS_MESSAGE'])) {
								$bankHandler = new Ubrir(array(																												  // для реверса
									'shopId' => $_POST["VALUE2_ID_1"],
									'order_id' => $order_id, 
									'sert' => $_POST["VALUE2_SERT_1"],
									'twpg_order_id' => $arOrder['PS_STATUS_DESCRIPTION'], 
									'twpg_session_id' => $arOrder['PS_STATUS_MESSAGE']
								));
								$res = $bankHandler->reverse_order();	
								if($res == 'OK') {
									$out = '<div class="ubr_s">Оплата успешно отменена</div>';
									CSaleOrder::Update($order_id, array("PAYED" => "N"));   									
									CSaleOrder::StatusOrder($order_id, "N");
								}
								else $out = $res;
							}
						else $out = '<div class="ubr_f">Получить реверс данного заказа невозможно. Он был оплачен через Uniteller</div>';
						}
						else $out = '<div class="ubr_f">Получить реверс данного заказа невозможно, он не был оплачен, либо его не существует</div>';
					}
					if(empty($_POST['shoporderidforstatus'])) $out = "<div class='ubr_f'>Вы не ввели номер заказа</div>";
					break;

				case '4':
					if(!empty($_POST["VALUE2_ID_1"])  AND !empty($_POST["VALUE2_SERT_1"])) {					
							$bankHandler = new Ubrir(array(																												 // // для сверки итогов
								'shopId' => $_POST["VALUE2_ID_1"],
								'sert' => $_POST["VALUE2_SERT_1"],
								));
							$out = $bankHandler->reconcile();
					}                                                                                          
					break;		
					
				case '5':
					if(!empty($_POST["VALUE2_ID_1"])  AND !empty($_POST["VALUE2_SERT_1"])) {					
							$bankHandler = new Ubrir(array(																												 // // для журнала операции
								'shopId' => $_POST["VALUE2_ID_1"],
								'sert' => $_POST["VALUE2_SERT_1"],
								));
							$out = $bankHandler->extract_journal();
					}      
					break;	

				case '6':
					if(!empty($_POST["VALUE2_UNI_LOGIN_1"])  AND !empty($_POST["VALUE2_UNI_EMP_1"])) {					
							$bankHandler = new Ubrir(array(																												 // // для журнала Uniteller
								'uni_login' => $_POST["VALUE2_UNI_LOGIN_1"],
								'uni_pass' => $_POST["VALUE2_UNI_EMP_1"],
								));
							$out = $bankHandler->uni_journal();
					}  
					else $out = '<div class="ubr_f">Необходимо ввести логин и пароль ЛК для MasterCard</div>';		
					break;	
				case '7':
					if(!empty($_POST['mailsubject'])  AND !empty($_POST['maildesc']) AND !empty($_POST['mailem'])) {					
							$to = 'ibank@ubrr.ru';
							 $subject = htmlspecialchars($_GET['mailsubject'], ENT_QUOTES);
							 $message = 'Отправитель: '.htmlspecialchars($_GET['mailem'], ENT_QUOTES).' | '.htmlspecialchars($_GET['maildesc'], ENT_QUOTES);
							 $headers = 'From: '.$_SERVER["HTTP_HOST"];
							 mail($to, $subject, $message, $headers);
							 echo 'Отправлено успешно';
					}
					else echo 'Заполнены не все поля';
					break;	
					
				default:
					break;
			}
			
$toprint = '
<div id="callback" style="display: none;">
 <table>
 <tr>
 <h2 onclick="show(this);" style="text-align: center; cursor:pointer;">Обратная связь<span style="margin-left: 20px; font-size: 80%; color: grey;" onclick="jQuery(\'#callback\').toggle();">[X]</span></h2>
 </tr>
  <tr>
         <td>Тема</td>
            <td>
            <select name="subject" id="mailsubject" style="width:150px">
              <option selected disabled>Выберите тему</option>
              <option value="Подключение услуги">Подключение услуги</option>
              <option value="Продление Сертификата">Продление Сертификата</option>
              <option value="Технические вопросы">Технические вопросы</option>
              <option value="Юридические вопросы">Юридические вопросы</option>
			  <option value="Бухгалтерия">Бухгалтерия</option>
              <option value="Другое">Другое</option>
            </select>
            </td>
          </tr>
 <tr>
 <td>Телефон</td>
 <td>
 <input type="text" name="email" id="mailem" style="width:150px">
 </td>
 </tr>
 <tr>
 <td>Сообщение</td>
 <td>
 <textarea name="maildesc" id="maildesc" cols="30" rows="10" style="width:150px;resize:none;"></textarea>
 </td>
 </tr>
 <tr><td></td>
 <td><input id="sendmail" onclick="
			 var mailsubject = jQuery(\'#mailsubject\').val();
			 var maildesc = jQuery(\'#maildesc\').val();
			 var mailem = jQuery(\'#mailem\').val();
			 console.log(mailsubject);
			 console.log(maildesc);
			 console.log(mailem);
			 if(!mailem & !!maildesc) {
			 jQuery(\'#mailresponse\').html(\'<br>Необходимо указать телефон\');
			 return false;
			 }
			 if(!maildesc & !!mailem) {
			 jQuery(\'#mailresponse\').html(\'<br>Сообщение не может быть пустым\');
			 return false;
			 }
			 if(!!mailem & !!maildesc) 
			 jQuery.ajax({
			 type: \'POST\',
			 url: location.href,
			 data: {mailsubject:mailsubject, maildesc:maildesc, mailem:mailem, task_ubrir:7},
			 success: function(response){
			 jQuery(\'#mailresponse\').html(\'Письмо отправлено на почтовый сервер\');
			 jQuery(\'#maildesc\').val(null);
			 jQuery(\'#mailsubject\').val(null);
			 jQuery(\'#mailem\').val(null);
			 }
			 });
			 else jQuery(\'#mailresponse\').html(\'<br>Заполнены не все поля\');
			 return false;
			 " type="button" name="sendmail" value="Отправить">
			 </tr>
			 <tr>
			 <td>
			 </td>
			 <td style="padding: 0" id="mailresponse">
			 </td>
			 </tr>
			 <tr>
			 <td></td>
			<td>8 (800) 1000-200</td></tr>
 </table>
 </div>
 <div style="width: 100%; margin-top: 10px;">'.$out.'</div>
<div style="margin: 20px 0 20px 0; text-align: center; padding: 20px; width: 415px; border: 1px dashed #999;"> 
<h3 style="text-align: center; padding: 0 0 20px 0; margin: 0;">Получить детальную информацию:</h3>
<div style="margin: 0 auto; text-align: center; padding: 5px; width: 200px; border: 1px dashed #999;"><form action="" method="post">Номер заказа: <br>
<input style="margin: 5px;" type="text" name="shoporderidforstatus" id="shoporderidforstatus" value="'.$order_id.'" placeholder="№ заказа" size="8">
<input style="margin: 5px;" type="hidden" name="task_ubrir" id="task_ubrir" value="">
      <input class="twpginput" type="button" onclick="$(\'#task_ubrir\').val(1); submit();" id="statusbutton" value="Запросить статус заказа">
      <input class="twpginput" type="button" onclick="$(\'#task_ubrir\').val(2); submit();" id="detailstatusbutton" value="Информация о заказе">
      <input class="twpginput" type="button" onclick="$(\'#task_ubrir\').val(3); submit();" id="reversbutton" value="Отмена заказа"><br>
 </div>  
      <input class="twpgbutton" type="button" onclick="$(\'#task_ubrir\').val(4); submit();" id="recresultbutton" value="Сверка итогов">
      <input class="twpgbutton" type="button" onclick="$(\'#task_ubrir\').val(5); submit();" id="journalbutton" value="Журнал операций Visa">
	  <input class="twpgbutton" type="button" onclick="$(\'#task_ubrir\').val(6); submit();" id="unijournalbutton" value="Журнал операций MasterCard">
	  <input class="twpgbutton" type="button" onclick="jQuery(\'#callback\').toggle();" id="mailbutton" value="Написать в банк">
	  </form>
</div>
';			

?>
