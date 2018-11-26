<?php
/**
 * Created by PhpStorm.
 * User: vincent
 * Date: 04/11/2018
 * Time: 11:43
 */

namespace Vidavenel\Payzen;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;

class PayzenService
{
    private $mode;
    private $site_id;
    private $debug;

    /**
     * PayzenService constructor.
     * @param string $mode TEST | PRODUCTION
     */
    const INTERACTIVE = 'INTERACTIVE';

    public function __construct($site_id, $mode = 'TEST', $debug = false)
    {
        $this->mode = $mode;
        $this->site_id = $site_id;
        $this->debug = $debug;
    }

    public function form(CommandeInterface $commande, $idform = 'payzen_form')
    {
        /** @var Paiement $paiement */
        $paiement = Paiement::firstOrNew([
            'commande_id' => $commande->getId(),
            'statut' => null
            ],
            [
            'order_id' => $this->calulOrderId($commande->getId()),
            'trans_id' => $this->calculTransId(),
        ]);
        $paiement->trans_date = gmdate('YmdHis');
        $paiement->prix = $commande->getPrix();
        $paiement->save();

        $vads = $this->getVars($commande, $paiement);
        $signature = $this->calculSignature($vads);

        return View::make('payzen::form', ['id_form' => $idform, 'vads' => $vads, 'signature' => $signature]);
    }

    /**
     * init des variables du formulaire
     * @param CommandeInterface $commande
     * @param Paiement $paiement
     * @return array
     */
    private function getVars(CommandeInterface $commande, Paiement $paiement)
    {
        $_client = $commande->getClient();
        $_adresse = $_client->getAdresse();

        $opt_tech = [
            'vads_action_mode' => 'INTERACTIVE',
            'vads_ctx_mode' => $this->mode,
            'vads_page_action' => 'PAYMENT',
            'vads_site_id' => $this->site_id,
            'vads_version' => 'V2'
        ];
        $opt_transaction = [
            'vads_amount' =>  $paiement->prix,
            'vads_capture_delay' => 0,
            'vads_currency' => 978,
            'vads_payment_config' => 'SINGLE',
            'vads_trans_id' => str_pad($paiement->trans_id, 6, '0', STR_PAD_LEFT),
            'vads_order_id' =>  $paiement->order_id,
            'vads_validation_mode' => 0,
            'vads_trans_date' =>  $paiement->trans_date
        ];
        $opt_commande = [
            'vads__nb_products' => 1,
            'vads_product_amount0' => $paiement->prix,
            'vads_product_label0' => 'Carte grise',
            'vads_product_qty0' => 1,
            'vads_product_ref0' => 'TCG',
            'vads_product_type0' =>  'AUTOMOTIVE'
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
    private function checkAdresse(array $adresse)
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

        if ($this->debug) {
            $str = "";
            foreach ($table as $k => $value) {
                $str .= "$k => $value - ";
            }
            Log::debug($str);
        }

        $string = implode('+', $table) . config('payzen.key');
        $encoded_string = SHA1($string);
        if ($this->debug) {
            $str = "";
            foreach ($table as $k => $value) {
                $str .= "$k => $value - ";
            }
            Log::debug($str);
            Log::debug("chaine avant encodage : $string");
            Log::debug("chaine encode : $encoded_string");
        }

        return $encoded_string;
    }

    private function calulOrderId($idCommande)
    {
        $count = Paiement::where('commande_id', $idCommande)->count();
        if ($count < 10 ) {
            $count = "0".$count;
        }
        return $idCommande.$count;
    }

    private function calculTransId()
    {
        $date = Carbon::now()->format('Y-m-d');
        return Paiement::whereDate('created_at', $date)->count() + 1;
    }
}