<?php

/**
 * Extends oxorder to overload the PDF functions from the invoicePDF module.
 * @category asign
 * @package  A-asign_ycaddfields_international
 * @author   Johannes Rebhan A-SIGN GmbH 2015 - rebhan@a-sign.ch
 * @link     http://www.a-sign.ch
 */
class asign_ycaddfields_oxorder extends asign_ycaddfields_oxorder_parent
{
    /**
     * Overloads function to add copying of additional field in checkout
     * @param $oUser
     */
    protected function _setUser($oUser)
    {
        parent::_setUser($oUser);
        $sSourceFieldName = $oUser->getCoreTableName() . '__' . asign_ycaddfields_db::EORI;
        $sDestFieldName = $this->getCoreTableName() . '__' . asign_ycaddfields_db::EORI;
        $this->$sDestFieldName = clone $oUser->$sSourceFieldName;
    }
}