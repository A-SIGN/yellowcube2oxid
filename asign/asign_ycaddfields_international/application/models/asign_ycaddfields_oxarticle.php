<?php
/**
 * Extends oxarticle to add more data fields in order completion for PDF generation
 * @category asign
 * @package  A-asign_ycaddfields_international
 * @author   Johannes Rebhan A-SIGN GmbH 2015 - rebhan@a-sign.ch
 * @link     http://www.a-sign.ch
 */

class asign_ycaddfields_oxarticle extends asign_ycaddfields_oxarticle_parent{

    public function getCountryOriginList($iLang){
        $oCountryList = oxNew('oxcountrylist');
        $sViewName = getViewName('oxcountry', $iLang);
        $sSelect = "SELECT oxid, oxtitle, oxisoalpha2 FROM " . $sViewName . " ORDER BY oxorder, oxtitle";
        $oCountryList->selectString($sSelect);

        return $oCountryList;
    }
}