<?php
/**
* Handles database related queries
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
* @see       Asign_YellowCube_Model
* @since     File available since Release 2.0
*/

/**
* Handles database actions
* 
* @category Asign
* @package  Asign_Yellowcube_V_EE
* @author   Asign <entwicklung@a-sign.ch>
* @license  http://www.a-sign.ch/
* @link     http://www.a-sign.ch
*/
class Asign_YellowCube_Model extends oxI18n
{
    /**
     * Current class name
     *
     * @var string
     */
    protected $_sClassName = 'asign_yellowcube_model';

    /**
    * Tracking response
    * @var boolean
    */
    protected $_bIsTrackingResponse = false;

    /**
    * @var constants
    */
    const YCRESPONSE = 'asignycresponse';
    const YCWABRESPONSE = 'asignycwabresponse';
    const YCWARRESPONSE = 'asignycwarresponse';
    const YCRETRESPONSE = 'asignycretresponse';
    const YCDETAILS = 'asignspsdetails';
  
    /**
     * Class constructor, initiates parent constructor (parent::oxI18n()).
     */
    public function __construct()
    {
        parent::__construct();
        $this->init('asign_ycinventory');
    }
    
    /**
     * Function to save the Response
     * received from Yellowcube 
     *
     * @param array  $aResponseData Array of response
     * @param string $sTable        Tablename to be saved
     * @param string $soxId         Object Id for condition
     * @param string $sType         Mode of transfer
     *
     * @return null
     */
    public function saveResponseData($aResponseData, $sTable, $soxId, $sType = null)
    {
        $aResponseData = (array)$aResponseData;

        $sResponseData = serialize((array)$aResponseData);
        // add this date line
        $sDeliveryDate = date('Y-m-d H:i:s', strtotime($aResponseData[WAR][0]->GoodsIssue->CustomerOrderHeader->YCDeliveryDate));
        // end here
        $sTrackingCode = '';
        $sSQLTrackingInsert = '';

        $oDb = oxDb::getDb();
        switch ($sType) {
            case 'WAB': $sColumn = self::YCWABRESPONSE;
                break;

            case 'WAR': $sColumn = self::YCWARRESPONSE;
                $sTrackingCode = $aResponseData[WAR][0]->GoodsIssue->CustomerOrderHeader->PostalShipmentNo;
                break;

            case 'RET': $sColumn = self::YCRETRESPONSE;
                break;

            default: $sColumn = self::YCRESPONSE;
                break;
        }

        // if the table=oxorder and trackingcode is not empty then?
        if($sTrackingCode != '' && $sTable == "oxorder"){
            $sSQLTrackingInsert = "',`oxsenddate` = '" . $sDeliveryDate . "', `oxfolder` = 'ORDERFOLDER_FINISHED', `oxtrackcode` = '" . $sTrackingCode;
            $this->_bIsTrackingResponse = true;
        }

        $oDb->execute("update `" . $sTable . "` set `" . $sColumn . "` = '" . $sResponseData . $sSQLTrackingInsert . "' where `oxid` = '" . $soxId . "'");
    }

    /**
     * Returns saved status from the saved data
     *
     * @param string $sOxid Object id
     * @param string $sTable Table name
     * @param bool $isReturn Is return action?
     *
     * @param string $sColumn
     * @return array
     */
    public function getYellowcubeReport($sOxid, $sTable, $isReturn = false, $sColumn = self::YCRESPONSE)
    {

        if ($isReturn) {
            $sColumn = self::YCRETRESPONSE;
        }
        
        $sQuery = "select `" . $sColumn . "` from `" . $sTable . "` where `oxid` = '" . $sOxid ."'";
        $aComplete = oxDb::getDb(oxDb::FETCH_MODE_ASSOC)->getOne($sQuery);
        $aResponse = unserialize($aComplete);
       
        $aReturn = array();
        if (!empty($aResponse)) {
            foreach ($aResponse as $key => $result) {
                $aReturn[$key] = $result;
            }           
        }
        
        return $aReturn;
    }

    /**
     * Returns WAB Response
     *
     * @param string $sOxid Object id
     * 
     * @return array
     */
    public function getYellowcubeWABReport($sOxid)
    {
        $aComplete = oxDb::getDb(oxDb::FETCH_MODE_ASSOC)->getOne("select `" . self::YCWABRESPONSE . "` from `oxorder` where `oxid` = '" . $sOxid ."'");
        $aResponse = unserialize($aComplete);
       
        $aReturn = null;
        if (!empty($aResponse)) {
            foreach ($aResponse as $key => $result) {
                $aReturn[$key] = $result;
            }
        }
        
        return $aReturn;
    }

    /**
     * Returns WAR Response 
     *
     * @param string $sOxid Object id
     * 
     * @return array
     */
    public function getYellowcubeWARReport($sOxid)
    {
        $aComplete = oxDb::getDb(oxDb::FETCH_MODE_ASSOC)->getOne("select `" . self::YCWARRESPONSE . "` from `oxorder` where `oxid` = '" . $sOxid ."'");
        $aResponse = unserialize($aComplete);
       
        $aReturn = null;
        if (!empty($aResponse)) {
            foreach ($aResponse as $key => $result) {
                $aReturn[$key] = $result;
            }
        }
        
        return $aReturn;
    }
    
    /**
     * Returns stored Yellowcube reference
     *
     * @param string $sOxid Object id
     * @param string $sType Type of query
     * 
     * @return array
     */
    public function getYCReferenceNumber($sOxid, $sType)
    {
        $aTables =  array(
                        'ART'   => 'oxarticles',
                        'WAB'   => 'oxorder',
                        'WAR'   => 'oxorder'
                   );
        $aResponse = $this->getYellowcubeReport($sOxid, $aTables[$sType]);
        $sReference = $aResponse['Reference'];
                
        // if second calling then?
        if ($sType == "WAR") {
            $sReference = oxDb::getDb()->getOne("select `oxordernr` from `oxorder` where `oxid` = '" . $sOxid . "'");
        }        
        
        return $sReference;
    }
    
    /**
     * Stores inventory information received from Yellowcube
     *
     * @param array $aResponseData Array of response
     *
     * @return null
     */
    public function saveInventoryData($aResponseData)
    {        
        if ($aResponseData) {
            $iCount = 0;

            $this->resetInventoryData();

            foreach ($aResponseData->ArticleList->Article as $article) {
                // format the response data
                $aFormData = $this->frameResponseToArray($article);
                $this->storeFinalData($aFormData);

                $iCount = $iCount + 1;
            }

            return $iCount;
        }        
    }

    /**
     * Resets the oxstock value for all articles that are entered in the YC warehouse.
     * This should be run before setting stock, because YC only sends information on articles, that have
     * over 0 stock.
     */
    public function resetInventoryData(){
        $oArticleList = oxNew('oxlist');
        $oArticleList->init('oxarticle');
        $oArticleList->selectString("SELECT * FROM oxarticles WHERE `" . self::YCRESPONSE . "` != ''");

        foreach($oArticleList as $oArticle){
            $aResponse = unserialize($oArticle->oxarticles__asignycresponse->value);

            if($aResponse['StatusCode'] == 100){
                $oArticle->oxarticles__oxstock = new oxField(0);
                $oArticle->save();
            }
        }
    }

    /**
     * Frames object data to array response
     *
     * @param object $oData Object of response
     *
     * @return array
     */
    protected function frameResponseToArray($oData)
    {
        // check if the storage location is YAFS.
        // that means the stock is available.
        // if not then don't insert it at all
        $isStocker = false;
        if ($oData->StorageLocation == 'YAFS') {
            $isStocker = true;
        }

        // frame the additioanal information array
        $aAddInfo = array(
            'EAN'               => $oData->EAN,  
            'Plant'             => $oData->Plant,
            'StorageLocation'   => $oData->StorageLocation,
            'StockType'         => $oData->StockType,
            'QuantityISO'       => $oData->QuantityUOM->QuantityISO,
            'QuantityUOM'       => $oData->QuantityUOM->_,
            'YCLot'             => $oData->YCLot,
            'Lot'               => $oData->Lot,
            'BestBeforeDate'    => $oData->BestBeforeDate,
        );

        // serialize additional info
        $sAllParams = serialize($aAddInfo); 

        // finalize array
        $aReturns = array(
            'oxid'      => md5($oData->YCArticleNo),
            'shopid'    => $this->getShopName($this->getConfig()->getShopId()),
            'ycartnum'  => $oData->YCArticleNo,
            'artnum'    => $oData->ArticleNo,
            'artdesc'   => $oData->ArticleDescription,    
            'allparams' => $sAllParams,
            'stockval'  => $oData->QuantityUOM->_,
            'isvalid'   => $isStocker
        );

        return $aReturns;
    }

    /**
     * Saves data to database and return status
     *
     * @param array $aData Array of Data
     *
     * @return bool
     */
    public function storeFinalData($aData)
    {
        try{
            // if stock is avalaible then insert...
            if ($aData['isvalid']) {
                $iQuery = "insert into `asign_ycinventory` set `oxid` = '".$aData['oxid']."', `oxshopid` = '" . $aData['shopid'] . "', `oxycarticlenr` = '" . $aData['ycartnum'] . "', `oxarticlenr` = '" . $aData['artnum'] . "', `oxartdesc` = '" . $aData['artdesc'] . "', `oxadditional` = '" . $aData['allparams'] . "' on duplicate key update `oxtimestamp` = CURRENT_TIMESTAMP";
                oxDb::getDb()->execute($iQuery);   
                
                // update the stock information in oxarticles table
                $iStock = (int) $aData['stockval'];
                oxDb::getDb()->execute("update `oxarticles` set `oxstock` = '" . $iStock . "' where `oxartnum` = '" . $aData['artnum'] . "' and `oxactive` = 1");

                // save the last updated date
                $updateDate = date('Y-m-d H:i:s');
                $this->getConfig()->saveShopConfVar('str', 'ycupdlast', $updateDate);

                return true;
            }            
        } catch(Exception $dbEx) {
            oxRegistry::get("oxUtilsView")->addErrorToDisplay(new oxException('Database Error: ' . $dbEx->getMessage()));
        }
    }
    
    /**
     * Returns stored inventory data
     *
     * @param string $soxId Select object id
     *      
     * @return array
     */
    public function getInventoryData($soxId)
    {
        $sResponse = oxDb::getDb()->getOne("select `oxadditional` from `asign_ycinventory` where `oxid` = '" . $soxId . "'");
        $aResponse = unserialize($sResponse);
        
        return $aResponse;
    }
    
    /**
     * Returns Shop name for the sent id
     *
     * @param integer $shopid Shop Id
     * 
     * @return string
     */
    public function getShopName($shopid)
    {
        return oxDb::getDb()->getOne("select `oxname` from `oxshops` where `oxid` = '" . $shopid . "'");
    }

    /**
     * Returns Stock value meaning
     *
     * @param string $iStock Stock value
     *
     * @return string
     */
    public function getStockValueMeaning($iStock)
    {
        $aStocks =  array(
                        'X' => 'Qualitätsprüfung',
                        'S' => 'Gesperrt',
                        '2' => 'Qualitätsprüfung',
                        '3' => 'Gesperrt',
                        '0' => 'Frei verwendbar',
                        ''  => 'Frei verwendbar',
                        'F' => 'Frei verwendbar'
                   );
        
        return $aStocks[$iStock];
    }

    /**
     * Return SPS values..
     *
     * @param string $sOxid article object id
     *
     * @return string
     */
    public function getSpSDetailsForThisArticle($sOxid)
    {
        $sParam = oxDb::getDb(oxDb::FETCH_MODE_ASSOC)->getOne("select `" . self::YCDETAILS . "` from `oxarticles` where `oxid` = '".$sOxid."'");
        $aParams = unserialize($sParam);

        return $aParams;
    }

    /**
     * Getter method for the boolean status variable
     * @return bool
     */
    public function isTrackingNrResponse(){
        return $this->_bIsTrackingResponse;
    }
}
