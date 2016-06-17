<?php

/**
 * Metadata file
 * 
 * PHP version 5
 * 
 * @category  A-SIGN
 * @package   asign_ycaddfields_international
 * @author    A-Sign <entwicklung@a-sign.ch>
 * @copyright Johannes Rebhan - A-SIGN GmbH 2015
 * @license   
 * @link      http://www.a-sign.ch
 * @see       Metadata.php
 * @since     File available since Release 1.0
 */
$sMetadataVersion = '1.1';

$aModule = array(
    'id'                    => 'asign_ycaddfields_international',
    'title'                 => 'A-Sign GmbH Additional Fields for YC international shipping',
    'description'           => 'If a shop wants to send orders internationally this module is needed to add additional fields to article, user and order data tables and admin interfaces. Without this module international shipping will not generate appropriate PDF documents',
    'version'               => '1.9',
    'thumbnail'             => 'images/picture.png',
    'author'                => 'A-Sign',
    'email'                 => 'entwicklung@a-sign.ch',
    'url'                   => 'http://www.a-sign.ch',
    'extend'                =>  array(
        'oxorder'                       =>     'asign/asign_ycaddfields_international/application/models/asign_ycaddfields_oxorder',
        'oxarticle'                       =>     'asign/asign_ycaddfields_international/application/models/asign_ycaddfields_oxarticle',
        'oxorderarticle'                       =>     'asign/asign_ycaddfields_international/application/models/asign_ycaddfields_oxorderarticle',
    ),
    'files'                 => array(
        'asign_ycaddfields_db'        =>     'asign/asign_ycaddfields_international/application/controllers/asign_ycaddfields_db.php',
    ),
    'blocks'                =>  array(
        array(
            'template'                  =>     'form/fieldset/user_billing.tpl',
            'block'                     =>     'form_user_billing_country',
            'file'                      =>     'application/views/blocks/asign_ycaddfields_user_billing.tpl'
        ),
        array(
            'template'                  =>     'order_address.tpl',
            'block'                     =>     'admin_order_address_billing',
            'file'                      =>     'application/views/blocks/asign_ycaddfields_address_admin.tpl'
        ),
        array(
            'template'                  =>     'user_main.tpl',
            'block'                     =>     'admin_user_main_form',
            'file'                      =>     'application/views/blocks/asign_ycaddfields_user_admin.tpl'
        ),
        array(
            'template'                  =>     'article_extend.tpl',
            'block'                     =>     'admin_article_extend_form',
            'file'                      =>     'application/views/blocks/asign_ycaddfields_article_admin.tpl'
        ),
    ),
    'settings'              => array(
        //array('group' => 'GENERAL', 'name' => 'sTrustedShopsID', 'type' => 'str', 'value' => ''),
    ),
    'events'        => array(
        'onActivate'     => 'asign_ycaddfields_db::onActivate',
//        'onDeactivate'   => 'asign_connector_db::onDeactivate',
    ),
);