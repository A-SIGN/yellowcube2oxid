<?php
/**
 * Defines module information
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
 * Metadata version
 */
$sMetadataVersion = '1.0';

/**
 * Module information
 */
$aModule = array(
    'id'            => 'asign_invoicepdf',
    'title'         => 'A-Sign GmbH Invoice PDF v1.0',
    'description'   => 'Module to export invoice PDF files.',
    'thumbnail'     => 'picture.png',
    'version'       => '1.0',
    'author'        => 'entwicklung@a-sign.ch',
    'email'         => 'entwicklung@a-sign.ch',
    'url'           => 'http://www.a-sign.ch',
    'extend'        => array(
        'oxorder'        => 'asign/asign_invoicepdf/application/models/invoicepdfoxorder',
        'order_overview' => 'asign/asign_invoicepdf/application/controllers/admin/invoicepdforder_overview',
    ),
    'files'         => array(
        'InvoiceoxPdf'             => 'asign/asign_invoicepdf/application/models/invoiceoxpdf.php',
        'InvoicepdfBlock'          => 'asign/asign_invoicepdf/application/models/invoicepdfblock.php',
        'InvoicepdfArticleSummary' => 'asign/asign_invoicepdf/application/models/invoicepdfarticlesummary.php',
        'oxpdf'                    => 'asign/asign_invoicepdf/application/models/oxpdf.php',
        'cn22labelpdfblock'        => 'asign/asign_invoicepdf/application/models/cn22labelpdfblock.php',
    ),
    'blocks'        => array(
        array(
            'template' => 'order_overview.tpl',
            'block'    => 'admin_order_overview_export',
            'file'     => 'application/views/admin/blocks/order_overview.tpl'
        ),
    ),
    'settings' => array(
        array('group' => 'main', 'name' => 'blGeneratePdfInPdfaFormat', 'type' => 'bool', 'value' => 'false'),
        array('group' => 'main', 'name' => 'blAddPPFrankate', 'type' => 'bool', 'value' => 'false'),
        array('group' => 'main', 'name' => 'sFrankateNr', 'type' => 'str', 'value' => ''),
        array(
            'group'         => 'main',
            'name'          => 'sCN22OrderType',
            'type'          => 'select',
            'value'         => 'O',
            'constraints'    => 'G|C|D|O'
        ),
    ),
);
