<?php
/**
 * Created by PhpStorm.
 * User: vincent
 * Date: 04/11/2018
 * Time: 11:43
 */

namespace Vidavenel\Payzen;

use Illuminate\Support\Facades\View;

class PayzenService
{
    private $vads_action_mode;
    private $vads_ctx_mode;
    private $vads_page_action;
    private $vads_site_id;
    private $vads_version;

    private $vads_capture_delay;
    private $vads_currency;
    private $vads_payment_config;
    private $vads_validation_mode;

    private $vads_trans_date;
    private $vads_trans_id;
    private $vads_amount;

    /**
     * PayzenService constructor.
     * @param string $mode TEST | PRODUCTION
     */
    public function __construct($site_id, $mode = 'TEST')
    {
        $this->vads_action_mode = 'INTERACTIVE';
        $this->vads_ctx_mode = $mode;
        $this->vads_page_action = 'PAYMENT';
        $this->vads_site_id = $site_id;
        $this->vads_version = 'V2';

        $this->vads_capture_delay = 0;
        $this->vads_currency = 978;
        $this->vads_payment_config = 'SINGLE';
        $this->vads_validation_mode = 0;
    }

    public function form()
    {
        return View::make('payplug::form');
    }

    private function calculSignature()
    {
        $vads_Array = array (

            'vads_action_mode' => 'INTERACTIVE',
            'vads_amount' => ($TotalPanierResume * 100), // En centimes
            'vads_capture_delay' => '0',
            // 'vads_ctx_mode' => 'TEST', // A MODIFIER EN PROD
            'vads_ctx_mode' => 'PRODUCTION',
            'vads_currency' => '978',
            'vads_page_action' => 'PAYMENT',
            // 'vads_payment_config' => 'MULTI:first='.$x3Montant1.';count='.$NbEcheances.';period=30',
            'vads_payment_config' => 'SINGLE',
            'vads_payment_option_code' => $vads_payment_option_code,
            // 'vads_site_id' => '87894583',
            'vads_site_id' => '46788809',
            'vads_trans_date' => gmdate('YmdHis'),
            'vads_trans_id' => date('His'),
            'vads_validation_mode' => '0',
            'vads_version' => 'V2',
            'vads_order_id' => $session_id,
            'vads_payment_cards' => 'ONEY',
            'vads_payment_option_code' => $vads_payment_option_code,
            'vads_cust_status' => 'PRIVATE',
            // Répété plus bas pour les 4 prochaines lignes
            /* 'vads_ship_to_status' => 'PRIVATE', // COMPANY pour un Pro
            'vads_ship_to_type' => 'PACKAGE_DELIVERY_COMPANY',
            'vads_ship_to_speed' => 'STANDARD',
            'vads_ship_to_delivery_company_name' => 'XXX', */
            // PANIER
            'vads_nb_products' => 1,
            'vads_product_amount0' => ($TotalPanierResume * 100),
            'vads_product_qty0' => 1,
            'vads_product_label0' => $vads_product_labelN,
            'vads_product_ref0' => 'TCG',
            'vads_product_type0' => 'AUTOMOTIVE',
            // LE CLIENT
            'vads_cust_first_name' => $PaypalPrenom,
            'vads_cust_last_name' => $PaypalNom,
            'vads_cust_address' => $PaypalAdresse1,
            'vads_cust_zip' => $PaypalCodePostal,
            'vads_cust_city' => $PaypalVille,
            'vads_cust_country' => 'FR',
            'vads_cust_email' => $email,
            // ADRESSE DE LIVRAISON (Pour Oney seulement)
            'vads_ship_to_city' => $PaypalVille,
            'vads_ship_to_country' => 'FR',
            'vads_ship_to_delivery_company_name' => 'La Poste',
            'vads_ship_to_first_name' => $PaypalPrenom,
            'vads_ship_to_last_name' => $PaypalNom,
            'vads_ship_to_phone_num' => $vads_ship_to_phone_num,
            'vads_ship_to_speed' => 'STANDARD',
            'vads_ship_to_status' => 'PRIVATE',
            'vads_ship_to_street' => $PaypalAdresse1,
            'vads_ship_to_type' => 'PACKAGE_DELIVERY_COMPANY',
            'vads_ship_to_zip' => $PaypalCodePostal,
            // URL DE RETOUR AUTOMATIQUE
            'vads_redirect_success_timeout' => 2,
            'vads_url_success' => 'https://www.topcartegrise.fr/mon-compte.php?Ref='.$session_id,

        );
        if (isset ($TelMobile)) $vads_Array['vads_cust_cell_phone'] = $TelMobile;
        else $vads_Array['vads_cust_phone'] = $PaypalTel;
    }
}