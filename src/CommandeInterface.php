<?php
/**
 * Created by PhpStorm.
 * User: vince
 * Date: 05/11/2018
 * Time: 08:59
 */

namespace Vidavenel\Payzen;


interface CommandeInterface
{
    public function getClient() : ClientInterface;
    public function getPrix() : int;
    public function getId() : int;
}