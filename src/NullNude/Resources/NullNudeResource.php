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

namespace NullNude\Resources;

/**
 * Class NullNudeResource
 *
 * @package NullNude
 */
class NullNudeResource {
    
    /**
     * @var string The response from the API in json format. 
     */
    protected $jsonResponse;
    
    /**
     * @var array The response from the API in an array format. 
     */
    protected $response;
    
    /**
     * @var array The requested API response data.
     */
    protected $responseData;
    
    /**
     * @var string An url to the photo that this resource was build for.
     */
    protected $imageUrl;
    
    /**
     * @var string The status of the API call. 
     */
    protected $status;
    
    /**
     * @var string Error message returned if the API call was unsuccessful.
     */
    protected $errorMessage;
    
    /**
     * @var int Error code returned if the API call was unsuccessful.
     */
    protected $errorCode;
    
    /**
     * Creates a new NullNudeResource object.
     * 
     * @param array $response
     * @param string $jsonResponse
     */
    public function __construct($response, $jsonResponse) {
        $this->response = $response;
        $this->jsonResponse = $jsonResponse;
    }
    
    /**
     * Populates the reource object.
     * 
     * @param array $response
     * 
     * @return null|false
     */
    protected function populateResource($response) {
        $this->status = $response['status'];
        
        if ( $this->status === 'failure' ) {
            $this->errorMessage = $response['error_message'];
            $this->errorCode = (int)$response['error_code'];
            
            return;
        }
        
        $this->responseData = $this->response['data'];
        $this->imageUrl = $this->response['url'];
    }
    
    /**
     * Determines if the request to the API was successful.
     * 
     * @return boolean
     */
    public function isSuccessful() {
        if ( $this->getStatus() === 'success' ) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Determins if the request to the API has been queued.
     * 
     * @return boolean
     */
    public function isQueued() {
        if ( $this->getStatus() === 'queued' ) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Determins if the request to the API has failed.
     * 
     * @return boolean
     */
    public function hasFailed() {
        if ( !$this->isSuccessful() && !$this->isQueued() ) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Returns the request status.
     * 
     * @return string
     */
    public function getStatus() {
        return $this->status;
    }
    
    /**
     * Returns the request response in an array format.
     * 
     * @return array
     */
    public function getResponse() {
        return $this->response;
    }
    
    /**
     * Returns the request response in a JSON format.
     * 
     * @return string
     */
    public function getJsonResponse() {
        return $this->jsonResponse;
    }
    
    /**
     * Returns the request response data.
     * 
     * @return array
     */
    public function getResponseData() {
        return $this->responseData;
    }
    
    /**
     * Returns the image url that this reosurce has been build for.
     * 
     * @return string
     */
    public function getImageUrl() {
        return $this->imageUrl;
    }
    
    /**
     * Returns the error message if the API call was unsuccessful.
     * 
     * @return string
     */
    public function getErrorMessage() {
        return $this->errorMessage;
    }
    
    /**
     * Returns the error code if the API call was unsuccessful.
     * 
     * @return int
     */
    public function getErrorCode() {
        return $this->errorCode;
    }
}