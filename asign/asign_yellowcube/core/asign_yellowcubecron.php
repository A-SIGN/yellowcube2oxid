<?php
/**
 * Defines yellowcube cron functions
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
 * @see       Asign_YellowCubeCron
 * @since     File available since Release 2.0
 */

/**
 * Yellowcube cron functions
 *
 * @category Asign
 * @package  Asign_Yellowcube_V_EE
 * @author   Asign <entwicklung@a-sign.ch>
 * @license  http://www.a-sign.ch/
 * @link     http://www.a-sign.ch/
 */
class Asign_YellowCubeCron
{
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
     * @param array $options script options
     *
     * @return \Asign_YellowCubeCron
     */
    public function __construct($options)
    {
        /**
         * First option is used for Hashcheck
         * Syntax: php -f autorun/runscript.php <hashvalue> <option1> <option2>
         */
        $sHashValue = $options[1];
        $dbHashValue = oxRegistry::getConfig()->getConfigParam('sYellowCubeCronHash');

        if ($sHashValue != $dbHashValue) {
            die('You are not allowed to access this file!!');
        }

        // define the log file path
        $myconfig = oxRegistry::getConfig();
        $this->sLogFilepath = $myconfig->getShopConfVar("sShopDir") . "modules/asign/asign_yellowcube/logs/YClogs.log";

        /**
         * Options for script:
         *
         * co - Create YC Customer Order
         *      pp - Sends only prepaid orders
         * ia - Insert Article Master Data
         *      ax  - Include only active
         *      ix  - Include only inactive
         *      xx  - Include all
         *      I   - Insert article to yellowcube
         *      U   - Update article to yellowcube
         *      D   - Delete article from yellowcube
         * spdfs - Send InvoicePdfs to yellowcube
         *      pp - Sends only prepaid orders
         *      custom - Sends only orders defined in the settings
         * gi - Get Inventory
         */
        $command = $options[2];// main command - ia,co,gi
        switch ($command) {
            case 'co':  // only for prepayment: CashInAdvance/Vorouskasse is present
                $sMode = $options[3]; // payment - prepad (pp) only
                $this->createYCCustomerOrder($sMode);
                break;

            case 'ia':  // applicable only for articles...
                $sMode = $options[3];// ax, ix, xx
                $sFlag = $options[4];//I, U, D

                // if no flags specified then use from module settings
                if ($sFlag == "") {
                    $sFlag = "I";
                }
                $this->insertArticleMasterData($sMode, $sFlag);
                break;

            case 'gi':
                $this->getInventory();
                break;

            default:
                echo "No options specified...";
                break;
        }
    }

    /**
     * Creates New customer Order in Yellowcube
     *
     * @param string $sMode Mode of Operation
     *
     * @return array
     */
    public function createYCCustomerOrder($sMode = null)
    {
        $iCount = 0;

        // if pp = prepayment then?
        $sWhere = "WHERE `" . asign_yellowcube_oxorder::YCIGNORE . "` != 1
                   AND (`" . asign_yellowcube_model::YCRESPONSE . "` = '' OR `" . asign_yellowcube_model::YCWABRESPONSE . "` = '' OR `" . asign_yellowcube_model::YCWARRESPONSE . "` = '')";// check if initial wab response not present
        if ($sMode === 'pp') {
            $sWhere .= " and `oxpaymenttype` = 'oxidpayadvance' AND `oxpaid` != 0 ";// include only prepayment
        } else {
            $sWhere .= " and `oxpaymenttype` <> 'oxidpayadvance'"; // exclude prepayment
        }

        // get orders based on condition
        $aOrders = oxDb::getDb(oxDb::FETCH_MODE_ASSOC)->getAll("SELECT `oxid` FROM `oxorder` " . $sWhere);

        // traverse and perform YC actions
        foreach ($aOrders as $order) {
            $soxId = $order['oxid'];
            $oOrder = oxNew("oxorder");

            /** @var object $oOrder */
            $oOrder->load($soxId);

            // check if the article is already recorded previously...
            $sRequestField = $this->_getOrderRequestField($oOrder);
            $aResponseType = '';

            $iStatusCode = $this->getRecordedStatus($soxId, $oOrder->getCoreTableName(), $sRequestField);

            // not 10 then create new order
            $oYCube = oxNew("asign_yellowcubecore");
            if ($iStatusCode == null && $oOrder->getFieldData($sRequestField) == '') {
                // execute the order object
                echo "Submitting Order for OXID: " . $soxId . "\n";
                $sFilename = $oOrder->saveGeneratedPDF($soxId);

                if ($sFilename) {
                    // define the paths
                    $sShopDir = oxRegistry::getConfig()->getConfigParam('sShopDir');
                    $sFilename = $sShopDir . "/export/asign/pdf/" . $sFilename;

                    // get the base64 encoded content
                    $sContent  = file_get_contents($sFilename);
                } else {
                    $sContent = '';
                }

                // set in array:
                $aParams = array(
                    'pdfdata'       => $sContent,
                    'batchnum'      => "",'supbatchnum'   => "",'pickmessage'   => "",'returnreason'  => "",
                );

                $oRequestObject = $oYCube->getYCFormattedOrderData($oOrder, $aParams);
                $aResponse = $oYCube->createYCCustomerOrder($oRequestObject);
            } elseif ($iStatusCode < 100) {
                // get the status
                echo "Requesting WAB status for OXID: " . $soxId . "\n";
                $aResponse = $oYCube->getYCGeneralDataStatus($soxId, "WAB");
                $aResponseType = 'WAB';
            } elseif ($iStatusCode == 100) {
                // get the WAR status
                echo "Requesting WAR status for OXID: " . $soxId . "\n";
                $aResponse = $oYCube->getYCGeneralDataStatus($soxId, "WAR");
                $aResponseType = 'WAR';
            }

            // increment the counter
            if (isset($aResponse) && count((array)$aResponse) !== 0) {
                // save the response to database
                $oModel = oxNew("asign_yellowcube_model");
                $oModel->saveResponseData($aResponse, $oOrder->getCoreTableName(), $soxId, $aResponseType);

                if ($oModel->isTrackingNrResponse()) {
                    echo "Sending Tracking E-Mail \n";
                    $oEmail = oxNew("oxemail");
                    $oEmail->sendSendedNowMail($oOrder);
                }

                $iCount = $iCount + 1;
            }
        }

        error_log("[ " . date("Y-m-d H:i:s") . " ][CRON-ORDERS] Total Yellowcube Orders created: " . $iCount . " \n", 3, $this->sLogFilepath);
    }

    /**
     * Inserts Article Master data to Yellowcube
     *
     * @param string $sMode - Mode of handling
     *                        ax - Only active ones
     *                        ix - Only Inactive ones
     *                        xx - All articles
     * @param string $sFlag - Type of action
     *                        Insert(I),
     *                        Update(U),
     *                        Deactivate/Delete(D)
     *
     * @return array
     */
    public function insertArticleMasterData($sMode, $sFlag)
    {
        $oArticle = oxNew("oxarticle");
        $iCount = 0;
        $where = "";

        // form where condition based on options...
        $sWhere = " where `" . asign_yellowcube_model::YCRESPONSE . "` = ''";
        switch ($sMode) {
            case 'ax':
                $sWhere = " and oxactive = 1";
                break;

            case 'ia':
                $sWhere = " and oxactive = 0";
                break;

            case 'xx':
                $sWhere = "";
                break;
        }

        // get all the articles based on above condition...
        $aArticles = oxDb::getDb(oxDb::FETCH_MODE_ASSOC)->getAll("SELECT `oxid` FROM `oxarticles`" . $sWhere);
        foreach ($aArticles as $article) {
            $soxId = $article['oxid'];
            $oArticle->load($soxId);

            // check if the article is already recorded previously...
            $iStatusCode = $this->getRecordedStatus($soxId, "oxarticles");

            // if not 10 then insert the article
            // execute the article object
            // skip product if downloadable/virtual
            if (!$oArticle->isDownloadable()) {
                $oYCube = oxNew("asign_yellowcubecore");
                if ($iStatusCode != 10) {
                    $oRequestObject = $oYCube->getYCFormattedArticleData($oArticle, $sFlag);
                    $aResponse = $oYCube->insertArticleMasterData($oRequestObject);
                } elseif ($iStatusCode == 10 && $iStatusCode != 100) {
                    // get the status
                    $aResponse = $oYCube->getYCGeneralDataStatus($soxId, "ART");
                }

                // increment the counter
                if ($aResponse) {
                    $iCount = $iCount + 1;
                }

                // save the response to database
                $oModel = oxNew("asign_yellowcube_model");
                $oModel->saveResponseData($aResponse, "oxarticles", $soxId);
            }
        }

        error_log("[ " . date("Y-m-d H:i:s") . " ][CRON-ARTICLES] Total items inserted into Yellowcube: " . $iCount . " \n", 3, $this->sLogFilepath);
    }


    /**
     * Checks which step is the present step the order is in based on the filled database fields
     *
     * @param oxOrder $oOrder
     * @return string
     */
    protected function _getOrderRequestField(oxOrder $oOrder)
    {

        $sResponseField = '';

        if ($oOrder->getFieldData(asign_yellowcube_model::YCRESPONSE) == '') {
            $sResponseField = asign_yellowcube_model::YCRESPONSE;
        } elseif ($oOrder->getFieldData(asign_yellowcube_model::YCWABRESPONSE) == '') {
            $sResponseField = asign_yellowcube_model::YCRESPONSE;
        } elseif ($oOrder->getFieldData(asign_yellowcube_model::YCWARRESPONSE) == '') {
            $sResponseField = asign_yellowcube_model::YCWABRESPONSE;
        }

        return $sResponseField;
    }

    /**
     * Returns inventory list from Yellowcube
     *
     * @param string $soxId Object id
     * @param string $sTable Table name
     *
     * @param string null $sResponseType
     * @return string
     */
    protected function getRecordedStatus($soxId, $sTable, $sResponseField = null)
    {
        $oModel = oxNew("asign_yellowcube_model");
        $aParams = $oModel->getYellowcubeReport($soxId, $sTable, false, $sResponseField);

        return $aParams["StatusCode"];
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
        $oYCube = oxNew("asign_yellowcubecore");
        $aResponse = $oYCube->getInventory();

        // update
        $oModel = oxNew("asign_yellowcube_model");
        $iCount = $oModel->saveInventoryData($aResponse);

        error_log("[ " . date("Y-m-d H:i:s") . " ][CRON-INVENTORY] Total updated inventory Items: " . $iCount . " \n", 3, $this->sLogFilepath);
    }
}
