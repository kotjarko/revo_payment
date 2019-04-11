<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?><?
include(GetLangFileName(dirname(__FILE__)."/", "/edit.php"));

$psTitle =  GetMessage("REVO0_EDIT_TITLE");
$psDescription = GetMessage("REVO0_EDIT_DESCR");

$arPSCorrespondence = array(
    "USE_TEST" => array(
        "NAME" => GetMessage("REVO0_USE_TEST"),
        "DESCR" => GetMessage("REVO0_USE_TEST_DESCR"),
        'SORT' => 200,
        'INPUT' => array(
            'TYPE' => 'Y/N'
        ),
        'DEFAULT' => array(
            "PROVIDER_VALUE" => "N",
            "PROVIDER_KEY" => "INPUT"
        )
    ),

    "STORE_ID" => array(
        "NAME" => GetMessage("REVO0_STORE_ID"),
        "TYPE" => "VALUE",
        'SORT' => 210,
    ),
    "SECRET_KEY" => array(
        "NAME" => GetMessage("REVO0_SECRET_KEY"),
        "TYPE" => "VALUE",
        'SORT' => 220,
    ),

    "TEST_STORE_ID" => array(
        "NAME" => GetMessage("REVO0_TEST_STORE_ID"),
        "TYPE" => "VALUE",
        'SORT' => 230,
    ),
    "TEST_SECRET_KEY" => array(
        "NAME" => GetMessage("REVO0_TEST_SECRET_KEY"),
        "TYPE" => "VALUE",
        'SORT' => 240,
    ),
);
