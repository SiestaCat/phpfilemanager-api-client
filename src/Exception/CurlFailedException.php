<?php

namespace Siestacat\PhpfilemanagerApiClient\Exception;

final class CurlFailedException extends \Exception
{
    public function __construct(mixed $response, int $http_code)
    {

        $raw_response = is_string($response) || is_null($response) ? $response : '';

        //var_dump($raw_response);

        parent::__construct('Api failed response. HTTP CODE: ' . $http_code . '. Response: ' . $raw_response);
    }  
}