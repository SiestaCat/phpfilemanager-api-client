<?php

namespace Siestacat\PhpfilemanagerApiClient;

final class Exists
{
    public function __construct(private Client $client)
    {}

    public function exists(string $hash):\stdClass
    {
        return $this->client->makeRequest
        (
            'GET',
            'exists/' . $hash
        );
    }
}