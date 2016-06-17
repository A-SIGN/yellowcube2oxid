<?php
/**
 * Helper class for certain DB tasks like creating custom fields in different tables on module activation and CONST fields for easier changing and more extendable code.
 * @category asign
 * @package  A-asign_ycaddfields_international
 * @author   Johannes Rebhan A-SIGN GmbH 2015 - rebhan@a-sign.ch
 * @link     http://www.a-sign.ch
 */

class asign_ycaddfields_db{
    const EORI = 'asigneori';
    const CUSTOMS = 'asigncustomstariff';
    const TARA = 'asigntara';
    const ORIGIN = 'asignorigin';

    /**
     * On activation of the module this creates custom fields. If more fields are switched from oxid vanilla to custom, or more fields are added, this has to be extended
     */
    public static function onActivate(){
        $sSQL_oxuser = "ALTER TABLE oxuser
                 ADD COLUMN " . self::EORI . " CHAR(17) NOT NULL DEFAULT ''";
        $sSQL_oxorder = "ALTER TABLE oxorder
                 ADD COLUMN " . self::EORI . " CHAR(17) NOT NULL DEFAULT ''";
        $sSQL_oxarticle = "ALTER TABLE oxarticles
                 ADD COLUMN " . self::CUSTOMS . " CHAR(11) NOT NULL DEFAULT '',
                ADD COLUMN " . self::TARA . " DOUBLE NOT NULL DEFAULT 0.0,
                ADD COLUMN " . self::ORIGIN . " VARCHAR(32) NOT NULL DEFAULT ''";
        $sSQL_oxorderarticles = "ALTER TABLE oxorderarticles
                 ADD COLUMN " . self::CUSTOMS . " CHAR(11) NOT NULL DEFAULT '',
                ADD COLUMN " . self::TARA . " DOUBLE NOT NULL DEFAULT 0.0,
                ADD COLUMN " . self::ORIGIN . " VARCHAR(32) NOT NULL DEFAULT ''";

        try{
            oxDb::getDb(oxDb::FETCH_MODE_ASSOC)->execute($sSQL_oxuser);
        } catch(Exception $oEx){
            oxRegistry::get('oxUtilsView')->addErrorToDisplay($oEx);
        }

        try{
            oxDb::getDb(oxDb::FETCH_MODE_ASSOC)->execute($sSQL_oxorder);
        } catch(Exception $oEx){
            oxRegistry::get('oxUtilsView')->addErrorToDisplay($oEx);
        }

        try{
            oxDb::getDb(oxDb::FETCH_MODE_ASSOC)->execute($sSQL_oxarticle);
        } catch(Exception $oEx){
            oxRegistry::get('oxUtilsView')->addErrorToDisplay($oEx);
        }

        try{
            oxDb::getDb(oxDb::FETCH_MODE_ASSOC)->execute($sSQL_oxorderarticles);
        } catch(Exception $oEx){
            oxRegistry::get('oxUtilsView')->addErrorToDisplay($oEx);
        }
    }

    public static function printDebug($oData){
        echo "<pre>";
        var_dump($oData);
        echo "</pre>";
    }
} 