<?php
/**
 * Created by PhpStorm.
 * User: vince
 * Date: 05/11/2018
 * Time: 10:03
 */

namespace Vidavenel\Payzen\Tests\factory;

use Vidavenel\Payzen\ClientInterface;
use Vidavenel\Payzen\CommandeInterface;

class Commande implements CommandeInterface
{
    public function getClient(): ClientInterface
    {
        // TODO: Implement getClient() method.
    }

    public function getPrix(): int
    {
        // TODO: Implement getPrix() method.
    }

    public function getId(): int
    {
        // TODO: Implement getId() method.
    }
}