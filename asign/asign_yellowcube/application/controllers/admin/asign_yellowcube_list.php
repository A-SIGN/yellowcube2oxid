<?php
/**
* Renders template asign_yellowcube_list.tpl
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
* @see       Asign_YellowCube_List
* @since     File available since Release 2.0
*/

/**
* Renders template asign_yellowcube_list.tpl
* 
* @category Asign
* @package  Asign_Yellowcube_V_EE
* @author   Asign <entwicklung@a-sign.ch>
* @license  http://www.a-sign.ch/
* @link     http://www.a-sign.ch
*/
class Asign_Yellowcube_List extends oxAdminList
{
    /**
     * Name of chosen object class (default null).
     *
     * @var string
     */
    protected $_sListClass = 'asign_yellowcube_model';

    /**
     * Default SQL sorting parameter (default null).
     *
     * @var string
     */
    protected $_sDefSortField = 'oxycarticlenr';

    /**
     * Enable/disable sorting by DESC (SQL) (default false - disable).
     *
     * @var bool
     */
    protected $_blDesc = false;

    /**
     * Executes parent method parent::render() and returns name of template
     * file "asign_yellowcube_list.tpl".
     *
     * @return string
     */
    public function render()
    {
        parent::render();
        return "asign_yellowcube_list.tpl";
    }
    
    /**
     * Returns Shop name for the sent id
     *
     * @param string $oxid Shop Id
     * 
     * @return string
     */
    public function getShopName($oxid)
    {
        return oxDb::getDb()->getOne("select `oxshopid` from `asign_ycinventory` where `oxid` = '" . $oxid . "'");
    }    
}
