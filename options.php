<?php
use Bitrix\Main\Localization\Loc;
use	Bitrix\Main\HttpApplication;
use Bitrix\Main\Loader;
use Bitrix\Main\Config\Option;

Loc::loadMessages(__FILE__);

$request = HttpApplication::getInstance()->getContext()->getRequest();

$module_id = htmlspecialcharsbx($request["mid"] != "" ? $request["mid"] : $request["id"]);

Loader::includeModule($module_id);

// TODO manual: https://dev.1c-bitrix.ru/community/webdev/user/104863/blog/5296/

?>
<form>
    <hr>
    настройки
    <hr>
</form>
