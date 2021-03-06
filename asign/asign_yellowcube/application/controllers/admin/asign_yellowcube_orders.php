<?php
/**
* Renders template file asign_yellowcube_orders.tpl
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
* @see       Asign_YellowCube_Orders
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
class Asign_YellowCube_Orders extends oxAdminView
{
    /**
     * Folderpath for the files to uploaded
     * used in import section
     *
     * @return string
     */
    protected $sUploadPathPdf = "export/asign/pdf/";

    /**
     * Executes parent method parent::render(), show the details
     * for selected object and passed to Smarty engine and returns name of template file
     * "asign_yellowcube_orders.tpl".
     *
     * @return string
     */
    public function render()
    {
        parent::render();

        $soxId      = $this->getEditObjectId();
        $this->_aViewData["oxid"] = $soxId;
        $oModel = oxNew("asign_yellowcube_model");

        // if order response present then?
        $ordResponse = $oModel->getYellowcubeReport($soxId, "oxorder");
        if ($ordResponse !== null) {
            $ordResponse = array_reverse($ordResponse);
        }
        $this->_aViewData["ORDResponse"] = $ordResponse;

        // if wab response present then?
        $wabResponse = $oModel->getYellowcubeWABReport($soxId);        
        if ($wabResponse !== null) {
            $wabResponse = array_reverse($wabResponse);
        }
        $this->_aViewData["WABResponse"] = $wabResponse;

        // if wab response present then?
        $warResponse = $oModel->getYellowcubeWARReport($soxId);

        if ($warResponse !== null) {
            $oGoodsIssue = $warResponse[WAR][0]->GoodsIssue;

            // set the Goods Issue Header
            $aGoodIssueHeader['title'] = 'Goods Issue Header';
            foreach ($oGoodsIssue->GoodsIssueHeader as $key=>$value) {
                $aGoodIssueHeader[$key] = $value;
            }
            $this->_aViewData["GoodResponse"] = $aGoodIssueHeader;

            // set the Customer Order Header
            $aCustomerOrderHeader['title'] = 'Customer Order Header';
            foreach ($oGoodsIssue->CustomerOrderHeader as $key=>$value) {
                $aCustomerOrderHeader[$key] = $value;
            }
            $this->_aViewData["CustResponse"] = $aCustomerOrderHeader;

            // set the Customer Order List
            $aCustomerOrderList = null;
            if (count($oGoodsIssue->CustomerOrderList->CustomerOrderDetail) > 1) {
                foreach ($oGoodsIssue->CustomerOrderList->CustomerOrderDetail as $key=>$response) {
                    $aCustomerOrderList[$key] = (array)$response;
                }
                $this->_aViewData["ListResponse"] = $aCustomerOrderList;
            } else {
                $aCustomerOrderSingle = (array)$oGoodsIssue->CustomerOrderList;
                $aCustomerOrderSingle = reset($aCustomerOrderSingle['CustomerOrderDetail']);
                $this->_aViewData["ListResponseSingle"] = (array)$aCustomerOrderSingle;
            }
        }

        return 'asign_yellowcube_orders.tpl';
    }
    
    /**
     * Executes yellowcube order creation
     *
     * @return null
     */
    public function create()
    {
        $soxId = $this->getEditObjectId();
        $this->_aViewData["oxid"]   = $soxId;
        $oOrder                     = oxNew("oxorder");
        $oOrder->load($soxId);

        // check if the file is uploaded
        $pdfData = null;
        if ($_FILES) {
            $filename = $_FILES["orderpdf"]["name"];
            $tmpfname = $_FILES["orderpdf"]["tmp_name"];
            $pdfData = $this->uploadAndReturnEncodedContent($filename, $tmpfname);
        }

        // set in array:
        $aParams = array(
            'pdfdata'       => $pdfData,
            'batchnum'      => oxRegistry::getConfig()->getRequestParameter("sYCLot"),
            'supbatchnum'   => oxRegistry::getConfig()->getRequestParameter("sLot"),
            'pickmessage'   => oxRegistry::getConfig()->getRequestParameter("sMessage"),
            'returnreason'  => oxRegistry::getConfig()->getRequestParameter("sReason"),
        );

        try {
            // execute the order object
            $oYCube = oxNew("asign_yellowcubecore");
            /** @var string $pdfData - base64 encoded */
            $oRequestObject = $oYCube->getYCFormattedOrderData($oOrder, $aParams);
            $aResponse = $oYCube->createYCCustomerOrder($oRequestObject);

            if ($aResponse == null) {
                $this->_aViewData["mcreate"] = "false";
            } else {
                // get status information
                $aResp = (array) $aResponse;
                $sCode = $aResp["StatusCode"];
                $sType = $aResp["StatusType"];

                // if success response then?
                // save the response to database
                if ($sCode === 10 && $sType === 'S') {
                    $oModel = oxNew("asign_yellowcube_model");
                    $oModel->saveResponseData($aResponse, "oxorder", $soxId);

                    $this->_aViewData["mcreate"] = "true";
                } else {
                    $this->_aViewData["mcreate"] = "false";
                }
            }
        } catch (Exception $oEx) {
            $oModel = oxNew("asign_yellowcube_model");
            $oModel->saveResponseData('Error - Create Customer Order: ' . $oEx->getMessage(), "oxorder", $soxId);

            oxRegistry::get("oxUtilsView")->addErrorDisplay(new Exception($oEx->getMessage()));
        }
    }

    /**
     * Manually imports the order pdf for sending to YC
     * 1) Upload the file to the server
     * 2) Extract the content
     *
     * @param string $filename Filename
     * @param string $tmpfname Temp filename
     *
     * @return null
     */
    public function uploadAndReturnEncodedContent($filename, $tmpfname)
    {
        //set the targetpath: where file to be uploaded
        $sPath = oxRegistry::getConfig()->getConfigParam("sShopDir");
        $target_path = $sPath . $this->sUploadPathPdf;
        $target_path = $target_path.basename($filename);
        $blDocDelete = Asign_SoapClientApi::isYCDeleteOrderFile();

        //check if the folder is writable: if not set permission
        if (!is_writable($target_path)) {
            chmod($target_path, 0777);
        }

        //move the file to targetpath
        try {
            if (move_uploaded_file($tmpfname, $target_path)) {                
                // get file content
                $content = file_get_contents($target_path);

                //unlink the file: based on module setting
                if ($blDocDelete) {
                    unlink($target_path);
                }

                return $content;
            }
        } catch (Exception $fileEx){            
            oxRegistry::get("oxUtilsView")->addErrorToDisplay(new oxException('[Error: Encoding Error] ' . utf8_decode($fileEx->getMessage())));
        }
    }

    /**
     * Returns the status of the created order (WAB)
     *
     * @return array
     */
    public function prestatus()
    {
        $soxId      = $this->getEditObjectId();
        $this->_aViewData["oxid"] = $soxId;

        try {
            $oYCube = oxNew("asign_yellowcubecore");
            $aResponse = $oYCube->getYCGeneralDataStatus($soxId, "WAB");

            // save update status
            if (count((array)$aResponse) == 0) {
                $this->_aViewData["mstatus"] = "false";
            } else {
                $oModel = oxNew("asign_yellowcube_model");
                $oModel->saveResponseData($aResponse, "oxorder", $soxId, 'WAB');

                $this->_aViewData["mstatus"] = "true";
            }
        } catch (Exception $fileEx){
            $oModel = oxNew("asign_yellowcube_model");
            $oModel->saveResponseData('Error - Get Order Status: ' . '[' . $fileEx->getCode() . ']' . $fileEx->getMessage(), "oxorder", $soxId);

            oxRegistry::get("oxUtilsView")->addErrorToDisplay(new oxException('[Error: Get Order Status] ' . $fileEx->getMessage()));
        }
    }  


    /**
     * Returns the status of the created order (WAR)
     *
     * @return array
     */
    public function status()
    {
        $soxId = $this->getEditObjectId();
        $this->_aViewData["oxid"] = $soxId;

        try {
            $oYCube = oxNew("asign_yellowcubecore");
            $aResponse = $oYCube->getYCGeneralDataStatus($soxId, "WAR");

            // save update status
            if (count((array)$aResponse) == 0) {
                $this->_aViewData["mstatus"] = "false";
            } else {
                $oModel = oxNew("asign_yellowcube_model");
                $oModel->saveResponseData($aResponse, "oxorder", $soxId, "WAR");

                $this->_aViewData["mstatus"] = "true";
            }
        } catch (Exception $fileEx){
            $oModel = oxNew("asign_yellowcube_model");
            $oModel->saveResponseData('Error - Get Order Reply: ' . '[' . $fileEx->getCode() . ']' . $fileEx->getMessage(), "oxorder", $soxId);

            oxRegistry::get("oxUtilsView")->addErrorToDisplay(new oxException('[Error: Get Order Reply] ' . utf8_decode($fileEx->getMessage())));
        }
    }   
}
