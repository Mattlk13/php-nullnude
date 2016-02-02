<?php
/**
 * Copyright (c) 2015, dNeural.com
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 * 
 * Except as contained in this notice, the name of dNeural and or its trademarks
 * (and among others NullNude) shall not be used in advertising or otherwise to
 * promote the sale, use or other dealings in this Software without prior
 * written authorization from dNeural.
 */

namespace NullNude;

use NullNude\NullNude;
use NullNude\NullNudeCurl;
use NullNude\Resources\NullNudeRoi;
use NullNude\Resources\NullNudeNudity;
use NullNude\Resources\NullNudeModerate;
use NullNude\Exceptions\NullNudeException;

/**
 * Class NullNudeClient
 *
 * @package NullNude
 */
class NullNudeClient
{
    /**
     * @const int The timeout in seconds for a normal request.
     */
    const REQUEST_TIMEOUT = 30;

    /**
     * @const int The timeout in seconds for a request that contains file uploads.
     */
    const FILE_REQUEST_TIMEOUT = 180;
    
    /**
     * @const string API incorrect key error code.
     */
    const INCORRECT_API_KEY = 3;
    
    /**
     * @const string API incorrect key or secret error code.
     */
    const INCORRECT_API_KEY_OR_SECRET = 4;
    
    /**
     * @var string|boolean Response from the server
     */
    protected $response;

    /**
     * @var NullNudeCurl A NullNudeCurl object
     */
    protected $nullNudeCurl;
    
    /**
     * @var string User api_key.
     */
    protected $api_key;
    
    /**
     * @var string User api_secret.
     */
    protected $api_secret;
    
    /**
     * Creates a new NullNudeClient entity.
     * 
     * @param string $apiKey
     * @param string $apiSecret
     * @param NullNudeCurl $httpClient
     */
    public function __construct($apiKey, $apiSecret, NullNudeCurl $httpClient)
    {
        $this->api_key = $apiKey;
        $this->api_secret = $apiSecret;
        $this->nullNudeCurl = $httpClient;
    }

    /**
     * Sends the request to the API endpoint, 
     * returns a NullNude Resource object.
     * 
     * @param string $method
     * @param string $endpoint
     * @param string $url
     * @param array $postfields
     * 
     * @return NullNudeRoi|NullNudeNudity|NullNudeModerate
     * 
     * @throws NullNudeException
     */
    public function send($method, $endpoint, $url, $postfields=false)
    {
        $this->connect($method, $endpoint, $url, $postfields);
        $this->sendRequest();
        
        $errorCode = $this->nullNudeCurl->errno();
        if ( $errorCode ) {
            throw new NullNudeException($this->nullNudeCurl->error(), $errorCode);
        }

        $this->close();
        
        return $this->getResponseResource($this->response, $endpoint);
    }

    /**
     * Sets curl connection option and initializes the connection.
     * 
     * @param string $method
     * @param string $endpoint
     * @param string $image
     * @param array $postfields
     */
    protected function connect($method, $endpoint, $image, $postfields=false)
    {
        $timeout = self::REQUEST_TIMEOUT;
        if ( $method === 'POST' ) {
            $timeout = self::FILE_REQUEST_TIMEOUT;
        }
        
        $curlOptions = array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT => $timeout,
            CURLOPT_URL => $this->getRequestUrl($method, $endpoint, $image),
            CURLOPT_USERAGENT => 'NullNude php SDK cURL Request',
            CURLOPT_CAINFO => __DIR__ . '/certs/DstRootCaX3.pem'
        );

        if ( $method === "POST" ) {
            $postfields['api_key'] = $this->api_key;
            $postfields['api_secret'] = $this->api_secret;
            
            $curlOptions[CURLOPT_POST] = true;
            $curlOptions[CURLOPT_POSTFIELDS] = $postfields;
        }

        $this->nullNudeCurl->init();
        $this->nullNudeCurl->setoptArray($curlOptions);
    }

    /**
     * Closes curl connection
     */
    protected function close()
    {
        $this->nullNudeCurl->close();
    }

    /**
     * Send the request.
     */
    protected function sendRequest()
    {
        $this->response = $this->nullNudeCurl->exec();
    }
    
    /**
     * Returns a formated API request url.
     * 
     * @param string $method
     * @param string $endpoint
     * @param string $image
     * 
     * @return string
     */
    protected function getRequestUrl($method, $endpoint, $image) {
        $apiUrl = NullNude::API_URL . NullNude::API_VERSION . '/' . $endpoint;
        
        if ( $method === 'GET' ) {
            $query = [
                'api_key' => $this->api_key,
                'api_secret' => $this->api_secret,
                'url' => $image
            ];
            
            $apiUrl .= '?' .  http_build_query($query);
        }
        
        return $apiUrl;
    }
    
    /**
     * Returns a NullNude Resource object.
     * 
     * @param type $jsonResponse
     * @param type $endpoint
     * 
     * @return NullNudeRoi|NullNudeNudity|NullNudeModerate
     * 
     * @throws NullNudeException
     */
    protected function getResponseResource($jsonResponse, $endpoint) {
        $response = json_decode($jsonResponse, true);
        
        if ( null === $response ) {
            throw new NullNudeException("API returned an invalid JSON response.");
        }
        
        if ( $response['status'] === 'failure' && 
             ($response['error_code'] === self::INCORRECT_API_KEY 
             || $response['error_code'] === self::INCORRECT_API_KEY_OR_SECRET) ) {
            throw new NullNudeException(
                $response['error_message'],
                $response['error_code']
            );
        }
        
        switch ( $endpoint ) {
            case 'nudity.json':
                return new NullNudeNudity($response, $jsonResponse);
            case 'moderate.json':
                return new NullNudeModerate($response, $jsonResponse);
            case 'roi.json':
                return new NullNudeRoi($response, $jsonResponse);
        }
        // @codeCoverageIgnoreStart
    }
}
