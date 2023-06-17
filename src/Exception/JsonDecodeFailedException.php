<?php

namespace Siestacat\PhpfilemanagerApiClient\Exception;

final class JsonDecodeFailedException extends \Exception
{
    public function __construct(string $json_str)
    {
        parent::__construct
        (
            sprintf('Unable to decode JSON. Error: %s Error code: %s. JSON STR:', json_last_error_msg(), json_last_error())
            . "\n" . $json_str
        );
    }
}