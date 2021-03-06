<?php
/**
 * This file is part of OXID eShop Community Edition.
 *
 * OXID eShop Community Edition is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eShop Community Edition is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2014
 * @version   OXID eShop CE
 */

/**
 * Order pdf generator class
 */
class InvoicepdfOxOrder extends InvoicepdfOxOrder_parent
{

    /**
     * PDF language
     *
     * @var int
     */
    protected $_iSelectedLang = 0;

    /**
     * Cached active shop object
     *
     * @var object
     */
    protected $_oActShop = null;

    /**
     * Order arctiles VAT's
     *
     * @var array
     */
    protected $_aVATs = array();

    /**
     * Order currency object
     *
     * @var object
     */
    protected $_oCur = null;


    /**
     * Set language for pdf generation.
     *
     * @param integer $iLang Language id.
     */
    public function setSelectedLang($iLang)
    {
        $this->_iSelectedLang = $iLang;
    }

    /**
     * Returns active shop object.
     *
     * @return oxshop $oUser
     */
    protected function _getActShop()
    {
        // shop is allready loaded
        if ($this->_oActShop !== null) {
            return $this->_oActShop;
        }

        $this->_oActShop = oxNew('oxshop');
        $this->_oActShop->load($this->getConfig()->getShopId());

        return $this->_oActShop;
    }

    /**
     * Returns translated string.
     *
     * @param string $sString string to translate
     *
     * @return string
     */
    public function translate($sString)
    {
        return oxRegistry::getLang()->translateString($sString, $this->getSelectedLang());
    }

    /**
     * Formats pdf page footer.
     *
     * @param object $oPdf pdf document object
     */
    public function pdfFooter($oPdf)
    {
        $iFooterStart = 270;
        $oShop = $this->_getActShop();

        $oPdfBlock = new InvoicepdfBlock($oPdf);
        $oPdfBlock->setLineHeight(1.2);
        $oPdfBlock->line(InvoicepdfBlock::LINEBEGIN, $iFooterStart, InvoicepdfBlock::LINEEND, $iFooterStart);
        $iFooterStart = $oPdfBlock->nextLine($iFooterStart);
        $iLine = $iFooterStart;
        /* column 1 - company name, shop owner info, shop address */
        $oPdfBlock->font($oPdfBlock->getFont(), '', 7);
        $oPdfBlock->text(InvoicepdfBlock::COLONE, $iLine, strip_tags($oShop->oxshops__oxcompany->getRawValue()));
        $iLine = $oPdfBlock->nextLine($iLine);
        $oPdfBlock->text(InvoicepdfBlock::COLONE, $iLine, strip_tags($oShop->oxshops__oxfname->getRawValue()) . ' ' . strip_tags($oShop->oxshops__oxlname->getRawValue()));
        $iLine = $oPdfBlock->nextLine($iLine);
        $oPdfBlock->text(InvoicepdfBlock::COLONE, $iLine, strip_tags($oShop->oxshops__oxstreet->getRawValue()));
        $iLine = $oPdfBlock->nextLine($iLine);
        $oPdfBlock->text(InvoicepdfBlock::COLONE, $iLine, strip_tags($oShop->oxshops__oxzip->value) . ' ' . strip_tags($oShop->oxshops__oxcity->getRawValue()));
        $iLine = $oPdfBlock->nextLine($iLine);
        $oPdfBlock->text(InvoicepdfBlock::COLONE, $iLine, strip_tags($oShop->oxshops__oxcountry->getRawValue()));

        /* column 2 - phone, fax, url, email address */
        $iLine = $iFooterStart;
        $oPdfBlock->text(InvoicepdfBlock::COLTWO, $iLine, $this->translate('ORDER_OVERVIEW_PDF_PHONE') . strip_tags($oShop->oxshops__oxtelefon->value));
        $iLine = $oPdfBlock->nextLine($iLine);
        $oPdfBlock->text(InvoicepdfBlock::COLTWO, $iLine, $this->translate('ORDER_OVERVIEW_PDF_FAX') . strip_tags($oShop->oxshops__oxtelefax->value));
        $iLine = $oPdfBlock->nextLine($iLine);
        $oPdfBlock->text(InvoicepdfBlock::COLTWO, $iLine, strip_tags($oShop->oxshops__oxurl->value));
        $iLine = $oPdfBlock->nextLine($iLine);
        $oPdfBlock->text(InvoicepdfBlock::COLTWO, $iLine, strip_tags($oShop->oxshops__oxorderemail->value));

        /* column 3 - bank information */
        $iLine = $iFooterStart;
        $oPdfBlock->text(InvoicepdfBlock::COLTHREE, $iLine, strip_tags($oShop->oxshops__oxbankname->getRawValue()));
        $iLine = $oPdfBlock->nextLine($iLine);
        $oPdfBlock->text(InvoicepdfBlock::COLTHREE, $iLine, $this->translate('ORDER_OVERVIEW_PDF_ACCOUNTNR') . strip_tags($oShop->oxshops__oxibannumber->value));
        $iLine = $oPdfBlock->nextLine($iLine);
        $oPdfBlock->text(InvoicepdfBlock::COLTHREE, $iLine, $this->translate('ORDER_OVERVIEW_PDF_BANKCODE') . strip_tags($oShop->oxshops__oxbiccode->value));
        $iLine = $oPdfBlock->nextLine($iLine);
        $oPdfBlock->text(InvoicepdfBlock::COLTHREE, $iLine, $this->translate('ORDER_OVERVIEW_PDF_VATID') . strip_tags($oShop->oxshops__oxvatnumber->value));

        if ( !empty($oShop->oxshops__oxtaxnumber->value) ) {
            $iLine = $oPdfBlock->nextLine($iLine);
            $oPdfBlock->text(InvoicepdfBlock::COLTHREE, $iLine, $this->translate('ORDER_OVERVIEW_PDF_TAXID') . strip_tags($oShop->oxshops__oxtaxnumber->value));
        }
        $oPdfBlock->run($oPdf);
    }

    /**
     * Adds shop logo to page header. Returns position for next texts in pdf.
     *
     * @param object $oPdf pdf document object
     *
     * @return int
     */
    public function pdfHeaderPlus($oPdf)
    {

        // new page with shop logo
        $this->pdfHeader($oPdf);

        $oPdfBlock = new InvoicepdfBlock($oPdf);
        // column names
        $oPdfBlock->font($oPdfBlock->getFont(), '', 8);
        $oPdfBlock->text(InvoicepdfBlock::LINEBEGIN, 50, $this->translate('ORDER_OVERVIEW_PDF_AMOUNT'));
        $oPdfBlock->text(30, 50, $this->translate('ORDER_OVERVIEW_PDF_ARTID'));
        $oPdfBlock->text(45, 50, $this->translate('ORDER_OVERVIEW_PDF_DESC'));
        $oPdfBlock->text(160, 50, $this->translate('ORDER_OVERVIEW_PDF_UNITPRICE'));
        $sText = $this->translate('ORDER_OVERVIEW_PDF_ALLPRICE');
        $oPdfBlock->text(InvoicepdfBlock::LINEEND - $oPdfBlock->getStringWidth($sText), 50, $sText);

        // line separator
        $oPdfBlock->line(InvoicepdfBlock::LINEBEGIN, 52, InvoicepdfBlock::LINEEND + 1, 52);

        $oPdfBlock->run($oPdf);

        return 56;
    }

    /**
     * Creating new page with shop logo. Returning position to continue data writing.
     *
     * @param object $oPdf pdf document object
     *
     * @return int
     */
    public function pdfHeader($oPdf)
    {
        // adding new page ...
        $oPdf->addPage();

        // loading active shop
        $oShop = $this->_getActShop();

        //logo
        $myConfig = $this->getConfig();
        $aSize = getimagesize($myConfig->getImageDir() . '/pdf_logo.jpg');
        $iMargin = InvoicepdfBlock::LINEEND - $aSize[0] * 0.2;
        $oPdf->setLink($oShop->oxshops__oxurl->value);
        $oPdf->image($myConfig->getImageDir() . '/pdf_logo.jpg', $iMargin, 10, $aSize[0] * 0.2, $aSize[1] * 0.2, '', $oShop->oxshops__oxurl->value);

        return 14 + $aSize[1] * 0.2;
    }

    /**
     * Creates the signature with the signature image.
     *
     * @param $oPdf
     * @param $iLine
     */
    public function pdfSignature($oPdf, $iLine){
        // loading active shop
        $oShop = $this->_getActShop();

        $myConfig = $this->getConfig();
        $aSize = getimagesize($myConfig->getImageDir() . '/owner_signature.jpg');
        $fConversion = $aSize[0] / $aSize[1];
        $iHeight = 16;
        $iWidth = $iHeight * $fConversion;

        $oPdf->image($myConfig->getImageDir() . '/owner_signature.jpg', InvoicepdfBlock::LINEBEGIN, $iLine, $iWidth);

        $iLine = $iLine + $iHeight;
        $oPdf->text(InvoicepdfBlock::LINEBEGIN, $iLine, $oShop->oxshops__oxfname->value . ' ' . $oShop->oxshops__oxlname->value);
    }

    /**
     * Generates order pdf report file.
     *
     * @param string $sFilename name of report file
     * @param int $iSelLang active language
     * @param bool $isDirect
     */
    public function genPdf($sFilename, $iSelLang = 0, $isDirect = false)
    {
        // setting pdf language
        $this->setSelectedLang($iSelLang);

        $blIsNewOrder = 0;
        // setting invoice number
        if (!$this->oxorder__oxbillnr->value) {
            //$this->oxorder__oxbillnr->setValue($this->getNextBillNum());
            $this->oxorder_oxbillnr = new oxField($this->getNextBillNum(), oxField::T_RAW);
            $blIsNewOrder = 1;
        }
        // setting invoice date
        if ($this->oxorder__oxbilldate->value == '0000-00-00') {
            $this->oxorder__oxbilldate->setValue(date('Y-m-d', mktime(0, 0, 0, date('m'), date('d'), date('Y'))));
            $blIsNewOrder = 1;
        }
        // saving order if new number or date
        if ($blIsNewOrder) {
            $this->save();
        }

        // initiating pdf engine
        if (oxRegistry::getConfig()->getConfigParam('blGeneratePdfInPdfaFormat')) {
            $oPdf = oxNew('invoiceoxpdf');
        } else {
            $oPdf = oxNew('oxPDF');
        }
        $oPdf->setPrintHeader(false);
        $oPdf->open();

        // Adding the CN22 formular for all foreign shipments, that were made with the International Mail shipping method
        $iSkipFirstPage = 0;
        if($this->_isForeignShipment() && ($this->oxorder__oxdeltype->value === 'SPS_INTPRI' || $this->oxorder__oxdeltype->value === 'SPS_INTECO')){
            $oPdf->addPage();
            $oCN22 = new cn22labelpdfblock($this, $oPdf);
            $oCN22->generate();
            $oCN22->run($oPdf);
            $iSkipFirstPage++;
        }

        // adding header
        $this->pdfHeader($oPdf);

        // adding info data
        switch (oxRegistry::getConfig()->getRequestParameter('pdftype')) {
            case 'dnote':
                $this->exportDeliveryNote($oPdf);
                break;
            default:
                $this->exportStandart($oPdf);
        }

        // adding footer
        $this->pdfFooter($oPdf);

        if($this->_isForeignShipment()){
            $iPageCount = $oPdf->getNumPages();
            $this->_duplicatePages($oPdf, 1 + $iSkipFirstPage, $iPageCount);
            $this->_duplicatePages($oPdf, 1 + $iSkipFirstPage, $iPageCount);
        }

        $this->_addPageToPdf($oPdf);

        // outputting file to browser
        if ($isDirect) {
            $oPdf->output($sFilename, 'F');
        } else {
            $oPdf->output($sFilename, 'I');
        }
    }

    /**
     * Page hook for other modules, that want to attach additional pages to the PDFs
     * @param $oPdf
     */
    protected function _addPageToPdf($oPdf){
        // stub
    }

    /**
     * Set billing address info to pdf.
     *
     * @param object $oPdf pdf document object
     * @return int|void
     */
    protected function _setBillingAddressToPdf($oPdf, $iLine)
    {
        $iBillingAddressStart = $iLine + 3;
        $oLang = oxRegistry::getLang();
        $sSal = $this->oxorder__oxbillsal->value;
        try {
            $sSal = $oLang->translateString($this->oxorder__oxbillsal->value, $this->getSelectedLang());
        } catch (Exception $e) {
        }
        $oPdfBlock = new InvoicepdfBlock($oPdf);
        $oPdfBlock->setLineHeight(1.0);
        $iLine = $iBillingAddressStart;
        $oPdfBlock->font($oPdfBlock->getFont(), '', 9);
        if($this->oxorder__oxbillcompany->value !== ''){
            $oPdfBlock->text(InvoicepdfBlock::LINEBEGIN, $iLine, $this->oxorder__oxbillcompany->getRawValue());
            $iLine = $oPdfBlock->nextLine($iLine);
        }
        $oPdfBlock->text(InvoicepdfBlock::LINEBEGIN, $iLine, $sSal . ' ' . $this->oxorder__oxbillfname->getRawValue() . ' ' . $this->oxorder__oxbilllname->getRawValue());
        $iLine = $oPdfBlock->nextLine($iLine);
        $oPdfBlock->text(InvoicepdfBlock::LINEBEGIN, $iLine, $this->oxorder__oxbillstreet->getRawValue() . ' ' . $this->oxorder__oxbillstreetnr->value);
        $oPdfBlock->font($oPdfBlock->getFont(), 'B', 9);
        $iLine = $oPdfBlock->nextLine($iLine);
        $oPdfBlock->text(InvoicepdfBlock::LINEBEGIN, $iLine, $this->oxorder__oxbillzip->value . ' ' . $this->oxorder__oxbillcity->getRawValue());
        $iLine = $oPdfBlock->nextLine($iLine);
        $oPdfBlock->font($oPdfBlock->getFont(), '', 9);
        $oPdfBlock->text(InvoicepdfBlock::LINEBEGIN, $iLine, $this->oxorder__oxbillcountry->getRawValue());

        if($this->_isForeignShipment() && class_exists('asign_ycaddfields_db')){
            $iLine = $oPdfBlock->nextLine($iLine,2);
            $sEoriFieldName = 'oxorder__' . asign_ycaddfields_db::EORI;
            $oPdfBlock->text(InvoicepdfBlock::LINEBEGIN, $iLine, "EORI: " . $this->$sEoriFieldName->getRawValue());
        }

        return $oPdfBlock->run($oPdf);
    }

    /**
     * Set delivery address info to pdf.
     *
     * @param object $oPdf pdf document object
     * @param int $iStart
     * @return int|void
     */
    protected function _setDeliveryAddressToPdf($oPdf, $iStart = InvoicepdfBlock::COLTHREE, $iLine)
    {
        $iDeliveryAddressStart = $iLine;
        $oLang = oxRegistry::getLang();
        $sSal = $this->oxorder__oxdelsal->value;
        try {
            $sSal = $oLang->translateString($this->oxorder__oxdelsal->value, $this->getSelectedLang());
        } catch (Exception $e) {
        }
        $oPdfBlock = new InvoicepdfBlock($oPdf);
        $oPdfBlock->setLineHeight(1.0);
        $iLine = $iDeliveryAddressStart;

        $oPdfBlock->font($oPdfBlock->getFont(), '', 9);

        if($iStart === InvoicepdfBlock::COLTHREE){
            $oPdfBlock->font($oPdfBlock->getFont(), '', 6);
            $oPdfBlock->text($iStart, $iLine, $this->translate('ORDER_OVERVIEW_PDF_DELIVERYADDRESS'));
            $oPdfBlock->font($oPdfBlock->getFont(), '', 9);
            $iLine = $oPdfBlock->nextLine($iLine);
        }

        if($this->oxorder__oxdelcompany->value !== ''){
            $oPdfBlock->text($iStart, $iLine, $this->oxorder__oxdelcompany->getRawValue());
            $iLine = $oPdfBlock->nextLine($iLine);
        }
        $oPdfBlock->text($iStart, $iLine, $sSal . ' ' . $this->oxorder__oxdellname->getRawValue() . ' ' . $this->oxorder__oxdelfname->getRawValue());
        $iLine = $oPdfBlock->nextLine($iLine);
        $oPdfBlock->text($iStart, $iLine, $this->oxorder__oxdelstreet->getRawValue() . ' ' . $this->oxorder__oxdelstreetnr->value);
        $oPdfBlock->font($oPdfBlock->getFont(), 'B', 9);
        $iLine = $oPdfBlock->nextLine($iLine);
        $oPdfBlock->text($iStart, $iLine, $this->oxorder__oxdelzip->value . ' ' . $this->oxorder__oxdelcity->getRawValue());
        $iLine = $oPdfBlock->nextLine($iLine);
        $oPdfBlock->font($oPdfBlock->getFont(), '', 9);
        $oPdfBlock->text($iStart, $iLine, $this->oxorder__oxdelcountry->getRawValue());

        return $oPdfBlock->run($oPdf);
    }

    /**
     * Set order articles info and articles VAT's to pdf.
     *
     * @param object $oPdf        pdf document object
     * @param int    &$iStartPos  text start position from top
     * @param bool   $blShowPrice show articles prices / VAT info or not
     */
    protected function _setOrderArticlesToPdf($oPdf, &$iStartPos, $blShowPrice = true)
    {
        if (!$this->_oArticles) {
            $this->_oArticles = $this->getOrderArticles(true);
        }

        $oCurr = $this->getCurrency();
        $oPdfBlock = new InvoicepdfBlock($oPdf);
        $oPdfBlock->setLineHeight(1.2);
        // product list
        $oPdfBlock->font($oPdfBlock->getFont(), '', 6);
        $iStartPos = $iStartPos - 3;
        foreach ($this->_oArticles as $key => $oOrderArt) {
            // starting a new page ...
            if ($iStartPos > 243) {
                $this->pdffooter($oPdf);
                $iStartPos = $this->pdfheaderplus($oPdf);
            } else {
                $iStartPos = $oPdfBlock->nextLine($iStartPos);
            }

            // sold amount
            $oPdfBlock->text(InvoicepdfBlock::LINEBEGIN + 2 - $oPdfBlock->getStringWidth($oOrderArt->oxorderarticles__oxamount->value), $iStartPos, $oOrderArt->oxorderarticles__oxamount->value);

            // product number
            $oPdfBlock->text(25, $iStartPos, $oOrderArt->oxorderarticles__oxartnum->value);

            // product title
            $oPdfBlock->text(40, $iStartPos, substr(strip_tags($this->_replaceExtendedChars($oOrderArt->oxorderarticles__oxtitle->getRawValue(), true)), 0, 40));

            if($this->_isForeignShipment() && class_exists('asign_ycaddfields_db')){
                $sCustomsFieldName = 'oxorderarticles__' . asign_ycaddfields_db::CUSTOMS;
                $sTaraFieldName = 'oxorderarticles__' . asign_ycaddfields_db::TARA;
                $sOriginFieldName = 'oxorderarticles__' . asign_ycaddfields_db::ORIGIN;
                $oCountry = oxNew('oxcountry');
                $oCountry->loadInLang($this->getSelectedLang(), $oOrderArt->$sOriginFieldName->value);
                $oPdfBlock->text(110, $iStartPos, $oOrderArt->$sCustomsFieldName->value);
                $oPdfBlock->text(125, $iStartPos, $oOrderArt->oxorderarticles__oxweight->value - $oOrderArt->$sTaraFieldName->value . "kg");
                $oPdfBlock->text(145, $iStartPos, $oCountry->oxcountry__oxisoalpha2->value);
            }

            if ($blShowPrice) {
                $oLang = oxRegistry::getLang();

                // product VAT percent
                //$oPdfBlock->text(157 - $oPdfBlock->getStringWidth($oOrderArt->oxorderarticles__oxvat->value), $iStartPos, $oOrderArt->oxorderarticles__oxvat->value);

                // product price

                $dUnitPrice = ($this->isNettoMode()) ? $oOrderArt->oxorderarticles__oxnprice->value : $oOrderArt->oxorderarticles__oxbprice->value;
                $dTotalPrice = ($this->isNettoMode()) ? $oOrderArt->oxorderarticles__oxnetprice->value : $oOrderArt->oxorderarticles__oxbrutprice->value;

                $sText = $oLang->formatCurrency($dUnitPrice, $this->_oCur) . ' ' . $this->_oCur->name;
                $oPdfBlock->text(175 - $oPdfBlock->getStringWidth($sText), $iStartPos, $sText);

                // total product price
                $sText = $oLang->formatCurrency($dTotalPrice, $this->_oCur) . ' ' . $this->_oCur->name;
                $oPdfBlock->text(InvoicepdfBlock::LINEEND - $oPdfBlock->getStringWidth($sText), $iStartPos, $sText);

            }

            // additional variant info
            if ($oOrderArt->oxorderarticles__oxselvariant->value) {
                $iStartPos = $oPdfBlock->nextLine($iStartPos);
                $oPdfBlock->text(45, $iStartPos, substr($oOrderArt->oxorderarticles__oxselvariant->value, 0, 58));
            }
        }
        $oPdfBlock->run($oPdf);
    }

    /**
     * Checks if the order is going to a country that is not defined as one of the home countries of the shop
     *
     * @return bool
     */
    protected function _isForeignShipment(){
        $sDelCountry = ($this->oxorder__oxdelcountryid->value != '' ? $this->oxorder__oxdelcountryid->value : $this->oxorder__oxbillcountryid->value);

        return !in_array($sDelCountry, $this->getConfig()->getShopConfVar('aHomeCountry'));
    }

    protected function _generateShopAddressHeader(InvoicepdfBlock $oPdfBlock, $iPDFStart){
        // loading active shop
        $oShop = $this->_getActShop();
        $iClearViewLimit = 90;
        $iLine = $iPDFStart;

        // shop information
        $sShopCityName = $oShop->oxshops__oxcity->getRawValue();
        if($this->getConfig()->getConfigParam('blAddPPFrankate')){
            $iCityLength = $oPdfBlock->getStringWidth($sShopCityName);
            $oPdfBlock->setLineHeight(0.9);
            $iBoxLength = 50;
            $iLineStart = $iPDFStart;
            $oPdfBlock->font($oPdfBlock->getFont(), '', 10);
            $oPdfBlock->line(InvoicepdfBlock::LINEBEGIN, $iPDFStart, $iBoxLength, $iPDFStart);
            $oPdfBlock->font($oPdfBlock->getFont(), 'B', 13);
            $oPdfBlock->text($oPdfBlock::LINEBEGIN, $iLineStart, 'P.P.');
            $iPPWidth = $oPdfBlock->getStringWidth('P.P.');
            $oPdfBlock->font($oPdfBlock->getFont(), '', 6);
            $iSenderColumn = $oPdfBlock::LINEBEGIN + $iPPWidth + 6;
            $oPdfBlock->text($iSenderColumn, $iLine, 'CH-' . $oShop->oxshops__oxzip->value);
            $oPdfBlock->text($oPdfBlock->alignRightToColumn($iClearViewLimit, 'Post CH AG'), $iLine, 'Post CH AG');
            $iLine = $oPdfBlock->nextLine($iLine);
            $oPdfBlock->text($iSenderColumn, $iLine, $sShopCityName);
            $sFrankateNr = $this->getConfig()->getConfigParam('sFrankateNr');
            $oPdfBlock->text($oPdfBlock->alignRightToColumn($iClearViewLimit, $sFrankateNr), $iLine, $sFrankateNr);
            $iLine = $oPdfBlock->nextLine($iLine);
            $iContentLength = $iPPWidth + $iCityLength + 6;
            $iBoxLength = ($iContentLength + 6 > $iBoxLength)? $iContentLength: $iBoxLength;

            $oPdfBlock->line(InvoicepdfBlock::LINEBEGIN, $iLineStart, InvoicepdfBlock::LINEBEGIN - 2, $iLine +1 ); // left line
            $oPdfBlock->line(InvoicepdfBlock::LINEBEGIN, $iLine +1, $iBoxLength, $iLine +1); // bottom line
            $oPdfBlock->line($iBoxLength + 2, $iLineStart, $iBoxLength, $iLine +1); // right line
            $oPdfBlock->line(InvoicepdfBlock::LINEBEGIN, $iLine +2, $iClearViewLimit, $iLine + 2); // bottom line
        } else {
            $oPdfBlock->font($oPdfBlock->getFont(), 'U', 6);
            $oPdfBlock->text(InvoicepdfBlock::LINEBEGIN, $iPDFStart, $oShop->oxshops__oxname->getRawValue() . ' - ' . $oShop->oxshops__oxstreet->getRawValue() . ' - ' . $oShop->oxshops__oxzip->value . ' - ' . $oShop->oxshops__oxcity->getRawValue());
        }

        $iLine = $oPdfBlock->nextLine($iLine);

        return $iLine;
    }

    /**
     * Exporting standard invoice pdf
     *
     * @param object $oPdf pdf document object
     */
    public function exportStandart($oPdf)
    {
        $oShop = $this->_getActShop();

        // preparing order curency info
        $myConfig = $this->getConfig();
        $oPdfBlock = new InvoicepdfBlock($oPdf);
        $iPDFStart = 50;
        $iLine = $iPDFStart;

        $this->_oCur = $myConfig->getCurrencyObject($this->oxorder__oxcurrency->value);
        if (!$this->_oCur) {
            $this->_oCur = $myConfig->getActShopCurrencyObject();
        }

        $iLine = $this->_generateShopAddressHeader($oPdfBlock, $iPDFStart);

        // billing address
        $iBillLines = $this->_setBillingAddressToPdf($oPdf, $iLine);

        // delivery address
        $iDeliveryLines = 0;
        if ($this->oxorder__oxdelsal->value) {
            $iDeliveryLines = $this->_setDeliveryAddressToPdf($oPdf, $oPdfBlock::COLTHREE, $iLine);
        }

        // loading user
        $oUser = oxNew('oxuser');
        $oUser->load($this->oxorder__oxuserid->value);

        // user info
        $sText = $this->translate('ORDER_OVERVIEW_PDF_FILLONPAYMENT');
        $oPdfBlock->font($oPdfBlock->getFont(), '', 5);
        $oPdfBlock->text(InvoicepdfBlock::LINEEND - $oPdfBlock->getStringWidth($sText), $iPDFStart, $sText);

        // customer number
        $sCustNr = $this->translate('ORDER_OVERVIEW_PDF_CUSTNR') . ' ' . $oUser->oxuser__oxcustnr->value;
        $oPdfBlock->font($oPdfBlock->getFont(), '', 7);
        $iLine = $oPdfBlock->nextLine($iLine);
        $oPdfBlock->text(InvoicepdfBlock::LINEEND - $oPdfBlock->getStringWidth($sCustNr), $iLine, $sCustNr);

        $oPdfBlock->setLineHeight(1.2);

        // setting position if delivery address is used
        $iLineBelowAddress = ($iDeliveryLines > $iBillLines) ? $iDeliveryLines + 1 : $iBillLines;
        $iLine = $oPdfBlock->nextLine($iLine, $iLineBelowAddress + 2);

        // shop city
        $sText = $oShop->oxshops__oxcity->getRawValue() . ', ' . date('d.m.Y', strtotime($this->oxorder__oxbilldate->value));
        $oPdfBlock->font($oPdfBlock->getFont(), '', 10);
        $oPdfBlock->text(InvoicepdfBlock::LINEEND - $oPdfBlock->getStringWidth($sText), $iLine, $sText);

        if($this->_isForeignShipment()){
            $oPdfBlock->font($oPdfBlock->getFont(), 'B', 12);
            $iLine = $oPdfBlock->nextLine($iLine);
            $oPdfBlock->text(InvoicepdfBlock::LINEBEGIN, $iLine, $this->translate('ORDER_OVERVIEW_PDF_INVOICE'));
            $oPdfBlock->font($oPdfBlock->getFont(), '', 10);
        }

        // shop VAT number
        if ($oShop->oxshops__oxvatnumber->value) {
            $iLine = $oPdfBlock->nextLine($iLine);
            $sText = $this->translate('ORDER_OVERVIEW_PDF_TAXIDNR') . ' ' . $oShop->oxshops__oxvatnumber->value;
            $oPdfBlock->text(InvoicepdfBlock::LINEEND - $oPdfBlock->getStringWidth($sText), $iLine, $sText);
        } else {
            $iLine = $oPdfBlock->nextLine($iLine);
        }

        // invoice number
        $iLine = $oPdfBlock->nextLine($iLine);
        $sText = $this->translate('ORDER_OVERVIEW_PDF_COUNTNR') . ' ' . $this->oxorder__oxbillnr->value;
        $oPdfBlock->text(InvoicepdfBlock::LINEEND - $oPdfBlock->getStringWidth($sText), $iLine, $sText);

        // marking if order is canceled
        if ($this->oxorder__oxstorno->value == 1) {
            $this->oxorder__oxordernr->setValue($this->oxorder__oxordernr->getRawValue() . '   ' . $this->translate('ORDER_OVERVIEW_PDF_STORNO'), oxField::T_RAW);
        }

        // order number
        $oPdfBlock->font($oPdfBlock->getFont(), '', 12);
        $oPdfBlock->text(InvoicepdfBlock::LINEBEGIN, $iLine, $this->translate('ORDER_OVERVIEW_PDF_PURCHASENR') . ' ' . $this->oxorder__oxordernr->value);

        // order date
        $oPdfBlock->font($oPdfBlock->getFont(), '', 10);
        $iLine = $oPdfBlock->nextLine($iLine);
        $aOrderDate = explode(' ', $this->oxorder__oxorderdate->value);
        $sOrderDate = oxRegistry::get("oxUtilsDate")->formatDBDate($aOrderDate[0]);
        $oPdfBlock->text(InvoicepdfBlock::LINEBEGIN, $iLine, $this->translate('ORDER_OVERVIEW_PDF_ORDERSFROM') . $sOrderDate . $this->translate('ORDER_OVERVIEW_PDF_ORDERSAT') . $oShop->oxshops__oxurl->value);
        $iLine = $oPdfBlock->nextLine($iLine);
        $iLine = $oPdfBlock->nextLine($iLine);

        // product info header
        $oPdfBlock->font($oPdfBlock->getFont(), '', 6);
        $oPdfBlock->text(InvoicepdfBlock::LINEBEGIN, $iLine, $this->translate('ORDER_OVERVIEW_PDF_AMOUNT'));
        $oPdfBlock->text(25, $iLine, $this->translate('ORDER_OVERVIEW_PDF_ARTID'));
        $oPdfBlock->text(40, $iLine, $this->translate('ORDER_OVERVIEW_PDF_DESC'));

        if($this->_isForeignShipment()){
            $oPdfBlock->text(110, $iLine, $this->translate('ORDER_OVERVIEW_PDF_CUSTOMSTARIFF'));
            $oPdfBlock->text(125, $iLine, $this->translate('ORDER_OVERVIEW_PDF_NETTOWEIGHT'));
            $oPdfBlock->text(145, $iLine, $this->translate('ORDER_OVERVIEW_PDF_COUNTRYORIGIN'));
        }

        //$oPdfBlock->text(155, $iTop, $this->translate('ORDER_OVERVIEW_PDF_VAT'));
        $oPdfBlock->text(165, $iLine, $this->translate('ORDER_OVERVIEW_PDF_UNITPRICE'));
        $sText = $this->translate('ORDER_OVERVIEW_PDF_ALLPRICE');
        $oPdfBlock->text(InvoicepdfBlock::LINEEND - $oPdfBlock->getStringWidth($sText), $iLine, $sText);

        // separator line
        $iLine = $oPdfBlock->nextLine($iLine);
        $oPdfBlock->line(InvoicepdfBlock::LINEBEGIN, $iLine, InvoicepdfBlock::LINEEND, $iLine);

        // #345
        $siteH = $iLine;
        $oPdfBlock->font($oPdfBlock->getFont(), '', 8);

        // order articles
        $this->_setOrderArticlesToPdf($oPdf, $siteH, true);

        // generating pdf file
        $oArtSumm = new InvoicepdfArticleSummary($this, $oPdf);
        $iHeight = $oArtSumm->generate($siteH);
        if ($siteH + $iHeight > 258) {
            $this->pdfFooter($oPdf);
            $iTop = $this->pdfHeader($oPdf);
            $oArtSumm->ajustHeight($iTop - $siteH);
            $siteH = $iTop;
        }

        $oArtSumm->run($oPdf);
        $siteH += $iHeight + 8;

        $oPdfBlock->text(InvoicepdfBlock::LINEBEGIN, $siteH, $this->translate('ORDER_OVERVIEW_PDF_GREETINGS'));
        $siteH = $oPdfBlock->nextLine($siteH);
        $siteH = $oPdfBlock->nextLine($siteH);
        $oPdfBlock->run($oPdf);

        if($this->_isForeignShipment()){
            $this->pdfSignature($oPdf, $siteH);
        }
    }

    /**
     * Duplicates the pages in the PDF itself, adds them to the end of the document
     *
     * @param $oPdf
     * @param int $iStart Page Index (starts at 1)
     * @param int $iEnd Page Index
     */
    protected function _duplicatePages($oPdf, $iStart, $iEnd){
        for($iIndex = $iStart; $iIndex <= $iEnd; $iIndex++){
            $oPdf->copyPage($iIndex);
        }
    }

    /**
     * Generating delivery note pdf.
     *
     * @param object $oPdf pdf document object
     */
    public function exportDeliveryNote($oPdf)
    {
        $iPDFStart = 50;
        $iLine = $iPDFStart;
        $myConfig = $this->getConfig();
        $oShop = $this->_getActShop();
        $oPdfBlock = new InvoicepdfBlock($oPdf);
        $oPdfBlock->font($oPdfBlock->getFont(), '', 8);
        $oLang = oxRegistry::getLang();
        $sSal = $this->oxorder__oxdelsal->value;
        try {
            $sSal = $oLang->translateString($this->oxorder__oxdelsal->value, $this->getSelectedLang());
        } catch (Exception $e) {
        }

        // loading order currency info
        $this->_oCur = $myConfig->getCurrencyObject($this->oxorder__oxcurrency->value);
        if (!isset($this->_oCur)) {
            $this->_oCur = $myConfig->getActShopCurrencyObject();
        }

        $iLine = $this->_generateShopAddressHeader($oPdfBlock, $iPDFStart);

        if ($this->oxorder__oxdelsal->value) {
            $iAddressLines = $this->_setDeliveryAddressToPdf($oPdf, InvoicepdfBlock::LINEBEGIN, $iLine);
        } else {
            $iAddressLines = $this->_setBillingAddressToPdf($oPdf, $iLine);
        }
        $iLine = $oPdfBlock->nextLine($iLine, $iAddressLines + 4);

        // loading user info
        $oUser = oxNew('oxuser');
        $oUser->load($this->oxorder__oxuserid->value);

        // user info
        $sText = $this->translate('ORDER_OVERVIEW_PDF_FILLONPAYMENT');
        $oPdfBlock->font($oPdfBlock->getFont(), '', 5);
        $oPdfBlock->text(InvoicepdfBlock::LINEEND - $oPdfBlock->getStringWidth($sText), $iPDFStart, $sText);

        // customer number
        $sCustNr = $this->translate('ORDER_OVERVIEW_PDF_CUSTNR') . ' ' . $oUser->oxuser__oxcustnr->value;
        $oPdfBlock->font($oPdfBlock->getFont(), '', 7);
        $oPdfBlock->text(InvoicepdfBlock::LINEEND - $oPdfBlock->getStringWidth($sCustNr), $oPdfBlock->nextLine($iPDFStart), $sCustNr);

        // shops city
        $sText = $oShop->oxshops__oxcity->getRawValue() . ', ' . date('d.m.Y');
        $oPdfBlock->font($oPdfBlock->getFont(), '', 10);
        $oPdfBlock->text(InvoicepdfBlock::LINEEND - $oPdfBlock->getStringWidth($sText), $iLine, $sText);
        $iLine = $oPdfBlock->nextLine($iLine);

        // shop VAT number
        if ($oShop->oxshops__oxvatnumber->value) {
            $sText = $this->translate('ORDER_OVERVIEW_PDF_TAXIDNR') . ' ' . $oShop->oxshops__oxvatnumber->value;
            $oPdfBlock->text(InvoicepdfBlock::LINEEND - $oPdfBlock->getStringWidth($sText), $iLine, $sText);
            $iLine = $oPdfBlock->nextLine($iLine);
        }

        // invoice number
        $sText = $this->translate('ORDER_OVERVIEW_PDF_COUNTNR') . ' ' . $this->oxorder__oxbillnr->value;
        $oPdfBlock->text(InvoicepdfBlock::LINEEND - $oPdfBlock->getStringWidth($sText), $iLine, $sText);
        $iLine = $oPdfBlock->nextLine($iLine);

        // canceled order marker
        if ($this->oxorder__oxstorno->value == 1) {
            $this->oxorder__oxordernr->setValue($this->oxorder__oxordernr->getRawValue() . '   ' . $this->translate('ORDER_OVERVIEW_PDF_STORNO'), oxField::T_RAW);
        }

        // order number
        $oPdfBlock->font($oPdfBlock->getFont(), '', 12);
        $oPdfBlock->text(InvoicepdfBlock::LINEBEGIN, $iLine, $this->translate('ORDER_OVERVIEW_PDF_DELIVNOTE') . ' ' . $this->oxorder__oxordernr->value);
        $iLine = $oPdfBlock->nextLine($iLine);

        // order date
        $aOrderDate = explode(' ', $this->oxorder__oxorderdate->value);
        $sOrderDate = oxRegistry::get("oxUtilsDate")->formatDBDate($aOrderDate[0]);
        $oPdfBlock->font($oPdfBlock->getFont(), '', 10);
        $oPdfBlock->text(InvoicepdfBlock::LINEBEGIN, $iLine, $this->translate('ORDER_OVERVIEW_PDF_ORDERSFROM') . $sOrderDate . $this->translate('ORDER_OVERVIEW_PDF_ORDERSAT') . $oShop->oxshops__oxurl->value);
        $iLine = $oPdfBlock->nextLine($iLine, 2);

        // product info header
        $oPdfBlock->font($oPdfBlock->getFont(), '', 8);
        $oPdfBlock->text(InvoicepdfBlock::LINEBEGIN, $iLine, $this->translate('ORDER_OVERVIEW_PDF_AMOUNT'));
        $oPdfBlock->text(30, $iLine, $this->translate('ORDER_OVERVIEW_PDF_ARTID'));
        $oPdfBlock->text(45, $iLine, $this->translate('ORDER_OVERVIEW_PDF_DESC'));
        $iLine = $oPdfBlock->nextLine($iLine);

        // line separator
        $oPdf->line(InvoicepdfBlock::LINEBEGIN, $iLine, InvoicepdfBlock::LINEEND, $iLine);
        // product list
        // order articles
        $this->_setOrderArticlesToPdf($oPdf, $iLine, false);
        $iLine = $oPdfBlock->nextLine($iLine);

        // sine separator
        $oPdf->line(InvoicepdfBlock::LINEBEGIN, $iLine, InvoicepdfBlock::LINEEND, $iLine);
        $iLine = $oPdfBlock->nextLine($iLine);

        $oPdfBlock->run($oPdf);
    }

    /**
     * Replaces some special characters to HTML compatible symbol codes.
     * SWITCHED OFF NOW ( 2.2 )
     *
     * @param string $sValue    initial value
     * @param bool   $blReverse (default false) if false - checks if we do have already htmlentities inside
     *
     * @return string
     */
    protected function _replaceExtendedChars($sValue, $blReverse = false)
    {
        // we need to replace this for compatibility with XHTML
        // as this function causes a lot of trouble with editor
        // we switch it off, even if this means that fields do not validate through xhtml
        // return $sValue;

        // we need to replace this for compatibility with XHTML
        $aReplace = array(chr(169) => "&copy;", chr(128) => "&euro;", "\"" => "&quot;", "'" => "&#039;");

        // #899C reverse html entities and references transformation is used in invoicepdf module
        // so this part must be enabled. Now it works with html references like &#123;
        if ($blReverse) {
            // replace now
            if (version_compare(PHP_VERSION, '5.3.4') >= 0) {
                $aTransTbl = get_html_translation_table(HTML_ENTITIES, ENT_COMPAT, 'ISO-8859-1');
            } else {
                $aTransTbl = get_html_translation_table(HTML_ENTITIES, ENT_COMPAT);
            }

            $aTransTbl = array_flip($aTransTbl) + array_flip($aReplace);
            $sValue = strtr($sValue, $aTransTbl);
            $sValue = getStr()->preg_replace('/\&\#([0-9]+)\;/me', "chr('\\1')", $sValue);
        }

        return $sValue;
    }

    /**
     * Returns order articles VATS's.
     *
     * @return array
     */
    public function getVats()
    {
        // for older orders
        return $this->getProductVats(false);
    }

    /**
     * Returns order currency object.
     *
     * @return object
     */
    public function getCurrency()
    {
        return $this->_oCur;
    }

    /**
     * Returns order currency object.
     *
     * @return object
     */
    public function getSelectedLang()
    {
        return $this->_iSelectedLang;
    }

    /**
     * Method returns config param iPaymentTerm, default value is 7;
     *
     * @return int
     */
    public function getPaymentTerm()
    {
        if (null === $iPaymentTerm = $this->getConfig()->getConfigParam('iPaymentTerm')) {
            $iPaymentTerm = 7;
        }

        return $iPaymentTerm;
    }
}