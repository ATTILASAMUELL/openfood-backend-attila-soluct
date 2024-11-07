<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\EncryptException;
use App\Exceptions\DecryptException;
use App\Exceptions\OpenFoodException;

trait CryptographyHelper
{
    /**
     * Criptografa uma string.
     *
     * @param string $value
     * @return string
     * @throws EncryptException
     */
    public function encryptValue(string $value): string
    {
        try {
            return Crypt::encryptString($value);
        } catch (EncryptException $e) {
            // Log ou tratar a exceção conforme necessário
            throw new EncryptException("Erro ao criptografar o valor: " . $e->getMessage());
        }
    }

    /**
     * Descriptografa uma string.
     *
     * @param string $encryptedValue
     * @return string
     * @throws EncryptException
     */
    public function decryptValue(string $encryptedValue): string
    {
        try {
            return Crypt::decryptString($encryptedValue);
        } catch (DecryptException $e) {
            // Log ou tratar a exceção conforme necessário
            throw new DecryptException("Erro ao descriptografar o valor: " . $e->getMessage());
        }
    }
}
