<?php
/**
 * Extends order_overview class file
 *
 * @category  asign
 * @package
 * @author    entwicklung@a-sign.ch
 * @copyright asign
 * @license   http://www.a-sign.ch/
 * @version   2.0
 * @link      http://www.a-sign.ch/
 * @see
 * @since     File available since Release 1.0
 */

/**
 * Class InvoicepdfOrder_Overview extends order_overview.
 */
class InvoicepdfThankyou extends InvoicepdfThankyou_parent
{
    /**
     * PDF file export path
     *
     * @var string
     */
    protected $_sYellowcubePdfPath = "/export/asign/pdf/";
    
    /**
     * First checks for basket - if no such object available -
     * redirects to start page. Otherwise - executes parent::render()
     * and returns name of template to render thankyou::_sThisTemplate.
     *
     * @return  string  current template file name
     */
    public function render()
    {
        if (!$this->_oBasket || !$this->_oBasket->getProductsCount()) {
            oxRegistry::getUtils()->redirect($this->getConfig()->getShopHomeURL().'&cl=start', true, 302);
        }

        parent::render();

        $oUser = $this->getUser();

        // removing also unregistered user info (#2580)
        if (!$oUser || !$oUser->oxuser__oxpassword->value) {
            oxRegistry::getSession()->deleteVar('usr');
            oxRegistry::getSession()->deleteVar('dynvalue');
        }

        // loading order sometimes needed in template
        $oOrderId = $this->_oBasket->getOrderId();
        if ($oOrderId) {        	
            // owners stock reminder
            $oEmail = oxNew('oxemail');
            $oEmail->sendStockReminder($this->_oBasket->getContents());
        }
        
        // generate PDF automatically        
        if ($this->_oBasket->getPaymentId() !== 'oxidpayadvance') {
            $sFilename = $this->saveGeneratedPDF($oOrderId);
            if ($sFilename) {       
                // define the paths
                $sShopDir = oxRegistry::getConfig()->getConfigParam('sShopDir');
                $sFilename = $sShopDir . $this->_sYellowcubePdfPath . $sFilename;
                
                // get the base64 encoded content
                $sContent  = file_get_contents($sFilename);
                
                // send the order details and content...                 
                $this->sendGeneratedPDF($oOrderId, $sContent);
            }
        }
        
        // we must set active class as start
        $this->getViewConfig()->setViewConfigParam('cl', 'start');

        return $this->_sThisTemplate;
    }
    
    /**
     * Performs PDF export to user (outputs file to save).
     *
     * @param string $soxId Direct call
     *
     * @return null
     */
    public function saveGeneratedPDF($soxId)
    {
        if ($soxId != "-1" && isset($soxId)) {
            // load object
            $oOrder = oxNew("oxorder");
            if ($oOrder->load($soxId)) {
                $sTrimmedBillName = trim($oOrder->oxorder__oxbilllname->getRawValue());
                $sFilename = $oOrder->oxorder__oxordernr->value . "_" . $sTrimmedBillName . ".pdf";
                $sFilename = $this->makeValidFileName($sFilename);

                ob_start();
                $sShopDir = oxRegistry::getConfig()->getConfigParam('sShopDir');
                $oOrder->genPDF($sShopDir . $this->_sYellowcubePdfPath . $sFilename, oxRegistry::getConfig()->getRequestParameter("pdflanguage"), true);
                ob_get_contents();
                ob_end_clean();

                return $sFilename;
            }
        }
    }
    
    /**
     * Gets proper file name
     *
     * @param string $sFilename file name
     *
     * @return string
     */
    public function makeValidFileName($sFilename)
    {
        $sFilename = preg_replace('/[\s]+/', '_', $sFilename);
        $sFilename = preg_replace('/[^a-zA-Z0-9_\.-]/', '', $sFilename);

        return str_replace(' ', '_', $sFilename);
    }
    
    /**
     * Executes yellowcube order creation
     *     
     * @param string $sDirectOxid Order Object ID
     * @param string $sDirectData encoded data
     *
     * @return null
     */
    public function sendGeneratedPDF($sDirectOxid = null, $sDirectData = null)
    {        
        $oOrder = oxNew("oxorder");
        $oOrder->load($sDirectOxid);
        
        // set in array:
        $aParams = array(
            'pdfdata'       => $sDirectData,
            'batchnum'      => "",'supbatchnum'   => "",'pickmessage'   => "",'returnreason'  => "",
       );

        // execute the order object
        $oYCube = oxNew("asign_yellowcubecore");        
        $oRequestObject = $oYCube->getYCFormattedOrderData($oOrder, $aParams);
        $aResponse = $oYCube->createYCCustomerOrder($oRequestObject);
       
        if ($aResponse != null || $aResponse != "") {
            // save the response to database
            $oModel = oxNew("asign_yellowcube_model");
            $oModel->saveResponseData($aResponse, "oxorder", $sDirectOxid);
        }        
    }
}
