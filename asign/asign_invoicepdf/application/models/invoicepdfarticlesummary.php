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
 * Order summary class
 */
class InvoicepdfArticleSummary extends InvoicepdfBlock
{

    /**
     * order object
     *
     * @var object
     */
    protected $_oData = null;

    /**
     * pdf object
     *
     * @var object
     */


    /**
     * Constructor
     *
     * @param object $oData order object
     * @param object $oPdf  pdf object
     */
    public function __construct($oData, $oPdf)
    {
        $this->_oData = $oData;
        parent::__construct($oPdf);
    }

    /**
     * Sets total costs values using order without discount.
     *
     * @param int &$iStartPos text start position
     */
    protected function _setTotalCostsWithoutDiscount(&$iStartPos)
    {
        $oLang = oxRegistry::getLang();

        $sNetSum = $oLang->formatCurrency($this->_oData->oxorder__oxtotalnetsum->value, $this->_oData->getCurrency()) . ' ' . $this->_oData->getCurrency()->name;
        $this->text(self::SUMMARYBEGIN, $iStartPos, $this->_oData->translate('ORDER_OVERVIEW_PDF_ALLPRICENETTO'));
        $this->text(self::LINEEND - $this->getStringWidth($sNetSum), $iStartPos, $sNetSum);

        // #345 - product VAT info
        $iCtr = 1;
        foreach ($this->_oData->getVats() as $iVat => $dVatPrice) {
            $iStartPos = $this->nextLine($iStartPos);
            $sVATSum = $oLang->formatCurrency($dVatPrice, $this->_oData->getCurrency()) . ' ' . $this->_oData->getCurrency()->name;
            $this->text(self::SUMMARYBEGIN, $iStartPos, $this->_oData->translate('ORDER_OVERVIEW_PDF_ZZGLVAT') . $iVat . $this->_oData->translate('ORDER_OVERVIEW_PDF_PERCENTSUM'));
            $this->text(self::LINEEND - $this->getStringWidth($sVATSum), $iStartPos, $sVATSum);
            $iCtr++;
        }
        $iStartPos = $this->nextLine($iStartPos);

        // products brutto price
        $sBrutPrice = $oLang->formatCurrency($this->_oData->oxorder__oxtotalbrutsum->value, $this->_oData->getCurrency()) . ' ' . $this->_oData->getCurrency()->name;
        $this->text(self::SUMMARYBEGIN, $iStartPos, $this->_oData->translate('ORDER_OVERVIEW_PDF_ALLPRICEBRUTTO'));
        $this->text(self::LINEEND - $this->getStringWidth($sBrutPrice), $iStartPos, $sBrutPrice);

        $iStartPos = $this->nextLine($iStartPos);
    }


    /**
     * Sets total costs values using order with discount.
     *
     * @param int &$iStartPos text start position
     */
    protected function _setTotalCostsWithDiscount(&$iStartPos)
    {
        $oLang = oxRegistry::getLang();

        if ($this->_oData->isNettoMode()) {

            // products netto price
            $sNetSum = $oLang->formatCurrency($this->_oData->oxorder__oxtotalnetsum->value, $this->_oData->getCurrency()) . ' ' . $this->_oData->getCurrency()->name;
            $this->text(self::SUMMARYBEGIN, $iStartPos, $this->_oData->translate('ORDER_OVERVIEW_PDF_ALLPRICENETTO'));
            $this->text(self::LINEEND - $this->getStringWidth($sNetSum), $iStartPos, $sNetSum);

            $iStartPos = $this->nextLine($iStartPos);

            // discount
            $dDiscountVal = $this->_oData->oxorder__oxdiscount->value;
            if ($dDiscountVal > 0) {
                $dDiscountVal *= -1;
            }
            $sDiscount = $oLang->formatCurrency($dDiscountVal, $this->_oData->getCurrency()) . ' ' . $this->_oData->getCurrency()->name;
            $this->text(self::SUMMARYBEGIN, $iStartPos, $this->_oData->translate('ORDER_OVERVIEW_PDF_DISCOUNT'));
            $this->text(self::LINEEND - $this->getStringWidth($sDiscount), $iStartPos, $sDiscount);

            foreach ($this->_oData->getVats() as $iVat => $dVatPrice) {
                $iStartPos = $this->nextLine($iStartPos);
                $sVATSum = $oLang->formatCurrency($dVatPrice, $this->_oData->getCurrency()) . ' ' . $this->_oData->getCurrency()->name;
                $this->text(self::SUMMARYBEGIN, $iStartPos, $this->_oData->translate('ORDER_OVERVIEW_PDF_ZZGLVAT') . $iVat . $this->_oData->translate('ORDER_OVERVIEW_PDF_PERCENTSUM'));
                $this->text(self::LINEEND - $this->getStringWidth($sVATSum), $iStartPos, $sVATSum);
            }

            $iStartPos = $this->nextLine($iStartPos);

            // products brutto price
            $sBrutPrice = $oLang->formatCurrency($this->_oData->oxorder__oxtotalbrutsum->value, $this->_oData->getCurrency()) . ' ' . $this->_oData->getCurrency()->name;
            $this->text(self::SUMMARYBEGIN, $iStartPos, $this->_oData->translate('ORDER_OVERVIEW_PDF_ALLPRICEBRUTTO'));
            $this->text(self::LINEEND - $this->getStringWidth($sBrutPrice), $iStartPos, $sBrutPrice);

        } else {
            // products brutto price
            $sBrutPrice = $oLang->formatCurrency($this->_oData->oxorder__oxtotalbrutsum->value, $this->_oData->getCurrency()) . ' ' . $this->_oData->getCurrency()->name;
            $this->text(self::SUMMARYBEGIN, $iStartPos, $this->_oData->translate('ORDER_OVERVIEW_PDF_ALLPRICEBRUTTO'));
            $this->text(self::LINEEND - $this->getStringWidth($sBrutPrice), $iStartPos, $sBrutPrice);

            $iStartPos = $this->nextLine($iStartPos);

            // discount
            $dDiscountVal = $this->_oData->oxorder__oxdiscount->value;
            if ($dDiscountVal > 0) {
                $dDiscountVal *= -1;
            }
            $sDiscount = $oLang->formatCurrency($dDiscountVal, $this->_oData->getCurrency()) . ' ' . $this->_oData->getCurrency()->name;
            $this->text(self::SUMMARYBEGIN, $iStartPos, $this->_oData->translate('ORDER_OVERVIEW_PDF_DISCOUNT'));
            $this->text(self::LINEEND - $this->getStringWidth($sDiscount), $iStartPos, $sDiscount);

            $iStartPos = $this->nextLine($iStartPos);

            // products netto price
            $sNetSum = $oLang->formatCurrency($this->_oData->oxorder__oxtotalnetsum->value, $this->_oData->getCurrency()) . ' ' . $this->_oData->getCurrency()->name;
            $this->text(self::SUMMARYBEGIN, $iStartPos, $this->_oData->translate('ORDER_OVERVIEW_PDF_ALLPRICENETTO'));
            $this->text(self::LINEEND - $this->getStringWidth($sNetSum), $iStartPos, $sNetSum);

            // #345 - product VAT info
            foreach ($this->_oData->getVats() as $iVat => $dVatPrice) {
                $iStartPos = $this->nextLine($iStartPos);
                $sVATSum = $oLang->formatCurrency($dVatPrice, $this->_oData->getCurrency()) . ' ' . $this->_oData->getCurrency()->name;
                $this->text(self::SUMMARYBEGIN, $iStartPos, $this->_oData->translate('ORDER_OVERVIEW_PDF_ZZGLVAT') . $iVat . $this->_oData->translate('ORDER_OVERVIEW_PDF_PERCENTSUM'));
                $this->text(self::LINEEND - $this->getStringWidth($sVATSum), $iStartPos, $sVATSum);
            }
        }
        $iStartPos = $this->nextLine($iStartPos);

    }

    /**
     * Sets voucher values to pdf.
     *
     * @param int &$iStartPos text start position
     */
    protected function _setVoucherInfo(&$iStartPos)
    {
        $dVoucher = 0;
        if ($this->_oData->oxorder__oxvoucherdiscount->value) {
            $dDiscountVal = $this->_oData->oxorder__oxvoucherdiscount->value;
            if ($dDiscountVal > 0) {
                $dDiscountVal *= -1;
            }
            $sPayCost = oxRegistry::getLang()->formatCurrency($dDiscountVal, $this->_oData->getCurrency()) . ' ' . $this->_oData->getCurrency()->name;
            $this->text(self::SUMMARYBEGIN, $iStartPos, $this->_oData->translate('ORDER_OVERVIEW_PDF_VOUCHER'));
            $this->text(self::LINEEND - $this->getStringWidth($sPayCost), $iStartPos, $sPayCost);
            $iStartPos = $this->nextLine($iStartPos);
        }
    }

    /**
     * Sets delivery info to pdf.
     *
     * @param int &$iStartPos text start position
     */
    protected function _setDeliveryInfo(&$iStartPos)
    {
        $sAddString = '';
        $oLang = oxRegistry::getLang();
        $oConfig = oxRegistry::getConfig();
        if ($oConfig->getConfigParam('blShowVATForDelivery')) {
            // delivery netto
            $sDelCostNetto = $oLang->formatCurrency($this->_oData->getOrderDeliveryPrice()->getNettoPrice(), $this->_oData->getCurrency()) . ' ' . $this->_oData->getCurrency()->name;
            $this->text(self::SUMMARYBEGIN, $iStartPos, $this->_oData->translate('ORDER_OVERVIEW_PDF_SHIPCOST') . ' ' . $this->_oData->translate('ORDER_OVERVIEW_PDF_NETTO'));
            $this->text(self::LINEEND - $this->getStringWidth($sDelCostNetto), $iStartPos, $sDelCostNetto);
            $iStartPos = $this->nextLine($iStartPos);

            if ($oConfig->getConfigParam('sAdditionalServVATCalcMethod') != 'proportional') {
                $sVatValueText = $this->_oData->translate('ORDER_OVERVIEW_PDF_ZZGLVAT') . $this->_oData->oxorder__oxdelvat->value . $this->_oData->translate('ORDER_OVERVIEW_PDF_PERCENTSUM');
            } else {
                $sVatValueText = $this->_oData->translate('TOTAL_PLUS_PROPORTIONAL_VAT');
            }

            // delivery VAT
            $sDelCostVAT = $oLang->formatCurrency($this->_oData->getOrderDeliveryPrice()->getVATValue(), $this->_oData->getCurrency()) . ' ' . $this->_oData->getCurrency()->name;
            $this->text(self::SUMMARYBEGIN, $iStartPos, $sVatValueText);
            $this->text(self::LINEEND - $this->getStringWidth($sDelCostVAT), $iStartPos, $sDelCostVAT);

            $sAddString = ' ' . $this->_oData->translate('ORDER_OVERVIEW_PDF_BRUTTO');
            $iStartPos = $this->nextLine($iStartPos);

        } else {
            // if canceled order, reset value
            if ($this->_oData->oxorder__oxstorno->value) {
                $this->_oData->oxorder__oxdelcost->setValue(0);
            }
            $sDelCost = $oLang->formatCurrency($this->_oData->oxorder__oxdelcost->value, $this->_oData->getCurrency()) . ' ' . $this->_oData->getCurrency()->name;
            $this->text(self::SUMMARYBEGIN, $iStartPos, $this->_oData->translate('ORDER_OVERVIEW_PDF_SHIPCOST') . $sAddString);
            $this->text(self::LINEEND - $this->getStringWidth($sDelCost), $iStartPos, $sDelCost);
            $iStartPos = $this->nextLine($iStartPos);
        }
    }

    /**
     * Sets wrapping info to pdf.
     *
     * @param int &$iStartPos text start position
     */
    protected function _setWrappingInfo(&$iStartPos)
    {
        if ($this->_oData->oxorder__oxwrapcost->value || $this->_oData->oxorder__oxgiftcardcost->value) {
            $oLang = oxRegistry::getLang();
            $oConfig = oxRegistry::getConfig();

            //displaying wrapping VAT info
            if ($oConfig->getConfigParam('blShowVATForWrapping')) {

                if ($this->_oData->oxorder__oxwrapcost->value) {
                    // wrapping netto
                    $sWrapCostNetto = $oLang->formatCurrency($this->_oData->getOrderWrappingPrice()->getNettoPrice(), $this->_oData->getCurrency()) . ' ' . $this->_oData->getCurrency()->name;
                    $this->text(self::SUMMARYBEGIN, $iStartPos, $this->_oData->translate('WRAPPING_COSTS') . ' ' . $this->_oData->translate('ORDER_OVERVIEW_PDF_NETTO'));
                    $this->text(self::LINEEND - $this->getStringWidth($sWrapCostNetto), $iStartPos, $sWrapCostNetto);

                    //wrapping VAT
                    $iStartPos += 4;
                    $sWrapCostVAT = $oLang->formatCurrency($this->_oData->getOrderWrappingPrice()->getVATValue(), $this->_oData->getCurrency()) . ' ' . $this->_oData->getCurrency()->name;
                    $this->text(self::SUMMARYBEGIN, $iStartPos, $this->_oData->translate('ORDER_OVERVIEW_PDF_ZZGLVAT'));
                    $this->text(self::LINEEND - $this->getStringWidth($sWrapCostVAT), $iStartPos, $sWrapCostVAT);
                    $iStartPos = $this->nextLine($iStartPos);
                }

                if ($this->_oData->oxorder__oxgiftcardcost->value) {
                    // wrapping netto
                    $sWrapCostNetto = $oLang->formatCurrency($this->_oData->getOrderGiftCardPrice()->getNettoPrice(), $this->_oData->getCurrency()) . ' ' . $this->_oData->getCurrency()->name;
                    $this->text(self::SUMMARYBEGIN, $iStartPos, $this->_oData->translate('GIFTCARD_COSTS') . ' ' . $this->_oData->translate('ORDER_OVERVIEW_PDF_NETTO'));
                    $this->text(self::LINEEND - $this->getStringWidth($sWrapCostNetto), $iStartPos, $sWrapCostNetto);

                    //wrapping VAT
                    $iStartPos = $this->nextLine($iStartPos);
                    $sWrapCostVAT = $oLang->formatCurrency($this->_oData->getOrderGiftCardPrice()->getVATValue(), $this->_oData->getCurrency()) . ' ' . $this->_oData->getCurrency()->name;

                    if ($oConfig->getConfigParam('sAdditionalServVATCalcMethod') != 'proportional') {
                        $sVatValueText = $this->_oData->translate('ORDER_OVERVIEW_PDF_ZZGLVAT') . $this->_oData->oxorder__oxgiftcardvat->value . $this->_oData->translate('ORDER_OVERVIEW_PDF_PERCENTSUM');
                    } else {
                        $sVatValueText = $this->_oData->translate('TOTAL_PLUS_PROPORTIONAL_VAT');
                    }

                    $this->text(self::SUMMARYBEGIN, $iStartPos, $sVatValueText);
                    $this->text(self::LINEEND - $this->getStringWidth($sWrapCostVAT), $iStartPos, $sWrapCostVAT);
                    $iStartPos = $this->nextLine($iStartPos);
                }

            } else {
                $sAddString = ' ' . $this->_oData->translate('ORDER_OVERVIEW_PDF_BRUTTO');

                if ($this->_oData->oxorder__oxwrapcost->value) {
                    // wrapping cost
                    $sWrapCost = $oLang->formatCurrency($this->_oData->oxorder__oxwrapcost->value, $this->_oData->getCurrency()) . ' ' . $this->_oData->getCurrency()->name;
                    $this->text(self::SUMMARYBEGIN, $iStartPos, $this->_oData->translate('WRAPPING_COSTS' /*'ORDER_OVERVIEW_PDF_WRAPPING'*/) . $sAddString);
                    $this->text(self::LINEEND - $this->getStringWidth($sWrapCost), $iStartPos, $sWrapCost);
                    $iStartPos = $this->nextLine($iStartPos);
                }

                if ($this->_oData->oxorder__oxgiftcardcost->value) {
                    // gift card cost
                    $sWrapCost = $oLang->formatCurrency($this->_oData->oxorder__oxgiftcardcost->value, $this->_oData->getCurrency()) . ' ' . $this->_oData->getCurrency()->name;
                    $this->text(self::SUMMARYBEGIN, $iStartPos, $this->_oData->translate('GIFTCARD_COSTS') . $sAddString);
                    $this->text(self::LINEEND - $this->getStringWidth($sWrapCost), $iStartPos, $sWrapCost);
                    $iStartPos = $this->nextLine($iStartPos);
                }
            }
        }
    }

    /**
     * Sets payment info to pdf
     *
     * @param int &$iStartPos text start position
     */
    protected function _setPaymentInfo(&$iStartPos)
    {
        $oLang = oxRegistry::getLang();
        $oConfig = oxRegistry::getConfig();

        if ($this->_oData->oxorder__oxstorno->value) {
            $this->_oData->oxorder__oxpaycost->setValue(0);
        }

        if ($oConfig->getConfigParam('blShowVATForDelivery')) {
            if ($this->_oData->oxorder__oxpayvat->value) {
                // payment netto
                $sPayCostNetto = $oLang->formatCurrency($this->_oData->getOrderPaymentPrice()->getNettoPrice(), $this->_oData->getCurrency()) . ' ' . $this->_oData->getCurrency()->name;
                $this->text(self::SUMMARYBEGIN, $iStartPos, $this->_oData->translate('ORDER_OVERVIEW_PDF_PAYMENTIMPACT') . ' ' . $this->_oData->translate('ORDER_OVERVIEW_PDF_NETTO'));
                $this->text(self::LINEEND - $this->getStringWidth($sPayCostNetto), $iStartPos, $sPayCostNetto);

                if ($oConfig->getConfigParam('sAdditionalServVATCalcMethod') != 'proportional') {
                    $sVatValueText = $this->_oData->translate('ORDER_OVERVIEW_PDF_ZZGLVAT') . $this->_oData->oxorder__oxpayvat->value . $this->_oData->translate('ORDER_OVERVIEW_PDF_PERCENTSUM');
                } else {
                    $sVatValueText = $this->_oData->translate('TOTAL_PLUS_PROPORTIONAL_VAT');
                }

                // payment VAT
                $iStartPos = $this->nextLine($iStartPos);
                $sPayCostVAT = $oLang->formatCurrency($this->_oData->getOrderPaymentPrice()->getVATValue(), $this->_oData->getCurrency()) . ' ' . $this->_oData->getCurrency()->name;
                $this->text(self::SUMMARYBEGIN, $iStartPos, $sVatValueText);
                $this->text(self::LINEEND - $this->getStringWidth($sPayCostVAT), $iStartPos, $sPayCostVAT);
                $iStartPos = $this->nextLine($iStartPos);
            }

            // if canceled order, reset value

        } else {

            // payment costs
            if ($this->_oData->oxorder__oxpaycost->value) {
                $sPayCost = $oLang->formatCurrency($this->_oData->oxorder__oxpaycost->value, $this->_oData->getCurrency()) . ' ' . $this->_oData->getCurrency()->name;
                $this->text(self::SUMMARYBEGIN, $iStartPos, $this->_oData->translate('ORDER_OVERVIEW_PDF_PAYMENTIMPACT'));
                $this->text(self::LINEEND - $this->getStringWidth($sPayCost), $iStartPos, $sPayCost);
                $iStartPos = $this->nextLine($iStartPos);
            }
        }
    }

    /**
     * Sets payment info to pdf.
     *
     * @param int &$iStartPos text start position
     */
    protected function _setTsProtection(&$iStartPos)
    {
        $oLang = oxRegistry::getLang();
        $oConfig = oxRegistry::getConfig();
        if ($this->_oData->oxorder__oxtsprotectcosts->value && $oConfig->getConfigParam('blShowVATForPayCharge')) {

            // payment netto
            $sPayCostNetto = $oLang->formatCurrency($this->_oData->getOrderTsProtectionPrice()->getNettoPrice(), $this->_oData->getCurrency()) . ' ' . $this->_oData->getCurrency()->name;
            $this->text(self::SUMMARYBEGIN, $iStartPos, $this->_oData->translate('ORDER_OVERVIEW_PDF_TSPROTECTION') . ' ' . $this->_oData->translate('ORDER_OVERVIEW_PDF_NETTO'));
            $this->text(self::LINEEND - $this->getStringWidth($sPayCostNetto), $iStartPos, $sPayCostNetto);

            // payment VAT
            $iStartPos = $this->nextLine($iStartPos);
            $sPayCostVAT = $oLang->formatCurrency($this->_oData->getOrderTsProtectionPrice()->getVATValue(), $this->_oData->getCurrency()) . ' ' . $this->_oData->getCurrency()->name;
            $this->text(self::SUMMARYBEGIN, $iStartPos, $this->_oData->translate('ORDER_OVERVIEW_PDF_ZZGLVAT') . $oConfig->getConfigParam('dDefaultVAT') . $this->_oData->translate('ORDER_OVERVIEW_PDF_PERCENTSUM'));
            $this->text(self::LINEEND - $this->getStringWidth($sPayCostVAT), $iStartPos, $sPayCostVAT);
            $iStartPos = $this->nextLine($iStartPos);

        } elseif ($this->_oData->oxorder__oxtsprotectcosts->value) {

            $iStartPos = $this->nextLine($iStartPos);
            $sPayCost = $oLang->formatCurrency($this->_oData->oxorder__oxtsprotectcosts->value, $this->_oData->getCurrency()) . ' ' . $this->_oData->getCurrency()->name;
            $this->text(self::SUMMARYBEGIN, $iStartPos, $this->_oData->translate('ORDER_OVERVIEW_PDF_TSPROTECTION'));
            $this->text(self::LINEEND - $this->getStringWidth($sPayCost), $iStartPos, $sPayCost);
            $iStartPos = $this->nextLine($iStartPos);
        }
    }

    /**
     * Sets grand total order price to pdf.
     *
     * @param int &$iStartPos text start position
     */
    protected function _setGrandTotalPriceInfo(&$iStartPos)
    {
        // total order sum
        $sTotalOrderSum = $this->_oData->getFormattedTotalOrderSum() . ' ' . $this->_oData->getCurrency()->name;
        $this->text(self::SUMMARYBEGIN, $iStartPos, $this->_oData->translate('ORDER_OVERVIEW_PDF_ALLSUM'));
        $this->text(self::LINEEND - $this->getStringWidth($sTotalOrderSum), $iStartPos, $sTotalOrderSum);
        $iStartPos = $this->nextLine($iStartPos);

        if ($this->_oData->oxorder__oxdelvat->value || $this->_oData->oxorder__oxwrapvat->value || $this->_oData->oxorder__oxpayvat->value) {
            //
        }
    }

    /**
     * Sets payment method info to pdf.
     *
     * @param int &$iStartPos text start position
     */
    protected function _setPaymentMethodInfo(&$iStartPos)
    {
        $oPayment = oxNew('oxpayment');
        $oPayment->loadInLang($this->_oData->getSelectedLang(), $this->_oData->oxorder__oxpaymenttype->value);

        $text = $this->_oData->translate('ORDER_OVERVIEW_PDF_SELPAYMENT') . $oPayment->oxpayments__oxdesc->value;
        $iStartPos = $this->nextLine($iStartPos);
        $this->text(self::LINEBEGIN, $iStartPos, $text);
    }

    /**
     * Sets pay until date to pdf.
     *
     * @param int &$iStartPos text start position
     */
    protected function _setPayUntilInfo(&$iStartPos)
    {
        $text = $this->_oData->translate('ORDER_OVERVIEW_PDF_PAYUPTO') . date('d.m.Y', strtotime('+' . $this->_oData->getPaymentTerm() . ' day', strtotime($this->_oData->oxorder__oxbilldate->value)));
        $iStartPos = $this->nextLine($iStartPos);
        $this->text(self::LINEBEGIN, $iStartPos, $text);
    }

    /**
     * Generates order info block (prices, VATs, etc ).
     *
     * @param int $iStartPos text start position
     *
     * @return int
     */
    public function generate($iStartPos)
    {
        $this->font($this->getFont(), '', 8);
        $this->setLineHeight(1.1);

        $iStartPos = $this->nextLine($iStartPos);
        $siteH = $iStartPos;

        // #1147 discount for vat must be displayed
        $this->line(self::LINEBEGIN, $siteH, self::LINEEND, $siteH);

        if (!$this->_oData->oxorder__oxdiscount->value) {
            $this->_setTotalCostsWithoutDiscount($siteH);
        } else {
            $this->_setTotalCostsWithDiscount($siteH);
        }

        $this->line(self::SUMMARYBEGIN, $siteH, self::LINEEND, $siteH);


        // voucher info
        $this->_setVoucherInfo($siteH);

        // delivery info
        $this->_setDeliveryInfo($siteH);

        // payment info
        $this->_setPaymentInfo($siteH);

        // wrapping info
        $this->_setWrappingInfo($siteH);

        // TS protection info
        $this->_setTsProtection($siteH);

        // separating line

        $this->line(self::LINEBEGIN, $siteH, self::LINEEND, $siteH);

        // total order sum
        $this->_setGrandTotalPriceInfo($siteH);

        // separating line
        $this->line(self::LINEBEGIN, $siteH, self::LINEEND, $siteH);
        $siteH = $this->nextLine($siteH);

        // payment method
        $this->_setPaymentMethodInfo($siteH);

        // pay until ...
        if($this->_oData->oxorder__oxpaymenttype->value === 'oxidinvoice'){
            $this->_setPayUntilInfo($siteH);
        }

        return $siteH - $iStartPos;
    }
}