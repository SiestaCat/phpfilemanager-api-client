<?php

namespace Siestacat\PhpfilemanagerApiClient\Tests;

use PHPUnit\Framework\TestCase;
use Siestacat\Phpfilemanager\File\FileCommander;
use Siestacat\PhpfilemanagerApiClient\Client;

class AbstractClientTestCase extends TestCase
{

    protected ?Client $client = null;

    protected function createClient():Client
    {
        return $this->client === null ? new Client($_ENV['API_URL'], $_ENV['API_KEY']) : $this->client;
    }

    protected static function getTestsFilesPath():string
    {
        return __DIR__ . '/tests_files/';
    }

    protected function uploadSingleFile(string $filename):\stdClass
    {
        return $this->createClient()->getUploadClient()->upload(self::getTestsFilesPath() . $filename);
    }

    protected function uploadMultipleFile(array $filesnames):\stdClass
    {
        $files = [];

        foreach($filesnames as $filename)
        {
            $files[] = self::getTestsFilesPath() . $filename;
        }

        return $this->createClient()->getUploadClient()->upload($files);
    }

    protected function deleteFiles(array $hashes, bool $assert = true):\stdClass
    {
        $client = $this->createClient()->getDeleteClient();

        $response = $client->delete($hashes);

        if($assert)
        {
            $this->assertTrue($response->success);
            foreach($hashes as $hash) $this->assertTrue(in_array($hash, $response->deleted_hashes));
        }

        

        return $response;
    }

    protected function getHashesFromUpload(\stdClass $response):array
    {
        $hashes = [];

        foreach($response->files as $file)
        {
            $hashes[] = $file->hash;
        }

        return $hashes;
    }

    protected function assertUploadedFiles(\stdClass $response, ?array $hashes = null):void
    {
        $hashes = $hashes === null ? $this->getHashesFromUpload($response) : $hashes;

        $count_files_gt0 = count($response->files) > 0;

        $this->assertTrue($count_files_gt0);
        
        if($count_files_gt0)
        {
            foreach($hashes as $hash)
            {

                $exists_response = $this->createClient()->getExistsClient()->exists($hash);

                $this->assertTrue($exists_response->success);
                $this->assertTrue($exists_response->exists);

                foreach
                (
                    [
                        Client::EXPECT_RESPONSE_FILE,
                        Client::EXPECT_RESPONSE_BINARY
                    ]
                    as
                    $response_type
                )
                {
                    $get_response = $this->createClient()->getGetFileClient()->get($hash, (Client::EXPECT_RESPONSE_FILE === $response_type));

                    if($response_type === Client::EXPECT_RESPONSE_FILE)
                    {
                        $file_path = $get_response;
                        $local_hash = FileCommander::_hash_file($file_path);
                    }

                    if($response_type === Client::EXPECT_RESPONSE_BINARY)
                    {
                        $local_hash = hash(FileCommander::DEFAULT_HASH_ALGO, $get_response);
                    }

                    $this->assertEquals
                    (
                        $local_hash,
                        $hash
                    );
                }

                

                
            }

            $this->deleteFiles($hashes);

            foreach($hashes as $hash)
            {
                $exists_response = $this->createClient()->getExistsClient()->exists($hash);

                $this->assertTrue($exists_response->success);
                $this->assertFalse($exists_response->exists);
            }
        }
    }
}