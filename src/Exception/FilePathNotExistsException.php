<?php

namespace Siestacat\PhpfilemanagerApiClient\Exception;

final class FilePathNotExistsException extends \Exception
{
    public function __construct(string $path)
    {
        parent::__construct(sprintf('File path %s not exists', $path));
    }  
}