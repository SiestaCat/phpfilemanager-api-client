<?php

namespace Siestacat\PhpfilemanagerApiClient;

final class Delete
{
    public function __construct(private Client $client)
    {}

    public function delete(mixed $hashes):\stdClass
    {
        $hashes = is_array($hashes) ? $hashes : [$hashes];

        return $this->client->makeRequest
        (
            'DELETE',
            'delete',
            [
                'hashes' => $hashes
            ]
        );
    }
}