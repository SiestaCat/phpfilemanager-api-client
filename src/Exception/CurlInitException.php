<?php

namespace Siestacat\PhpfilemanagerApiClient\Exception;

final class CurlInitException extends \Exception
{
    public function __construct()
    {
        parent::__construct('Unable to init curl');
    }  
}