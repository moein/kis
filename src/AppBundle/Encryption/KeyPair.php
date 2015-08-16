<?php

namespace AppBundle\Encryption;


class KeyPair
{
    public $privateKey;
    public $publicKey;

    public function __construct($privateKey, $publicKey)
    {
        $this->privateKey = $privateKey;
        $this->publicKey = $publicKey;
    }
}