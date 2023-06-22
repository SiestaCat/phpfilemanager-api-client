<?php

namespace Siestacat\PhpfilemanagerApiClient;

use Siestacat\PhpfilemanagerApiClient\Exception\ApiNotSuccessException;
use Siestacat\PhpfilemanagerApiClient\Exception\CurlFailedException;
use Siestacat\PhpfilemanagerApiClient\Exception\CurlInitException;
use Siestacat\PhpfilemanagerApiClient\Exception\FilePathNotExistsException;
use Siestacat\PhpfilemanagerApiClient\Exception\JsonDecodeFailedException;
use Siestacat\PhpfilemanagerApiClient\Exception\MethodPostRequiredException;

final class Client
{

    const POST_METHODS = ['POST'];

    const EXPECT_RESPONSE_JSON = 1;
    const EXPECT_RESPONSE_FILE = 2;

    protected ?string $url = null;

    protected bool $ssl_verify = true;

    public function __construct(string $url, protected string $apikey)
    {
        $url = trim($url);
        $this->url = $url . (substr($url, -1) !== '/' ? '/' : null);
    }

    public function setSslVerify(bool $ssl_verify):self
    {

        $this->ssl_verify = $ssl_verify;

        return $this;
    }

    public function makeRequest(string $method, string $uri, array $parameters = [], array $files = [], int $expect_response = self::EXPECT_RESPONSE_JSON):\stdClass|string
    {

        //Prepare url string

        $get_params = [
            'apikey' => $this->apikey
        ];

        if(!self::isPostMethod($method)) $get_params = array_merge($get_params, $parameters);

        $url = $this->url . $uri;

        $url .= '?' . http_build_query($get_params);

        //Init curl

        $ch = curl_init($url);
        
        if($ch === false) throw new CurlInitException;

        //Prepare files

        $this->prepareCurlFiles($method, $files, $parameters);

        //Curl options

        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, $this->ssl_verify?2:0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->ssl_verify?2:0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        
        if(self::isPostMethod($method))
        {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $parameters);
        }

        //On expect response file

        $fp = null;

        if($expect_response === self::EXPECT_RESPONSE_FILE)
        {
            $tmp_file_path = tempnam(sys_get_temp_dir(), hash('md5', random_bytes(32)));
            $fp = fopen ($tmp_file_path, 'w+');
            curl_setopt($ch, CURLOPT_FILE, $fp); 
        }
        
        //Get response and check

        $response = curl_exec($ch);

        /**
         * @var int
         */
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if
        (
            !(
                $http_code === 200 &&
                (
                    (
                        $expect_response !== self::EXPECT_RESPONSE_FILE &&
                        $response !== false && $response !== ''
                    ) || $expect_response === self::EXPECT_RESPONSE_FILE
                )
            )
        )
        {
            throw new CurlFailedException($response, $http_code);
        }

        if($expect_response === self::EXPECT_RESPONSE_JSON)
        {
            return $this->checkJsonResponse($response);
        }

        //Close curl

        curl_close($ch);

        if($expect_response === self::EXPECT_RESPONSE_FILE)
        {
            fclose($fp);
            return $tmp_file_path;
        }

        
    }

    private function checkJsonResponse(mixed $response):\stdClass
    {
        $json = json_decode($response, false);

        if($json === false || $json === null) throw new JsonDecodeFailedException($response);

        if($json->success === false) throw new ApiNotSuccessException($json);

        return $json;
    }

    private function prepareCurlFiles(string $method, array $files, array &$parameters):void
    {
        if(count($files) > 0 && $method !== 'POST') throw new MethodPostRequiredException;

        foreach($files as $index => $path)
        {
            if(!is_file($path)) throw new FilePathNotExistsException($path);

            $file_parameter = 'files[' . $index . ']';

            $parameters[$file_parameter] = curl_file_create($path);
        }
    }

    private static function isPostMethod(string $method):bool
    {
        return in_array($method, self::POST_METHODS);
    }

    public function getUploadClient():Upload
    {
        return new Upload($this);
    }

    public function getGetFileClient():Get
    {
        return new Get($this);
    }

    public function getExistsClient():Exists
    {
        return new Exists($this);
    }

    public function getDeleteClient():Delete
    {
        return new Delete($this);
    }
}