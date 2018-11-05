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
    private $mode;
    private $site_id;

    /**
     * PayzenService constructor.
     * @param string $mode TEST | PRODUCTION
     */
    const INTERACTIVE = 'INTERACTIVE';

    public function __construct($site_id, $mode = 'TEST')
    {
        $this->mode = $mode;
        $this->site_id = $site_id;
    }

    public function form(CommandeInterface $commande)
    {
        $this->commande = $commande;
        $vads = $this->getVars($commande);
        $signature = $this->calculSignature($vads);

        return View::make('payplug::form', ['id' => $commande->getId(), 'vads' => $vads, 'signature' => $signature]);
    }

    /**
     * init des variables du formulaire
     * @param CommandeInterface $commande
     * @return array
     */
    private function getVars(CommandeInterface $commande)
    {
        $_client = $commande->getClient();
        $_adresse = $_client->getAdresse();

        $opt_tech = [
            'vads_action_mode' => 'INTERACTIVE',
            'vads_ctx_mode' => $this->mode,
            'vads_page_action' => 'PAYMENT',
            'vads_site_id' => $this->site,
            'vads_version' => 'V2'
        ];
        $opt_transaction = [
            'vads_amount' =>  $commande->getPrix(),
            'vads_capture_delay' => 0,
            'vads_currency' => 978,
            'vads_payment_config' => 'SINGLE',
            'vads_trans_id' => 0,
            'vads_order_id' =>  $commande->getId(),
            'vads_validation_mode' => 0,
            'vads_trans_date' =>  gmdate('YmdHis'),
        ];
        $opt_commande = [
            'vads__nb_products' => 1,
            'vads_product_amount' => [$commande->getPrix()],
            'vads_product_label' => ['Carte grise'],
            'vads_product_qty' => [1],
            'vads_product_ref' => ['TCG'],
            'vads_product_type' =>  ['AUTOMOTIVE']
        ];
        $opt_acheteur = [
            'vads_cust_last_name' => $_client->getNom(),
            'vads_cust_first_name' => $_client->getPrenom(),
            'vads_cust_address' => $_adresse['adresse'],
            'vads_cust_zip' => $_adresse['cp'],
            'vads_cust_city' => $_adresse['ville'],
            'vads_cust_country' => 'FR',
            'vads_cust_email' => $_client->getMail(),
            'vads_cust_id' => $_client->getId(),
            'vads_cust_status' => 'PRIVATE',
        ];
        $opt_livraison = [
            'vads_ship_to_city' => $_adresse['ville'],
            'vads_ship_to_country' => 'FR',
            'vads_ship_to_delivery_company_name' => 'La poste',
            'vads_cust_first_name' => $_client->getPrenom(),
            'vads_ship_to_last_name' => $_client->getNom(),
            'vads_ship_to_phone_num' => $_client->getPhone(),
            'vads_ship_to_speed' => 'STANDARD',
            'vads_ship_to_status' => 'PRIVATE',
            'vads_ship_to_street' => $_adresse['adresse'],
            'vads_ship_to_type' => 'PACKAGE_DELIVERY_COMPANY',
            'vads_ship_to_zip' => $_adresse['cp'],
        ];
        return array_merge($opt_tech, $opt_transaction, $opt_commande, $opt_acheteur, $opt_livraison);
    }

    /**
     * Verification du tableau adresse
     * @param array $adresse
     * @return bool
     */
    public function checkAdresse(array $adresse)
    {
        foreach (['adresse', 'cp', 'ville'] as $cle) {
            if (!array_key_exists($cle, $adresse))
                return false;
        }
        return true;
    }

    /**
     * Calcul de la signature a inclure dans le formulaire
     * @param array $table
     * @return string
     */
    private function calculSignature(array $table)
    {
        ksort($table);
        $string = implode('', $table);
        return SHA1($string);
    }
}