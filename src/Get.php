<?php

namespace Siestacat\PhpfilemanagerApiClient;

final class Get
{
    public function __construct(private Client $client)
    {}

    public function get(string $hash):string
    {
        return $this->client->makeRequest
        (
            'GET',
            'get/' . $hash,
            [],
            [],
            Client::EXPECT_RESPONSE_FILE
        );
    }
}