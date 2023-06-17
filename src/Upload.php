<?php

namespace Siestacat\PhpfilemanagerApiClient;

final class Upload
{
    public function __construct(private Client $client)
    {}

    public function upload(mixed $files):\stdClass
    {

        $files = is_array($files) ? $files : [$files];

        return $this->client->makeRequest
        (
            'POST',
            'upload',
            [],
            $files
        );
    }
}