<?php
/**
* Module information defined....
* 
* @category  Asign
* @package   Asign_Yellowcube_V_EE
* @author    Asign <entwicklung@a-sign.ch>
* @copyright asign
* @license   http://www.a-sign.ch/
* @version   2.0
* @link      http://www.a-sign.ch/
* @see       Module information
* @since     File available since Release 2.0
*/

/**
 * Metadata version
 */
$sMetadataVersion = '1.1';

/**
 * Module information
 */
$aModule = array(
    'id'            =>  'asign_yellowcube',
    'title'         =>  'A-Sign GmbH Yellowcube v2.0',
    'description'   =>  'Integrates SOAP based Yellowcube API. For signing in with SOAP Certificate upload your binary certificate file under <em>/cert folder</em>. <br /><br /><strong>Important:</strong> Make sure that this folder contents are <strong>.htaccess protected</strong>.',
    'version'       =>  '2.0',
    'thumbnail'     =>  'images/picture.png',
    'author'        =>  'entwicklung@a-sign.ch',
    'email'         =>  'entwicklung@a-sign.ch',
    'url'           =>  'http://www.a-sign.ch',
    'extend'        =>  array(
                            'oxorder'   => 'asign/asign_yellowcube/application/controllers/asign_yellowcube_oxorder'
    ),
                        
    'events'        =>  array(
                            'onActivate'        => 'asign_yellowcube_event::onActivate',
                            'onDeactivate'      => 'asign_yellowcube_event::onDeactivate',
                        ),
                         
    'files'         =>  array(                            
                            // soap related files 
                            'asign_soapclientapi'       => 'asign/asign_yellowcube/core/asign_soapclientapi.php',
                            'asign_soapclient'          => 'asign/asign_yellowcube/core/utils/asign_soapclient.php',
                            'WSSESoap'                  => 'asign/asign_yellowcube/core/utils/inc/WSSESoap.php',
                            'XMLSecurityDSig'           => 'asign/asign_yellowcube/core/utils/inc/XMLSecurityDSig.php',
                            'XMLSecurityKey'            => 'asign/asign_yellowcube/core/utils/inc/XMLSecurityKey.php',
                            
                            'asign_yellowcubecore'      => 'asign/asign_yellowcube/core/asign_yellowcubecore.php',    
                            'asign_yellowcubecron'      => 'asign/asign_yellowcube/core/asign_yellowcubecron.php',
                            'asign_yellowcube_event'    => 'asign/asign_yellowcube/admin/asign_yellowcube_event.php',
                            'asign_yellowcube'          => 'asign/asign_yellowcube/application/controllers/admin/asign_yellowcube.php',
                            'asign_yellowcube_list'     => 'asign/asign_yellowcube/application/controllers/admin/asign_yellowcube_list.php',
                            'asign_yellowcube_main'     => 'asign/asign_yellowcube/application/controllers/admin/asign_yellowcube_main.php',
                            'asign_yellowcube_logs'     => 'asign/asign_yellowcube/application/controllers/admin/asign_yellowcube_logs.php',
                            'asign_yellowcube_articles' => 'asign/asign_yellowcube/application/controllers/admin/asign_yellowcube_articles.php',
                            'asign_yellowcube_orders'   => 'asign/asign_yellowcube/application/controllers/admin/asign_yellowcube_orders.php',
                            'asign_yellowcube_return'   => 'asign/asign_yellowcube/application/controllers/admin/asign_yellowcube_return.php',
                            'asign_yellowcube_util'     => 'asign/asign_yellowcube/application/controllers/asign_yellowcube_util.php',
                            'asign_yellowcube_model'    => 'asign/asign_yellowcube/application/models/asign_yellowcube_model.php',
                        ),
                        
    'templates'     =>  array(                        
                            'asign_yellowcube.tpl'          => 'asign/asign_yellowcube/application/views/admin/tpl/asign_yellowcube.tpl',    
                            'asign_yellowcube_list.tpl'     => 'asign/asign_yellowcube/application/views/admin/tpl/asign_yellowcube_list.tpl',
                            'asign_yellowcube_main.tpl'     => 'asign/asign_yellowcube/application/views/admin/tpl/asign_yellowcube_main.tpl',
                            'asign_yellowcube_logs.tpl'     => 'asign/asign_yellowcube/application/views/admin/tpl/asign_yellowcube_logs.tpl',
                            'asign_yellowcube_articles.tpl' => 'asign/asign_yellowcube/application/views/admin/tpl/asign_yellowcube_articles.tpl',
                            'asign_yellowcube_orders.tpl'   => 'asign/asign_yellowcube/application/views/admin/tpl/asign_yellowcube_orders.tpl',
                            'asign_yellowcube_return.tpl'   => 'asign/asign_yellowcube/application/views/admin/tpl/asign_yellowcube_return.tpl',
                        ),    
     'blocks'        => array(
                                array(
                                    'template' => 'order_list.tpl',
                                    'block'    => 'admin_order_list_item',
                                    'file'     => 'application/views/admin/blocks/asign_yellowcube_listitems.tpl'
                                ),
                        ),
    
    'settings'      =>  array(
                                // SOAP information
                                array(
                                    'group'         => 'main', 
                                    'name'          => 'sYellowCubeMode',
                                    'type'          => 'select', 
                                    'value'         => 'T',
                                    'constraints'    => 'T|P|D'
                                ),
                                array(
                                    'group'         => 'main',
                                    'name'          => 'sYellowCubeWsdlUrl',
                                    'type'          => 'str',
                                    'value'         => '',
                                ),
                                array(
                                    'group'         => 'main',
                                    'name'          => 'sYellowCubeTransMaxTime',
                                    'type'          => 'str',
                                    'value'         => '120'
                                ),                                

                                // authentication information only
                                array(
                                    'group'         => 'authentic',
                                    'name'          => 'sYellowCubeDepositorNo',
                                    'type'          => 'str',
                                    'value'         => '',
                                ),
                                array(
                                    'group'         => 'authentic',
                                    'name'          => 'sYellowCubeSender',
                                    'type'          => 'str',
                                    'value'         => 'YCTest'
                                ),
                                array(
                                    'group'         => 'authentic',
                                    'name'          => 'sYellowCubeReceiver',
                                    'type'          => 'str',
                                    'value'         => 'YELLOWCUBE'
                                ),

                                // certificate information
                                array(
                                    'group'         => 'certificate',
                                    'name'          => 'blYellowCubeCertForAll',
                                    'type'          => 'bool',
                                    'value'         => 'true',
                                ),
                                array(
                                    'group'         => 'certificate',
                                    'name'          => 'sYellowCubeCertFile',
                                    'type'          => 'str',
                                    'value'         => '',
                                ),                                

                                // additional information only                               
                                array(
                                    'group'         => 'additional',
                                    'name'          => 'sYellowCubePartnerNo',
                                    'type'          => 'str',
                                    'value'         => ''
                                ),
                                array(
                                    'group'         => 'additional',
                                    'name'          => 'sYellowCubePType',
                                    'type'          => 'str',
                                    'value'         => 'WE'
                                ),
                                array(
                                    'group'         => 'additional',
                                    'name'          => 'sYellowCubePlant',
                                    'type'          => 'str',
                                    'value'         => ''
                                ),


                                // Order information only
                                array(
                                    'group'         => 'order',
                                    'name'          => 'blYellowCubeDocDelete',
                                    'type'          => 'bool',
                                    'value'         => 'false'
                                ),
                                array(
                                    'group'         => 'order',
                                    'name'          => 'blYellowCubeUsePhoneAndFax',
                                    'type'          => 'bool',
                                    'value'         => 'false'
                                ),
                                array(
                                    'group'         => 'order',
                                    'name'          => 'blYellowCubeOrderManualSend',
                                    'type'          => 'bool',
                                    'value'         => 'false'
                                ),                                                               
                                array(
                                    'group'         => 'order',
                                    'name'          => 'sYellowCubeOrderDocumentsFlag',
                                    'type'          => 'select',
                                    'value'         => '1',
                                    'constraints'    => '1|0'
                                ),                                
                                array(
                                    'group'         => 'order',
                                    'name'          => 'sYellowCubeDocMimeType',
                                    'type'          => 'select',
                                    'value'         => 'pdf',
                                    'constraints'    => 'pdf|pcl'
                                ),
                                array(
                                    'group'         => 'order',
                                    'name'          => 'sYellowCubeDocType',
                                    'type'          => 'select',
                                    'value'         => 'LS',
                                    'constraints'    => 'LS|BL|BC|DN|IV|RG|FA|ZS|BP|BV|PF'
                                ),                                

                                // article information only 
                                array(
                                    'group'         => 'article',
                                    'name'          => 'blYellowCubeIgnoreLotInfo',
                                    'type'          => 'bool',
                                    'value'         => 'true'
                                ),                              
                                array(
                                    'group'         => 'article',
                                    'name'          => 'sYellowCubeNetWeightISO',
                                    'type'          => 'select',
                                    'value'         => 'KGM',
                                    'constraints'    => 'KGM|GRM'
                                ),
                                array(
                                    'group'         => 'article',
                                    'name'          => 'sYellowCubeGrossWeightISO',
                                    'type'          => 'select',
                                    'value'         => 'KGM',
                                    'constraints'    => 'KGM|GRM'
                                ),
                                array(
                                    'group'         => 'article',
                                    'name'          => 'sYellowCubeEANType',
                                    'type'          => 'select',
                                    'value'         => 'HE',
                                    'constraints'    => 'HE|HK|I6|IC|IE|IK|UC|VC'
                                ),
                                array(
                                    'group'         => 'article',
                                    'name'          => 'sYellowCubeQuantityISO',
                                    'type'          => 'str',
                                    'value'         => 'PCE',
                                ),   
                                array(
                                    'group'         => 'article',
                                    'name'          => 'sYellowCubeAlternateUnitISO',
                                    'type'          => 'str',
                                    'value'         => 'PCE',
                                ),                                
                                array(
                                    'group'         => 'article',
                                    'name'          => 'sYellowCubeLengthISO',
                                    'type'          => 'select',
                                    'value'         => 'MTR',
                                    'constraints'    => 'MTR|CMT|MMT'
                                ),
                                array(
                                    'group'         => 'article',
                                    'name'          => 'sYellowCubeWidthISO',
                                    'type'          => 'select',
                                    'value'         => 'MTR',
                                    'constraints'    => 'MTR|CMT|MMT'
                                ),
                                array(
                                    'group'         => 'article',
                                    'name'          => 'sYellowCubeHeightISO',
                                    'type'          => 'select',
                                    'value'         => 'MTR',
                                    'constraints'    => 'MTR|CMT|MMT'
                                ),
                                array(
                                    'group'         => 'article',
                                    'name'          => 'sYellowCubeVolumeISO',
                                    'type'          => 'select',
                                    'value'         => 'CMQ',
                                    'constraints'    => 'CMQ|MTQ'
                                ),

                                // Cronjob information
                                array(
                                    'group'         => 'cronjob',
                                    'name'          => 'sYellowCubeCronHash',
                                    'type'          => 'str',
                                    'value'         => ''
                                ),
                                array(
                                    'group'         => 'developer',
                                    'name'          => 'sYellowCubeNotifyEmail',
                                    'type'          => 'str',
                                    'value'         => ''
                                ),
                        ),
);
