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

use NullNude\NullNudeClient;
use NullNude\NullNudeCurl;
use NullNude\Exceptions\NullNudeException;

/**
 * Class NullNude
 *
 * @package NullNude
 */
class NullNude
{
    /**
     * @const string Version number.
     */
    const VERSION = '1.0.0';

    /**
     * @const string API version.
     */
    const API_VERSION = '1.0';
    
    /**
     * @const string API base url.
     */
    const API_URL = "https://api.dneural.com/";
    
    /**
     * @var NullNudeClient NullNude http client.
     */
    protected $client;
    
    /**
     * @var string User api_key.
     */
    protected $api_key;
    
    /**
     * @var string User api_secret.
     */
    protected $api_secret;
    
    /**
     * Instantiates a new NullNude object.
     *
     * @param array $config
     * @param NullNudeCurl $httpClient
     *
     * @throws NullNudeException
     */
    public function __construct(array $config = [], NullNudeCurl $httpClient = null)
    {
        $config = array_merge([
            'api_key' => false,
            'api_secret' => false
        ], $config);

        if ( !$config['api_key'] ) {
            throw new NullNudeException('Required "api_key" not supplied ' .
                                        'in config.');
        }
        if ( !$config['api_secret'] ) {
            throw new NullNudeException('Required "api_secret" not ' .
                                        'supplied in config.');
        }
        
        $this->api_key = $config['api_key'];
        $this->api_secret = $config['api_secret'];
        
        if ( !extension_loaded('curl') ) {
            // @codeCoverageIgnoreStart
            throw new NullNudeException('The cURL extension must be loaded ' .
                                        'to use the NullNude SDK.');
            // @codeCoverageIgnoreEnd
        }
        
        if ( !$httpClient ) {
            $httpClient = new NullNudeCurl();
        }
        
        $this->client = new NullNudeClient($this->api_key, $this->api_secret, 
                                           $httpClient);
    }
    
    /**
     * Sends a request to the API to check if the 
     * supplied image has nudity in it.
     * 
     * @param string $image Either an image url or a local path
     * 
     * @return \NullNude\Resources\NullNudeNudity
     */
    public function checkNudity($image) {
        return $this->sendRequest('nudity.json', $image);
    }
    
    /**
     * Sends a request to the API to obtain regions of interest
     * in the supplied image.
     * 
     * @param string $image Either an image url or a local path
     * 
     * @return \NullNude\Resources\NullNudeRoi
     */
    public function getRoi($image) {
        return $this->sendRequest('roi.json', $image);
    }
    
    /**
     * Sends a request to the API to moderate the supplied image
     * if it has nudity in it.
     * 
     * @param string $image Either an image url or a local path
     * 
     * @return \NullNude\Resources\NullNudeModerate
     */
    public function moderate($image) {
        return $this->sendRequest('moderate.json', $image);
    }
    
    /**
     * Get the NullNude client.
     * 
     * @return \NullNude\NullNudeClient
     */
    public function getClient() {
        return $this->client;
    }
    
    /**
     * Send the request to the API.
     * 
     * @param string $endpoint
     * @param string $image Either an image url or a local path
     * 
     * @return mixed
     */
    protected function sendRequest($endpoint, $image) {
        $method = $this->getRequestMethod($image);
        
        if ( $method === 'POST' ) {
            return $this->post($method, $endpoint, $image);
        }
        
        return $this->get($method, $endpoint, $image);
    }
    
    /**
     * Send a get request to the API.
     * 
     * @param string $method
     * @param string $endpoint
     * @param string $image Either an image url or a local path
     * 
     * @return mixed
     */
    protected function get($method, $endpoint, $image) {
        return $this->client->send($method, $endpoint, $image);
    }
    /**
     * Send a post request to the API.
     * 
     * @param string $method
     * @param string $endpoint
     * @param string $image Either an image url or a local path
     * 
     * @return mixed
     */
    protected function post($method, $endpoint, $image) {
        $postfields = [
            'photo' => '@'.realpath($image)
        ];
        
        return $this->client->send($method, $endpoint, $image, $postfields);
    }
    
    /**
     * Returns the request method based on the string supplied as the 
     * image variable, checking if the string is a url or a local path.
     * 
     * @param string $image Either an image url or a local path
     * 
     * @return string
     * 
     * @throws NullNudeException
     */
    protected function getRequestMethod($image) {
        if ( filter_var($image, FILTER_VALIDATE_URL, 
                        FILTER_FLAG_SCHEME_REQUIRED) ) {
            $method = 'GET';
        } else if (file_exists($image) ) {
            $method = 'POST';
        } else {
            throw new NullNudeException('The image path supplied is neither a ' .
                                        'valid url nor an existing file path.');
        }
        
        return $method;
    }
}