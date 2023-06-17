<?php

namespace Siestacat\PhpfilemanagerApiClient\Tests;

use Siestacat\Phpfilemanager\File\FileCommander;

class FullTest extends AbstractClientTestCase
{
    public function testFull()
    {

        $filesnames = [];

        foreach(scandir(self::getTestsFilesPath()) as $filename)
        {
            if(in_array($filename, ['.', '..'])) continue;

            $filesnames[] = $filename;

        }

        $response = $this->uploadMultipleFile($filesnames);

        $hashes = $this->getHashesFromUpload($response);

        $this->assertUploadedFiles($response, $hashes);

        
    }
}