<?php
/**
 * Created by PhpStorm.
 * User: vincent
 * Date: 04/11/2018
 * Time: 11:13
 */
namespace Vidavenel\Payzen;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Paiement
 * @package Vidavenel\Payzen
 * @property-read int $id
 * @property int $commande_id
 * @property int $order_id
 * @property int $trans_id
 * @property int $trans_date
 * @property int $prix
 */
class Paiement extends Model
{
    protected $table = 'payzen_paiements';
    protected $fillable = ['commande_id', 'order_id', 'trans_id', 'trans_date', 'prix'];
}