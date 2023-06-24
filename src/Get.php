<?php

namespace Siestacat\PhpfilemanagerApiClient;

final class Get
{
    public function __construct(private Client $client)
    {}

    public function get(string $hash, bool $get_tmp_file = true):string
    {
        return $this->client->makeRequest
        (
            'GET',
            'get/' . $hash,
            [],
            [],
            (
                $get_tmp_file ? Client::EXPECT_RESPONSE_FILE : Client::EXPECT_RESPONSE_BINARY
            )
        );
    }
}