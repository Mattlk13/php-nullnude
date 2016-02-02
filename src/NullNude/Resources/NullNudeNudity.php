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
 * Class NullNudeNudity
 *
 * @package NullNude
 */
class NullNudeNudity extends NullNudeResource {
    /**
     * @var boolean Does the image contain nudity.
     */
    protected $nudity;
    
    /**
     * @var boolean Does the image contain covered nudity.
     */
    protected $coveredNudity;
    
    /**
     * @var float Nudity condidence between 0 and 1.
     */
    protected $nudityConfidence;
    
    /**
     * @var float Covered nudity confidence between 0 and 1. 
     */
    protected $coveredNudityConfidence;
    
    /**
     * Creates a new NullNudeNudity resource object.
     * 
     * @param array $response
     * @param string $jsonResponse
     */
    public function __construct($response, $jsonResponse) {
        parent::__construct($response, $jsonResponse);
        
        $this->populateResource($response);
    }
    
    /**
     * Does the image contain nudity.
     * 
     * @return boolean
     */
    public function hasNudity() {
        return $this->nudity;
    }
    
    /**
     * Does the image contain covered nudity.
     * 
     * @return boolean
     */
    public function hasCoveredNudity() {
        return $this->coveredNudity;
    }
    
    /**
     * Returns the nudity confidence.
     * 
     * @return float
     */
    public function getNudityConfidence() {
        return $this->nudityConfidence;
    }
    
    /**
     * Returns the covered nudity confidence.
     * 
     * @return float
     */
    public function getCoveredNudityConfidence() {
        return $this->coveredNudityConfidence;
    }
    
    /**
     * Populates the resource.
     * 
     * @param array $response
     * 
     * @return false
     */
    protected function populateResource($response) {
        parent::populateResource($response);
        
        if ( $this->status !== 'success' ) {
            return false;
        }
        
        $this->nudity = $this->responseData['nudity']['result'];
        $this->nudityConfidence = $this->responseData['nudity']['confidence'];
        $this->coveredNudity = $this->responseData['covered_nudity']['result'];
        $this->coveredNudityConfidence = $this->responseData['covered_nudity']['confidence'];
    }
}