<?php

namespace AppBundle\Encryption;

class KeyGen
{
    public static function generateKeyPair($passPhrase)
    {
        $config = [
            'digest_alg' => 'sha512',
            'private_key_bits' => 4096,
            'private_key_type' => OPENSSL_KEYTYPE_RSA
        ];

        $res = openssl_pkey_new($config);

        openssl_pkey_export($res, $privateKey, $passPhrase);

        $publicKey = openssl_pkey_get_details($res)['key'];

        return new KeyPair($privateKey, $publicKey);
    }

    public static function encrypt($plainContent, $publicKey)
    {
        openssl_public_encrypt($plainContent, $encryptedContent, $publicKey);

        return base64_encode($encryptedContent);
    }

    public static function decrypt($encryptedContent, $privateKey, $passPhrase)
    {
        $res = openssl_get_privatekey($privateKey, $passPhrase);
        if ($res === false)
        {
            throw new InvalidPassPhraseException('The provided passphrase for private key is wrong');
        }
        openssl_private_decrypt(base64_decode($encryptedContent), $plainContent, $res);

        return $plainContent;
    }
}