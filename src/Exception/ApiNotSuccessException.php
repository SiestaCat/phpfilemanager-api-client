<?php

namespace Siestacat\PhpfilemanagerApiClient\Exception;

final class ApiNotSuccessException extends \Exception
{
    public function __construct(\stdClass $json)
    {
        parent::__construct
        (
            sprintf('Api response not success. Error message: %s. JSON Response: %s', $json->error, json_encode($json))
        );
    }
}