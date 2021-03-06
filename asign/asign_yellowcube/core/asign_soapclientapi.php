<?php
/**
 * Defines SOAP Api functions
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
 * @see       Asign_SoapClientApi
 * @since     File available since Release 2.0
 */
/**
 * SOAP api functions
 * 
 * @category Asign
 * @package  Asign_Yellowcube_V_EE
 * @author   Asign <entwicklung@a-sign.ch>
 * @license  http://www.a-sign.ch/
 * @link     http://www.a-sign.ch/
 */
class Asign_SoapClientApi extends oxUBase
{
    /**
     * Filepath for storing the error-log.
     * Logs can bee seen in admin section
     * 
     * @return string
     */
    protected $sLogFilepath = null;

    /**
     * Constructor for this class
     *
     * @return \Asign_SoapClientApi
     */
    public function __construct()
    {
        $myconfig = oxRegistry::getConfig();
        $this->sLogFilepath = $myconfig->getShopConfVar("sShopDir") . "modules/asign/asign_yellowcube/logs/YClogs.log";
    }

    /**
     * Returns Operation mode for this process
     *
     * @return string
     */
    public static function getSoapOperatingMode()
    {
        return oxRegistry::getConfig()->getShopConfVar('sYellowCubeMode');
    }

    /**
     * Returns Yellowcube Depositor number
     *
     * @return string
     */
    public static function getYCDepositorNumber()
    {
        return oxRegistry::getConfig()->getShopConfVar('sYellowCubeDepositorNo');
    }

    /**
     * Returns Yellowcube Plant ID
     *
     * @return string
     */
    public static function getYCPlantId()
    {
        return oxRegistry::getConfig()->getShopConfVar('sYellowCubePlant');
    }

    /**
     * Returns Yellowcube Shipping Service. e.g. ECO, PRI, etc.
     *
     * @return string
     */
    public static function getYCShippingService()
    {
        return oxRegistry::getConfig()->getShopConfVar('sYellowCubeShipping');
    }
    
    /**
     * Returns Partner number
     *
     * @return string
     */
    public static function getYCPartnerNumber()
    {
        return oxRegistry::getConfig()->getShopConfVar('sYellowCubePartnerNo');
    }

    /**
     * Returns Partner Type
     *
     * @return string
     */
    public static function getYCPartnerType()
    {
        return oxRegistry::getConfig()->getShopConfVar('sYellowCubePType');
    }

    /**
     * Returns Quantity ISO value
     *
     * @return string
     */
    public static function getYCQuantityISO()
    {
        return oxRegistry::getConfig()->getShopConfVar('sYellowCubeQuantityISO');
    }

    /**
     * Returns default Picking Message
     *
     * @return string
     */
    public static function getYCPickingMessage()
    {
        return oxRegistry::getConfig()->getShopConfVar('sYellowCubePMessage');
    }

    /**
     * Returns default return reason message
     *
     * @return string
     */
    public static function getYCReturnReason()
    {
        return oxRegistry::getConfig()->getShopConfVar('sYellowCubeRReason');
    }

    /**
     * Returns default EAN Type
     *
     * @return string
     */
    public static function getYCEANType()
    {
        return oxRegistry::getConfig()->getShopConfVar('sYellowCubeEANType');
    }

    /**
     * Returns alternate ISO unit
     *
     * @return string
     */
    public static function getYCAlternateUnitISO()
    {
        return oxRegistry::getConfig()->getShopConfVar('sYellowCubeAlternateUnitISO');
    }

    /**
     * Returns default Net Weight ISO unit
     *
     * @return string
     */
    public static function getYCNetWeightISO()
    {
        return oxRegistry::getConfig()->getShopConfVar('sYellowCubeNetWeightISO');
    }

    /**
     * Returns default Gross Weight ISO unit
     *
     * @return string
     */
    public static function getYCGWeightISO()
    {
        return oxRegistry::getConfig()->getShopConfVar('sYellowCubeGrossWeightISO');
    }

    /**
     * Returns default Length ISO unit
     *
     * @return string
     */
    public static function getYCLengthISO()
    {
        return oxRegistry::getConfig()->getShopConfVar('sYellowCubeLengthISO');
    }

    /**
     * Returns default Width ISO unit
     *
     * @return string
     */
    public static function getYCWidthISO()
    {
        return oxRegistry::getConfig()->getShopConfVar('sYellowCubeWidthISO');
    }

    /**
     * Returns default Height ISO unit
     *
     * @return string
     */
    public static function getYCHeightISO()
    {
        return oxRegistry::getConfig()->getShopConfVar('sYellowCubeHeightISO');
    }

    /**
     * Returns default Volume ISO unit
     *
     * @return string
     */
    public static function getYCVolumeISO()
    {
        return oxRegistry::getConfig()->getShopConfVar('sYellowCubeVolumeISO');
    }

    /**
     * Returns order document type
     *
     * @return string
     */
    public static function getYCDocType()
    {
        return oxRegistry::getConfig()->getShopConfVar('sYellowCubeDocType');
    }

    /**
     * Returns order document MIME type
     *
     * @return string
     */
    public static function getYCDocMimeType()
    {
        return oxRegistry::getConfig()->getShopConfVar('sYellowCubeDocMimeType');
    }

    /**
     * Returns Order Documents Flag
     *
     * @return string
     */
    public static function getYCOrderDocumentsFlag()
    {
        return oxRegistry::getConfig()->getShopConfVar('sYellowCubeOrderDocumentsFlag');
    }

    /**
     * Returns whether to allow phone,fax,mobile information or not
     *
     * @return bool
     */
    public static function usePhoneAndFaxDetails()
    {
        return oxRegistry::getConfig()->getShopConfVar('blYellowCubeUsePhoneAndFax');
    }

    /**
     * Returns whether to Use certificate for LIVE or ALL modes
     *
     * @return bool
     */
    public static function useCertificateForAllModes()
    {
        return oxRegistry::getConfig()->getShopConfVar('blYellowCubeCertForAll');
    }

    /**
     * Returns whether to ignore LOT information selected
     *
     * @return bool
     */
    public static function isIgnoreLotInforSelected()
    {
        return oxRegistry::getConfig()->getShopConfVar('blYellowCubeIgnoreLotInfo');
    }

    /**
     * Returns whether to delete temporary file or not
     *
     * @return bool
     */
    public static function isYCDeleteOrderFile()
    {
        return oxRegistry::getConfig()->getShopConfVar('blYellowCubeDocDelete');
    }

    /**
     * Returns maximum wait time
     *
     * @return string
     */
    public static function getTransMaxTime()
    {
        return oxRegistry::getConfig()->getShopConfVar('sYellowCubeTransMaxTime');
    }

    /**
     * Returns Certificate Filename
     *
     * @return string
     */
    public static function getCertFilename()
    {
        return oxRegistry::getConfig()->getShopConfVar('sYellowCubeCertFile');
    }

    /**
     * Returns WSDL URI for the file
     *
     * @return string
     */
    public static function getSoapWsdlUri()
    {
        return oxRegistry::getConfig()->getShopConfVar('sYellowCubeWsdlUrl');
    }

    /**
     * Returns Sender Identity detail
     *
     * @return string
     */
    public static function getSoapWsdlSender()
    {
        return oxRegistry::getConfig()->getShopConfVar('sYellowCubeSender');
    }

    /**
     * Returns YellowCube Receiver info
     *
     * @return string
     */
    public static function getSoapWsdlReceiver()
    {
        return oxRegistry::getConfig()->getShopConfVar('sYellowCubeReceiver');
    }
    
    /**
     * Returns developer email address
     *
     * @return string
     */
    public static function getDeveloperEmail()
    {
        return oxRegistry::getConfig()->getShopConfVar('sYellowCubeNotifyEmail');
    }

    /**
     * Returns SOAP version defined
     *
     * @return string
     */
    public static function getSoapVersion()
    {        
        return "1.0";
    }
    
    /**
     * Returns Communication Type
     * Options: SOAP|REST|HTTPS|FTP
     *
     * @return string
     */
    public static function getCommType()
    {       
        return "SOAP";
    }

    /**
     * Initiates the Soap Client API
     *
     * @return object
     */
    protected function initSoap()
    {
        // api configs
        try{
            $wsdl               = self::getSoapWsdlUri();
            $sCertFilename      = self::getCertFilename();            
            $certPath           = oxRegistry::getConfig()->getConfigParam('sShopDir') . 'cert/' . $sCertFilename;

            // set SOAP parameters
            $aParams  = array(
                'soap_version'  => SOAP_1_1,
                'trace'         => true,                
                'exception'     => true,
                'features'      => SOAP_SINGLE_ELEMENT_ARRAYS,
            );

            // if only live then
            if (self::useCertificateForAllModes()) {
                $aParams["local_cert"]   = $certPath;
            }
            $sClient  = new asign_soapclient($wsdl, $aParams);
            
            return $sClient;
        } catch(SoapFault $sEx) {
            error_log("[ ".date("Y-m-d H:i:s")." ][Error: InitSoap] ". $sEx->getMessage() ." \n", 3, $this->sLogFilepath);
            oxRegistry::get("oxUtilsView")->addErrorToDisplay(new oxException('[Error: InitSoap] ' . $sEx->getMessage()));
        }
    }

    /**
     * Calls the function passed. Along with the params.
     * Performs SOAP call using passed function. Function name
     * varies for every WSDL.
     *
     * @param string $fnc    Function to be called
     * @param object $params object of params to be passed
     *
     * @return null
     */
    public function callFunction($fnc, $params = null)
    {        
        try{
            // soap call the function
            $oClient = $this->initSoap();
            $sResponse = $oClient->$fnc($params);

            // DEBUG: only for checking XML output
            $devMail = self::getDeveloperEmail();
            if ($devMail != "") {
                @mail($devMail, "SOAP_REQUEST", print_r($oClient->__getLastRequest(), 1)); // SPS request
                @mail($devMail, "SOAP_RESPONSE", print_r($sResponse, 1)); // SPS response
            }            
            // END-DEBUG
            
            return $sResponse;
        } catch(SoapFault $sEx) {
            error_log("[ ".date("Y-m-d H:i:s")." ][Error: callFunction] [".$fnc."] ". utf8_decode($sEx->getMessage()) ." \n", 3, $this->sLogFilepath);
            oxRegistry::get("oxUtilsView")->addErrorToDisplay(new oxException('[Error: callFunction] ['.$fnc.'] ' . $sEx->getMessage()));
        }
    }    
}
