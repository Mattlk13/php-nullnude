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

use NullNude\Resources\NullNudeResource;

/**
 * Class NullNudeModerate
 *
 * @package NullNude
 */
class NullNudeModerate extends NullNudeResource {
    
    /**
     * @var boolean|null Is the image moderated. 
     */
    protected $moderated;
    
    /**
     * @var string|null Moderated image url.
     */
    protected $moderatedUrl;
    
    /**
     * Creates a new NullNudeModerate object.
     * 
     * @param array $response
     * @param string $jsonResponse
     */
    public function __construct($response, $jsonResponse) {
        parent::__construct($response, $jsonResponse);
        
        $this->populateResource($response);
    }
    
    /**
     * Returns boolean value stating if the image has been moderated,
     * moderate.json moderates the image only if there was nudity in it.
     * 
     * @return boolean
     */
    public function isModerated() {
        return $this->moderated;
    }
    
    /**
     * Returns the moderated image url if there was nudity
     * present in it.
     * 
     * @return string|null Moderated image url.
     */
    public function getModeratedUrl() {
        return $this->moderatedUrl;
    }
    
    /**
     * Populates the resource object.
     * 
     * @param array $response
     * 
     * @return boolean|null
     */
    protected function populateResource($response) {
        parent::populateResource($response);
        
        if ( $this->status !== 'success' ) {
            return false;
        }
        
        $this->moderated = $this->responseData['moderated']['result'];
        if ( isset($this->responseData['moderated']['url']) ) {
            $this->moderatedUrl = $this->responseData['moderated']['url'];
        }
    }
}