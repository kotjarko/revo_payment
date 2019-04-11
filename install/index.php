<?
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class revo_payment extends CModule
{

    var $MODULE_ID = 'revo.payment';
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $PARTNER_NAME;
    var $PARTNER_URI;

    protected $langMess;

    function __construct() {

        $path = realpath(dirname(dirname(__FILE__)));

        require($path . "/config.php");

        $arModuleVersion = array();
        include __DIR__ . '/version.php';

        $this->MODULE_NAME = GetMessage('REVO0_MODULE_NAME') . " - " . REVO0_BANK_NAME;
        $this->MODULE_DESCRIPTION = GetMessage('REVO0_MODULE_DESCRIPTION');
        $this->PARTNER_NAME = GetMessage('REVO0_PARTNER_NAME');
        $this->PARTNER_URI = GetMessage('REVO0_PARTNER_URI');

        if (array_key_exists("VERSION", $arModuleVersion))
        {
            $this->MODULE_VERSION = $arModuleVersion["VERSION"];
            $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        }
    }


    function DoInstall()
    {
        $this->InstallFiles();
        RegisterModule($this->MODULE_ID);
        COption::SetOptionInt($this->MODULE_ID, "delete", false);

        RegisterModuleDependences("sale", "OnSaleOrderEntitySaved", $this->MODULE_ID, "RevoPaymentEventHandlers", "OnSaleOrderEntitySavedHandler");
        RegisterModuleDependences("sale", "OnPrintableCheckSend", $this->MODULE_ID, "RevoPaymentEventHandlers", "OnPrintableCheckSendHandler");
    }


    function InstallFiles($arParams = array())
    {

        $path = realpath(dirname(dirname(__FILE__))) . "/install/sale_payment/payment/";
        $files = new DirectoryIterator($path);

        foreach ($files as $file) {
            // excluding the . and ..
            if ($file->isDot() === false) {
                // seek and replace ;)
                $path_to_file = $file->getPathname();
                $file_contents = file_get_contents($path_to_file);
                $file_contents = str_replace("{module_path}", $this->MODULE_ID, $file_contents);
                file_put_contents($path_to_file, $file_contents);
            }
        }

        CopyDirFiles(
            realpath(dirname(dirname(__FILE__))) . "/install/sale_payment/payment/",
            $_SERVER["DOCUMENT_ROOT"] . "/bitrix/php_interface/include/sale_payment/revo_payment/",
            true, true
        );

        CopyDirFiles(
            realpath(dirname(dirname(__FILE__))) . "/install/sale/payment/",
            $_SERVER["DOCUMENT_ROOT"] . "/sale/revo_payment/"
        );

        CopyDirFiles(
            realpath(dirname(dirname(__FILE__))) . "/ajax.php",
            $_SERVER['DOCUMENT_ROOT'] . "/" . $this->MODULE_ID . "/ajax.php"
        );
    }


    function DoUninstall()
    {
        COption::SetOptionInt($this->MODULE_ID, "delete", true);
        DeleteDirFilesEx("/bitrix/php_interface/include/sale_payment/revo_payment");
        DeleteDirFilesEx("/sale/revo_payment/");
        DeleteDirFilesEx($this->MODULE_ID);

        UnRegisterModuleDependences("sale", "OnSaleOrderEntitySaved", $this->MODULE_ID, "RevoPaymentEventHandlers", "OnSaleOrderEntitySavedHandler");
        UnRegisterModuleDependences("sale", "OnPrintableCheckSend", $this->MODULE_ID, "RevoPaymentEventHandlers", "OnPrintableCheckSendHandler");

        UnRegisterModule($this->MODULE_ID);
        return true;
    }
}