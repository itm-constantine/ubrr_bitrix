# ubrr_bitrix
#############################################
#                Инструкция по установке               #
#############################################



1) Распаковать содержимое архива в корень_сайта/bitrix/modules/

2) Скопировать сгенерированные файлы личного сертификата и приватного ключа в папку корень_сайта/bitrix/modules/ubrir.payment/install/sale_payment/ubrir/sdk/certs c именами user.pem и user.key соответственно

3) Активировать модуль в админ.панели (Рабочий стол->Marketplace->Установленные решения)

4) Настроить модуль используя персональные данные на странице настроек

 (Рабочий стол->Магазин->Настройки->Платежные системы)

5) Прописать сформированный на странице настроек URL в настройках ПЦ Uniteller корень_сайта/ubrir/uniteller.php





#############################################
#              Инструкция по обновлению сетификта      #
#############################################


Скопировать новый сертификат в папку корень_сайта/bitrix/php_interface/include/install/sale_payment/ubrir/sdk/certs c именем ubrir.crt





#############################################
#              Инструкция по удалению      #
#############################################



Удалить модуль в админ.панели (Рабочий стол->Marketplace->Установленные решения)