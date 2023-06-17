<?php

namespace Siestacat\PhpfilemanagerApiClient\Exception;

final class MethodPostRequiredException extends \Exception
{
    public function __construct()
    {
        parent::__construct('Method POST required for upload files');
    }  
}