<?php

namespace App\Exceptions;

use Exception;

class DecryptException extends Exception
{
    protected $details;

    public function __construct($message = "Erro ao descriptografar", $code = 0, Exception $previous = null, $details = [])
    {
        parent::__construct($message, $code, $previous);
        $this->details = $details;
    }

    /**
     * Retorna detalhes adicionais do erro, se houver.
     *
     * @return array
     */
    public function getDetails()
    {
        return $this->details;
    }
}
