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
 * Class NullNudeRoi
 *
 * @package NullNude
 */
class NullNudeRoi extends NullNudeResource {
    
    /**
     * @var array Regions of interest within the photo. 
     */
    protected $roi;
    
    /**
     * @var array Category tag names.
     */
    protected $category = [
        'n00000001' => 'Rear',
        'n00000002' => 'Breasts',
        'n00000003' => 'Penis',
        'n00000004' => 'Female crotch',
        'n00000005' => 'Foot',
        'n00000006' => 'Stockings',
        'n00000007' => 'Bondage',
        'n00000008' => 'Dildo',
        'n00000009' => 'Fellatio',
        'n00000010' => 'Intercourse',
        'n00000011' => 'Breast',
        'n00000012' => 'Stocking',
        'n00000013' => 'Feet',
        'n00000014' => 'Anus',
        'n00000015' => 'Naked leg',
        'n00000016' => 'Naked legs',
        'n00000017' => 'Cunnilingus',
        'n00000018' => 'Masturbation',
        'n00000019' => 'Nipple',
        'n00000020' => 'Sperm',
        'n00000021' => 'Vagina',
        'n00000022' => 'Stocking band',
        'n00000023' => 'Underwear',
        'n00000024' => 'Bra',
        'n00000025' => 'Mouth',
        'n00000026' => 'Belly button',
    ];
    
    /**
     * Creates a new NullNudeRoi resource object.
     * 
     * @param array $response
     * @param string $jsonResponse
     */
    public function __construct($response, $jsonResponse) {
        parent::__construct($response, $jsonResponse);
        
        $this->populateResource($response);
    }
    
    /**
     * Returns the regions of interest within the image.
     * 
     * @return array
     */
    public function getRoi() {
        return $this->roi;
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
        
        $this->roi = $this->responseData['roi'];
        foreach ( $this->roi as $key => $roi ) {
            $this->roi[$key]['category'] = $this->category[$roi['category']];
        }
    }
}