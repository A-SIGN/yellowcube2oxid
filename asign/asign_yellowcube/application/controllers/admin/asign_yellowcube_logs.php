<?php
/**
* Renders template file asign_yellowcube_logs.tpl
*
* PHP version 5
* 
* @category  Asign
* @package   Asign_Yellowcube_V_EE
* @author    Asign <entwicklung@a-sign.ch>
* @copyright asign
* @license   http://www.a-sign.ch/
* @version   2.0
* @link      http://www.a-sign.ch/
* @see       Asign_YellowCube_Logs
* @since     File available since Release 2.0
*/

/**
* Renders Template file
* 
* @category Asign
* @package  Asign_Yellowcube_V_EE
* @author   Asign <entwicklung@a-sign.ch>
* @license  http://www.a-sign.ch/
* @link     http://www.a-sign.ch
*/
class Asign_YellowCube_Logs extends oxAdminView
{
    /**
     * Executes parent method parent::render(), show the details
     * for selected object and passed to Smarty engine and returns name of template file
     * "asign_yellowcube_logs.tpl".
     *
     * @return string
     */
    public function render()
    {
        $myconfig  = oxNew("oxconfig");
        $sFilepath = $myconfig->getShopConfVar("sShopDir") . "modules/asign/asign_yellowcube/logs/YClogs.log";
        
        parent::render();                
        
        //get the errorlog in array using explode and reverse to get latest first
        $errorLog = array_reverse(explode("\n", file_get_contents($sFilepath)));
       
        // show error message if folder permission missing        
        $this->_aViewData["_isReadable"] = (!is_writable($sFilepath) || !is_readable($sFilepath)) ? false : true;
       
        //limit the error-log count to latest 1000
        $errLogs = array();
        if (count($errorLog) > 0) {
            for ($i=0;$i<1000;$i++) {
                if (!empty($errorLog[$i])) {
                    $errLogs[] = $errorLog[$i].'<br />';
                }
            }
        }
        
        $this->_aViewData["yclogs"] = $errLogs;        
        return 'asign_yellowcube_logs.tpl';
    }
}
