<?php
define('STOP_STATISTICS', true);
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');
$GLOBALS['APPLICATION']->RestartBuffer();

if (!CModule::IncludeModule('sale')) return;

$inputData = file_get_contents('php://input');
// save original data to validate sign
$originalInputData = $inputData;

$arOrder = CSaleOrder::GetByID("145");

// init paysystem, need for use of props
$paySystem = new CSalePaySystemAction();
$paySystem->InitParamArrays($arOrder, $arOrder["ID"]);

echo "<pre>";
var_dump($paySystem);
echo "</pre>";