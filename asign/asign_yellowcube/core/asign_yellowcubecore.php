<?php
/**
 * Defines yellowcube core functions
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
 * @see       Asign_YellowCubeCore
 * @since     File available since Release 2.0
 */
/**
 * Yellowcube core functions
 * 
 * @category Asign
 * @package  Asign_Yellowcube_V_EE
 * @author   Asign <entwicklung@a-sign.ch>
 * @license  http://www.a-sign.ch/
 * @link     http://www.a-sign.ch/
 */
class Asign_YellowCubeCore extends oxUBase
{
    /**
     * List of the countries to be verified
     * for zip address.
     * Key = countryiso, value = digits
     *
     * @return array
     */
    protected $aCountryVsZip= array('CH' => 4, 'DE' => 5);
    
    /**
     * DE saluations
     * @return array
     */
    protected $aSaluationsDE= array('MR' => 'Herr',     'MRS' => 'Frau',    'COMPANY' => 'Firma');

    /**
     * EN saluations
     * @return array
     */
    protected $aSaluationsEN= array('MR' => 'Mr.',      'MRS' => 'Ms.',     'COMPANY' => 'Company');

    /**
     * IT saluations
     * @return array
     */
    protected $aSaluationsIT= array('MR' => 'Signore',  'MRS' => 'Signora', 'COMPANY' => 'Ditta');

    /**
     * FR saluations
     * @return array
     */
    protected $aSaluationsFR= array('MR' => 'Monsieur', 'MRS' => 'Madame',  'COMPANY' => 'Société');            

    /**
     * Filename for storing the error-log.
     * Logs can bee seen in admin section
     * 
     * @return string
     */
    protected $sLogFilepath = null;

    /**
     * Constructor for this class
     *
     * @return \Asign_YellowCubeCore
     */
    public function __construct()
    {
        $myconfig = oxRegistry::getConfig();
        $this->sLogFilepath = $myconfig->getShopConfVar("sShopDir") . "modules/asign/asign_yellowcube/logs/YClogs.log";
    }
   
    /**
     * Returns timestamp value with date and time
     * Default Format: YmdHis
     *
     * @param string $sFormat Format of Timestamp
     * 
     * @return string
     */
    protected function generateTimestampValue($sFormat = 'YmdHis')
    {
        return date($sFormat);
    }

    /**
     * Returns initial parameters used for request
     * Includes: Type, Sender, Receiver,Timestamp,
     *              OperatingMode, Version, CommType
     *
     * @param string $sType Type of request sent
     * E.g. WAB, ART, BAR, WAR
     * 
     * @return array
     */
    public function getInitialParams($sType)
    {        
        $aParams = array(
                    'Type'             => $sType,
                    'Sender'        => Asign_SoapClientApi::getSoapWsdlSender(),
                    'Receiver'        => Asign_SoapClientApi::getSoapWsdlReceiver(),
                    'Timestamp'        => (float)$this->generateTimestampValue(),//20141017000020,
                    'OperatingMode'    => Asign_SoapClientApi::getSoapOperatingMode(),
                    'Version'        => Asign_SoapClientApi::getSoapVersion(),
                    'CommType'        => Asign_SoapClientApi::getCommType()
                );
        
        return $aParams;
    }

    /**
     * Creates New customer Order in Yellowcube
     *
     * @param Object $oObject Active object
     *
     * @return array
     */
    public function createYCCustomerOrder($oObject)
    {
        try{
            // If there are no order positions the order should not be sent to YC
            if(count($oObject->Order->OrderPositions) > 0){
                $oSoap = oxNew('asign_soapclientapi');
                $aResponse = $oSoap->callFunction('CreateYCCustomerOrder', $oObject);
            } else {
                $aResponse = null;
            }

            return $aResponse;
        } catch(Exception $soapex) {
            error_log("[ ".date("Y-m-d H:i:s")." ][YellowCube] ". $soapex->getMessage() ." \n", 3, $this->sLogFilepath);
            oxRegistry::get("oxUtilsView")->addErrorToDisplay(new oxException($soapex->getMessage()));
        }
    }
    
    /**
     * Inserts Article Master data to Yellowcube
     *
     * @param Object $oRequestData Request Data
     * 
     * @return array
     */
    public function insertArticleMasterData($oRequestData)
    {        
        try{            
            $oSoap = oxNew('asign_soapclientapi');        
            $aResponse = $oSoap->callFunction('InsertArticleMasterData', $oRequestData);
            
            return $aResponse;        
        } catch(Exception $soapex) {
            error_log("[ ".date("Y-m-d H:i:s")." ] ". $soapex->getMessage() ." \n", 3, $this->sLogFilepath);
            oxRegistry::get("oxUtilsView")->addErrorToDisplay(new oxException(utf8_decode($soapex->getMessage())));
        }
    }
    
    /**
     * Returns status for both order/article from Yellowcube
     *
     * @param string $soxId Selected object ID
     * @param string $sType Defines if its WAB or ART
     * 
     * @return array
     */
    public function getYCGeneralDataStatus($soxId, $sType)
    {
        // define params
        $aParams         = $this->getInitialParams($sType);
        $aFunc["ART"]    = "GetInsertArticleMasterDataStatus";
        $aFunc["WAB"]    = "GetYCCustomerOrderStatus";
        $aFunc["WAR"]    = "GetYCCustomerOrderReply";
        
        $oObject = new stdClass();
        $oObject->ControlReference = new stdClass();        
        foreach ($aParams as $key => $param) {
            $oObject->ControlReference->$key = $param;
        }

        // get Reference number for the YC status
        $oModel = oxNew("asign_yellowcube_model");
        if ($sType == "WAR") {
            // add Max Wait Time...
            $oObject->ControlReference->TransMaxWait = Asign_SoapClientApi::getTransMaxTime();
            $oObject->CustomerOrderNo = $oModel->getYCReferenceNumber($soxId, $sType);
        } elseif ($sType == "ART" || $sType == "WAB") {
            $oObject->Reference = $oModel->getYCReferenceNumber($soxId, $sType);
        }

        // ping and get response...
        try{
            $oSoap = oxNew('asign_soapclientapi');        
            $aResponse = $oSoap->callFunction($aFunc[$sType], $oObject);
                        
            return $aResponse;
        } catch(Exception $soapex) {
            error_log("[ ".date("Y-m-d H:i:s")." ] ". $soapex->getMessage() ." \n", 3, $this->sLogFilepath);
            oxRegistry::get("oxUtilsView")->addErrorToDisplay(new oxException(utf8_decode($soapex->getMessage())));
        } 
    }

    /**
     * Returns inventory list from Yellowcube
     *
     * @internal param Object $oObject Active object
     *
     * @return array
     */
    public function getInventory()
    {
        $aParams = $this->getInitialParams("BAR");
        
        $oObject = new stdClass();
        $oObject->ControlReference = new stdClass();        
        foreach ($aParams as $key => $param) {
            $oObject->ControlReference->$key = $param;
        }
        
        try{
            $oSoap = oxNew('asign_soapclientapi');
            $aResponse = $oSoap->callFunction("GetInventory", $oObject);

            return $aResponse;
        } catch(Exception $soapex) {
            error_log("[ ".date("Y-m-d H:i:s")." ] ". $soapex->getMessage() ." \n", 3, $this->sLogFilepath);
            oxRegistry::get("oxUtilsView")->addErrorToDisplay(new oxException(utf8_decode($soapex->getMessage())));
        }
    }

    /**
     * Returns the status of the created order (WAB)
     *
     * @param string $sCountry - Country Id found
     *
     * @return string
     */
    public function getCountryISO($sCountry)
    {
        $sIsoAlpha = oxDb::getDb()->getOne("select `oxisoalpha2` from `oxcountry` where `oxid` = '" . $sCountry . "'");
        return $sIsoAlpha;
    }

    /**
     * Returns clean phone, mobileno and fax
     *
     * @param string $sPhone - unformed string
     *
     * @return string
     */
    protected function returnCleanValues($sPhone)
    {
        $sPhone = str_replace("-", "", $sPhone);
        return $sPhone;
    }

    /**
     * Returns array of SPS details
     *
     * @param string $oValue - Article object id
     *
     * @return array
     */
    protected function getSpsParams($oValue)
    {
        $oModel  = oxNew("asign_yellowcube_model");
        $aParams = $oModel->getSpSDetailsForThisArticle($oValue);

        return $aParams;
    }


    /**
     * Returns a User object with the correct shipping address for the order.
     * If there is a custom delivery address it will return that, if not it will return the standard order user
     *
     * @param oxOrder $oOrder
     * @return oxUser
     */
    protected function _getShippingAddressUser(oxOrder $oOrder){
        if($oOrder->oxorder__oxdelsal->value !== ''){
            $oUser = oxNew('oxuser');
            $oUser->load($oOrder->oxorder__oxuserid->value);
            $oUser->oxuser__oxcompany = clone $oOrder->oxorder__oxdelcompany;
            $oUser->oxuser__oxfname = clone $oOrder->oxorder__oxdelfname;
            $oUser->oxuser__oxlname = clone $oOrder->oxorder__oxdellname;
            $oUser->oxuser__oxstreet = clone $oOrder->oxorder__oxdelstreet;
            $oUser->oxuser__oxstreetnr = clone $oOrder->oxorder__oxdelstreetnr;
            $oUser->oxuser__oxaddinfo = clone $oOrder->oxorder__oxdeladdinfo;
            $oUser->oxuser__oxcity = clone $oOrder->oxorder__oxdelcity;
            $oUser->oxuser__oxcountryid = clone $oOrder->oxorder__oxdelcountryid;
            $oUser->oxuser__oxstateid = clone $oOrder->oxorder__oxdelstateid;
            $oUser->oxuser__oxzip = clone $oOrder->oxorder__oxdelzip;
            $oUser->oxuser__oxfon = clone $oOrder->oxorder__oxdelfon;
            $oUser->oxuser__oxfax = clone $oOrder->oxorder__oxdelfax;
            $oUser->oxuser__oxsal = clone $oOrder->oxorder__oxdelsal;
        } else {
            $oUser = $oOrder->getOrderUser();
        }

        return $oUser;
    }

    /**
     * Returns the order details in object form
     *
     * @param object $oOrder Order object
     * @param array  $aData  Array order params
     * @param bool   $isReturn  If this is return?
     *
     * @return object
     */
    public function getYCFormattedOrderData(oxOrder $oOrder, $aData = null, $isReturn = false)
    {
        $oObject = new stdClass();
        
        // define params needed
        $sLanguage      = oxRegistry::getLang()->getLanguageAbbr($oOrder->oxorder__oxlang->value);
        $oOrderUser     = $this->_getShippingAddressUser($oOrder);
        $sPartner       = $oOrderUser->oxuser__oxcustnr->value;
        $sPartnerNo     = Asign_SoapClientApi::getYCPartnerNumber();       
        $sPartnerType   = Asign_SoapClientApi::getYCPartnerType();
        $sDepoNumber    = Asign_SoapClientApi::getYCDepositorNumber();
        $sPlantID       = Asign_SoapClientApi::getYCPlantId();
        $blIgnoreLot    = Asign_SoapClientApi::isIgnoreLotInforSelected();
        $sShipping      = $this->cleanShippingValue($oOrder->oxorder__oxdeltype->value);
        
        $sDocType       = Asign_SoapClientApi::getYCDocType();
        $sDocMimeType   = Asign_SoapClientApi::getYCDocMimeType();
        $sOrderDocFlag  = Asign_SoapClientApi::getYCOrderDocumentsFlag();

        // set conditional params
        $sPickMessage   = ($aData['pickmessage'] != "") ? $aData['pickmessage'] : Asign_SoapClientApi::getYCPickingMessage();
        $sReturnReason  = ($aData['returnreason'] != "") ? $aData['returnreason'] : Asign_SoapClientApi::getYCReturnReason();

        // initiate formatting process with initial details
        $aParams = $this->getInitialParams("WAB");
        $oObject->ControlReference = new stdClass();
        foreach ($aParams as $key => $param) {
            $oObject->ControlReference->$key = $param;
        }
       
        // order header information
        $oObject->Order                                 = new stdClass();
        $oObject->Order->OrderHeader                    = new stdClass();
        $oObject->Order->OrderHeader->DepositorNo       = $sDepoNumber;
        $oObject->Order->OrderHeader->CustomerOrderNo   = $oOrder->oxorder__oxordernr->value;
        $oObject->Order->OrderHeader->CustomerOrderDate = date("Ymd", strtotime($oOrder->oxorder__oxorderdate->value));
        
        // order partner information
        $oObject->Order->PartnerAddress                            = new stdClass();
        $oObject->Order->PartnerAddress->Partner                   = new stdClass();
        $oObject->Order->PartnerAddress->Partner->PartnerType      = $sPartnerType;
        $oObject->Order->PartnerAddress->Partner->PartnerNo        = $sPartnerNo;
        $oObject->Order->PartnerAddress->Partner->PartnerReference = $sPartner;

        // get country ISO
        $countryISO = $this->getCountryISO($oOrderUser->oxuser__oxcountryid->value);
        $sSalutation = $this->getLangBasedSal($oOrder->oxorder__oxlang->value, $oOrderUser->oxuser__oxsal->value);

        $oObject->Order->PartnerAddress->Partner->Title            = $sSalutation;
        $oObject->Order->PartnerAddress->Partner->Name1            = $oOrderUser->oxuser__oxfname->value . " " . $oOrderUser->oxuser__oxlname->value;
        $oObject->Order->PartnerAddress->Partner->Street           = $oOrderUser->oxuser__oxstreet->value . ", " . $oOrderUser->oxuser__oxstreetnr->value;
        $oObject->Order->PartnerAddress->Partner->CountryCode      = $countryISO;
        $oObject->Order->PartnerAddress->Partner->ZIPCode          = $this->verifyZipStatus($oOrderUser->oxuser__oxzip->value, $countryISO);
        $oObject->Order->PartnerAddress->Partner->City             = $oOrderUser->oxuser__oxcity->value;
        $oObject->Order->PartnerAddress->Partner->Email            = $oOrderUser->oxuser__oxusername->value;
        $oObject->Order->PartnerAddress->Partner->LanguageCode     = strtolower($sLanguage);

        // if the phone and fax details allowed...
        if (Asign_SoapClientApi::usePhoneAndFaxDetails()) {
            $oObject->Order->PartnerAddress->Partner->PhoneNo          = $this->returnCleanValues($oOrderUser->oxuser__oxfon->value);
            $oObject->Order->PartnerAddress->Partner->MobileNo         = $this->returnCleanValues($oOrderUser->oxuser__oxfon->value);
            $oObject->Order->PartnerAddress->Partner->SMSAvisMobNo     = $this->returnCleanValues($oOrderUser->oxuser__oxfon->value);
            $oObject->Order->PartnerAddress->Partner->FaxNo            = $this->returnCleanValues($oOrderUser->oxuser__oxfax->value);
        }

        // Value added information
        $oObject->Order->ValueAddedServices                         = new stdClass();
        $oObject->Order->ValueAddedServices->AdditionalService      = new stdClass();

        // if the return operation
        if ($isReturn) {
            $sBasicShipping = "RETURN";
            $sAdditionalShipping = "";
        } else {
            $sBasicShipping = trim(reset($sShipping));
            if (count($sShipping) > 1) {
                $sAdditionalShipping = trim(end($sShipping));
            }            
        }
        
        $oObject->Order->ValueAddedServices->AdditionalService->BasicShippingServices   = $sBasicShipping;
        $oObject->Order->ValueAddedServices->AdditionalService->AdditionalShippingServices = $sAdditionalShipping;

        // order articles information
        $arrayOfObjects = array();
        $oObject->Order->OrderPositions                             = new stdClass();                
        $oOrderArticles = $oOrder->getOrderArticles();

        // define counter variable
        $iterator = 1;
        foreach ($oOrderArticles as $key=>$article) {

            $oArticle = $article->getArticle();

            $sYCResponse = $oArticle->getFieldData(asign_yellowcube_model::YCRESPONSE);
            if(empty($sYCResponse)){ // if an article has an empty YC response it hasn't been entered in the YC and should not be sent to YC - it would cause an error
                continue;
            }

            var_dump($article);

            // per article YCLot and lot details...
            $aSpsParams     = $this->getSpsParams($article->getProductId());

            // if the LOT information is ignored then?
            if (!$blIgnoreLot) {
                $sYCLot         = $aSpsParams['sYellowCubeYCLot'];
                $sLot           = $aSpsParams['sYellowCubeLot'];
            }

            // if not set: use OXID > articles > Extended tab values
            $sQuantityISO   = $aSpsParams['sYellowCubeAlternateUnitISO'];
            
            // set module default value
            if (!$sQuantityISO) {
                $sQuantityISO   = Asign_SoapClientApi::getYCQuantityISO();
            }

            $oObject->Order->OrderPositions->Position                   = new stdClass();
            $oObject->Order->OrderPositions->Position->PosNo            = $iterator;
            $oObject->Order->OrderPositions->Position->ArticleNo        = $article->oxorderarticles__oxartnum->value;
            $oObject->Order->OrderPositions->Position->EAN              = $article->oxorderarticles__oxean->value;
            
            if (!$blIgnoreLot) {
                $oObject->Order->OrderPositions->Position->YCLot            = $sYCLot;
                $oObject->Order->OrderPositions->Position->Lot              = $sLot;
            }
            
            $oObject->Order->OrderPositions->Position->Plant            = $sPlantID;
            $oObject->Order->OrderPositions->Position->Quantity         = $article->oxorderarticles__oxamount->value;
            $oObject->Order->OrderPositions->Position->QuantityISO      = $sQuantityISO;
            $oObject->Order->OrderPositions->Position->ShortDescription = substr($article->oxorderarticles__oxtitle->value,0,40);
            $oObject->Order->OrderPositions->Position->PickingMessage   = $sPickMessage;
            $oObject->Order->OrderPositions->Position->PickingMessageLC = strtolower($sLanguage);
            $oObject->Order->OrderPositions->Position->ReturnReason     = $sReturnReason;
            $arrayOfObjects[] = $oObject->Order->OrderPositions->Position;// assign to array

            $iterator = $iterator + 1;
        }
        $oObject->Order->OrderPositions = $arrayOfObjects; // reverse assign the array to object...

        // PDF order overview..
        if ($aData['pdfdata'] != "" || $aData['pdfdata'] != null) {
            $oObject->Order->OrderDocuments                                 = new stdClass();
            $oObject->Order->OrderDocuments->Docs                           = new stdClass();
            $oObject->Order->OrderDocuments->OrderDocumentsFlag             = $sOrderDocFlag;
            $oObject->Order->OrderDocuments->Docs->DocType                  = $sDocType;
            $oObject->Order->OrderDocuments->Docs->DocMimeType              = $sDocMimeType;
            $oObject->Order->OrderDocuments->Docs->DocStream                = $aData['pdfdata']; // base64 encoded data
        }

        if(count($oObject->Order->OrderPositions) == 0){
            $oOrder->setYCIgnore();
        }
        
        return $oObject;
    }

    /**
     * Returns the articles details in object form
     *
     * @param object $oArticle Article object id
     * @param string $sFlag    Operation Type
     * @param string $sBaseOUM Base operation unit of measure
     *
     * @return object
     */
    public function getYCFormattedArticleData($oArticle, $sFlag, $sBaseOUM = null)
    {
        // define params needed
        $aSpsParams         = $this->getSpsParams($oArticle->oxarticles__oxid->value);

        $sDepoNumber        = Asign_SoapClientApi::getYCDepositorNumber();
        $sPlantID           = Asign_SoapClientApi::getYCPlantId();
        $sMinRemLife        = Asign_SoapClientApi::getTransMaxTime();

        $sNWeightISO        = ($aSpsParams['sYellowCubeNetWeightISO']) ? $aSpsParams['sYellowCubeNetWeightISO'] : Asign_SoapClientApi::getYCNetWeightISO();
        $sGWeightISO        = ($aSpsParams['sYellowCubeGrossWeightISO']) ? $aSpsParams['sYellowCubeGrossWeightISO'] : Asign_SoapClientApi::getYCGWeightISO();
        $sLengthISO         = ($aSpsParams['sYellowCubeLengthISO']) ? $aSpsParams['sYellowCubeLengthISO'] : Asign_SoapClientApi::getYCLengthISO();
        $sWidthISO          = ($aSpsParams['sYellowCubeWidthISO']) ? $aSpsParams['sYellowCubeWidthISO'] : Asign_SoapClientApi::getYCWidthISO();
        $sHeightISO         = ($aSpsParams['sYellowCubeHeightISO']) ? $aSpsParams['sYellowCubeHeightISO'] : Asign_SoapClientApi::getYCHeightISO();
        $sVolumeISO         = ($aSpsParams['sYellowCubeVolumeISO']) ? $aSpsParams['sYellowCubeVolumeISO'] : Asign_SoapClientApi::getYCVolumeISO();
        $sEANType           = ($aSpsParams['sYellowCubeEANType']) ? $aSpsParams['sYellowCubeEANType'] : Asign_SoapClientApi::getYCEANType();
        $sAlternateUnitISO  = ($aSpsParams['sYellowCubeAlternateUnitISO']) ? $aSpsParams['sYellowCubeAlternateUnitISO'] : Asign_SoapClientApi::getYCAlternateUnitISO();
        $sBatchReq          = $aSpsParams['sYellowCubeBatchMngtReq'];
        $sExpDateType       = $aSpsParams['sYellowCubePeriodExpDateType'];
        $sSerialNoFlag      = $aSpsParams['sYellowCubeSerialNoFlag'];
        $sAltNumeratorUOM   = $aSpsParams['sYellowCubeAltNumeratorUOM'];
        $sAltDenominatorUOM = $aSpsParams['sYellowCubeAltDenominatorUOM'];

        // initiate formatting process with initial details
        $aParams            = $this->getInitialParams("ART");
        $oObject            = new stdClass();
        $oObject->ControlReference = new stdClass();
        foreach ($aParams as $key => $param) {
            $oObject->ControlReference->$key = $param;
        }

        // if no stock available for this article then,
        // deactivate this article in warehouse
        if ($oArticle->oxarticles__oxstock->value === 0) {
            $sFlag = "D";
        }

        // set default baseOUM if not received
        if ($sBaseOUM == null) {
            $sBaseOUM = $sAlternateUnitISO;
        }

        // set the length, width, height, volume
        $dLength    = $oArticle->oxarticles__oxlength->value;
        $dWidth     = $oArticle->oxarticles__oxwidth->value;
        $dHeight    = $oArticle->oxarticles__oxheight->value;
                
        // get precise volume values
        $dVolume = $this->calcArticleVolume($dLength, $dWidth, $dHeight, $sLengthISO, $sWidthISO, $sHeightISO, $sVolumeISO);
        
        // set the article data now!!
        $oObject->ArticleList                               = new stdClass();
        $oObject->ArticleList->Article                      = new stdClass();
        $oObject->ArticleList->Article->ChangeFlag          = $sFlag;
        $oObject->ArticleList->Article->DepositorNo         = $sDepoNumber;
        $oObject->ArticleList->Article->PlantID             = $sPlantID;
        $oObject->ArticleList->Article->ArticleNo           = $oArticle->oxarticles__oxartnum->value; // artnum
        $oObject->ArticleList->Article->BaseUOM             = $sBaseOUM;
        $oObject->ArticleList->Article->NetWeight["_"]      = round($oArticle->oxarticles__oxweight->value, 3); // weight
        $oObject->ArticleList->Article->NetWeight["ISO"]    = $sNWeightISO; // ISO
        $oObject->ArticleList->Article->BatchMngtReq        = $sBatchReq;
        $oObject->ArticleList->Article->Restlaufzeit        = $sMinRemLife;
        $oObject->ArticleList->Article->PeriodExpDateType   = $sExpDateType;
        $oObject->ArticleList->Article->SerialNoFlag        = $sSerialNoFlag;
        
        // Add unit data
        $oObject->ArticleList->Article->UnitsOfMeasure                      = new stdClass();
        $oObject->ArticleList->Article->UnitsOfMeasure->EAN["EANType"]      = $sEANType; // EANType
        
        if ($oArticle->oxarticles__oxean->value) {
            $sEANvalue = sprintf("%09d", $oArticle->oxarticles__oxean->value);
        }

        /** @var string $sEANvalue */
        $oObject->ArticleList->Article->UnitsOfMeasure->EAN["_"]            = $sEANvalue; // EAN
        $oObject->ArticleList->Article->UnitsOfMeasure->AlternateUnitISO    = $sAlternateUnitISO;
        $oObject->ArticleList->Article->UnitsOfMeasure->AltNumeratorUOM     = $sAltNumeratorUOM;
        $oObject->ArticleList->Article->UnitsOfMeasure->AltDenominatorUOM   = $sAltDenominatorUOM;
        $oObject->ArticleList->Article->UnitsOfMeasure->GrossWeight["ISO"]  = $sGWeightISO;
        $oObject->ArticleList->Article->UnitsOfMeasure->GrossWeight["_"]    = round($oArticle->oxarticles__oxweight->value, 3);
        $oObject->ArticleList->Article->UnitsOfMeasure->Length["ISO"]       = $sLengthISO;
        $oObject->ArticleList->Article->UnitsOfMeasure->Length["_"]         = round($dLength, 3);
        $oObject->ArticleList->Article->UnitsOfMeasure->Width["ISO"]        = $sWidthISO;
        $oObject->ArticleList->Article->UnitsOfMeasure->Width["_"]          = round($dWidth, 3);
        $oObject->ArticleList->Article->UnitsOfMeasure->Height["ISO"]       = $sHeightISO;
        $oObject->ArticleList->Article->UnitsOfMeasure->Height["_"]         = round($dHeight, 3);
        $oObject->ArticleList->Article->UnitsOfMeasure->Volume["ISO"]       = $sVolumeISO;
        $oObject->ArticleList->Article->UnitsOfMeasure->Volume["_"]         = round($dVolume, 3);

        // article description
        $arrayOfObjects = array();
        $oObject->ArticleList->Article->ArticleDescriptions                 = new stdClass();
        $aLangs = oxRegistry::getLang()->getLanguageArray();
        $proid = $oArticle->oxarticles__oxid->value;

        foreach ($aLangs as $lang) {
            $id   = $lang->id;
            $abbr = $lang->abbr;
            
            if ($id > 0) {
                $sTitle = "oxtitle_" . $id;
            } else {
                $sTitle = "oxtitle";
            }           

            $oObject->ArticleList->Article->ArticleDescriptions->ArticleDescription = array();
            $proTitle = oxDb::getDb()->getOne("select `".$sTitle."` from `oxarticles` where `oxid` = '".$proid."'");           
            
            if($proTitle) {
                $oObject->ArticleList->Article->ArticleDescriptions->ArticleDescription["ArticleDescriptionLC"] = $abbr;
                $oObject->ArticleList->Article->ArticleDescriptions->ArticleDescription["_"] = substr($proTitle, 0, 40);
                $arrayOfObjects[] = $oObject->ArticleList->Article->ArticleDescriptions->ArticleDescription;
            }            
        }
        $oObject->ArticleList->Article->ArticleDescriptions = $arrayOfObjects;
        
        return $oObject;
    }


    /**
     * Validates zipcode values
     * 
     * @param string $zipValue   Zipcode value
     * @param string $countryISO CountryCode
     *
     * @return string
     */
    public function verifyZipStatus($zipValue, $countryISO)
    {
        try {
            // flip chars -> iso
            $myArray = array_flip($this->aCountryVsZip);
            if (in_array($countryISO, $myArray)) {
                $maxChars = $this->aCountryVsZip[$countryISO];
                if (strlen($zipValue) != $maxChars) {
                    error_log("[ ".date("Y-m-d H:i:s")." ] ". oxRegistry::getLang()->translateString('ASIGN_MESSAGE_ZIPCODE_MISMATCH') ." \n", 3, $this->sLogFilepath);
                    oxRegistry::get("oxUtilsView")->addErrorToDisplay(new oxException(oxRegistry::getLang()->translateString('ASIGN_MESSAGE_ZIPCODE_MISMATCH')));
                } elseif (preg_match('/[A-Za-z]/', $zipValue)) {
                    error_log("[ ".date("Y-m-d H:i:s")." ] ". oxRegistry::getLang()->translateString('ASIGN_MESSAGE_ZIPCODE_INVALID') ." \n", 3, $this->sLogFilepath);
                    oxRegistry::get("oxUtilsView")->addErrorToDisplay(new oxException(oxRegistry::getLang()->translateString('ASIGN_MESSAGE_ZIPCODE_INVALID')));
                } else {
                    return $zipValue;
                }
            } else {
                return $zipValue;
            }
        } catch (Exception $sEx) {
            error_log("[ ".date("Y-m-d H:i:s")." ][Error: " . $sEx->getCode() . "] ". $sEx->getMessage() ." \n", 3, $this->sLogFilepath);
            oxRegistry::get("oxUtilsView")->addErrorToDisplay(new oxException($sEx->getMessage()));
        }
    }

    /**
     * Replaces and returns shipping method as YC format
     * e.g. BasicShippingServices = ECO,PRI,etc.
     * AdditionalShippingServices = SI;SA
     *
     * @param string $sValue SPS Shipping Value
     *
     * @return mixed
     */
    protected function cleanShippingValue($sValue)
    {
        $sValue = str_replace("SPS_", "", $sValue);
        $aShipValue = explode("_", $sValue);

        return $aShipValue;
    }
    
    /**
     * Returns Salutation based on langauge
     *
     * @param integer $iLang Language id
     * @param string  $sSal  Salutation value
     *
     * @return string
     */
    protected function getLangBasedSal($iLang, $sSal)
    {
        $sLang = oxRegistry::getLang()->getLanguageAbbr($iLang);
        switch ($sLang) {
        case 'de': return $this->aSaluationsDE[$sSal];
            break;
            
        case 'en': return $this->aSaluationsEN[$sSal];
            break;
            
        case 'fr': return $this->aSaluationsFR[$sSal];
            break;
            
        case 'it': return $this->aSaluationsIT[$sSal];
            break;            
        }
    }
    
    /**
     * Format OXID default to SPS form
     *
     * @param string $sValue OXID unitname
     *
     * @return string
     */
    protected function convetOXID2SPSformat($sValue)
    {
        $aQtyIso = array(
            '_UNIT_KG'      => 'KGM',
            '_UNIT_G'       => 'GRM',
            '_UNIT_L'       => 'MTQ',
            '_UNIT_ML'      => 'CMQ',
            '_UNIT_CM'      => 'CMT',
            '_UNIT_MM'      => 'MMT',
            '_UNIT_M'       => 'MTQ',
            '_UNIT_M3'      => 'MTQ',
            '_UNIT_PIECE'   => 'PCE',
            '_UNIT_ITEM'    => 'PCE'
        );
        
        return $aQtyIso[$sValue];
    }
    
    /**
     * Converts and sends Volume value based on Units
     *
     * @param float  $dLength    Length val
     * @param float  $dWidth     Width val
     * @param float  $dHeight    Height val
     * @param float  $sLength    Length Unit
     * @param float  $sWidth     Width unit
     * @param float  $sHeight    Height unit     
     * @param string $sVolumeISO Volume ISO name
     *
     * @return float
     */
    public function calcArticleVolume($dLength, $dWidth, $dHeight, $sLength, $sWidth, $sHeight, $sVolumeISO)
    {
        switch ($sVolumeISO) {
        case 'CMQ': $lt = $this->getAdjustedValues($sLength, $dLength);
                    $wt = $this->getAdjustedValues($sWidth, $dWidth);
                    $ht = $this->getAdjustedValues($sHeight, $dHeight);
            break;            
        case 'MTQ': $lt = $this->getAdjustedValues($sLength, $dLength, 'm');
                    $wt = $this->getAdjustedValues($sWidth, $dWidth, 'm');
                    $ht = $this->getAdjustedValues($sHeight, $dHeight, 'm');
            break;
        }
        
        return $lt * $wt * $ht;
    }

    /**
     * Returns adjusted value as per Unit and Type
     *
     * @param string $sUnit Unit name
     * @param float  $dUnit Unit value
     * @param string $sType Volume Unit - c=CMQ, m=MTQ
     *
     * @return float
     */
    protected function getAdjustedValues($sUnit, $dUnit, $sType = 'c')
    {
        switch ($sUnit) {
        case 'CMT': if ($sType === 'm') {
                        return round($dUnit / 100, 3);
                    }
                
                    return round($dUnit, 3);
            break;
        case 'MMT': if ($sType === 'm') {
                        return round($dUnit / 1000, 3);
                    }
                
                    return round($dUnit / 10, 3);
            break;
        case 'MTR': if ($sType === 'm') {
                        return round($dUnit, 3);
                    }
        
                    return round($dUnit * 100, 3);
            break;
        }
    }
}
