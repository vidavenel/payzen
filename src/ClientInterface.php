<?php
/**
 * Created by PhpStorm.
 * User: vince
 * Date: 05/11/2018
 * Time: 09:11
 */

namespace Vidavenel\Payzen;


interface ClientInterface
{
    public function getNom(): string;

    public function getPrenom(): string;

    public function getAdresse(): array;

    public function getPhone(): string;

    public function getMail(): string;

    public function getId(): int;
}