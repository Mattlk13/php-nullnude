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
namespace NullNude\Test;

use NullNude\NullNude;

class NullNudeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    private $api_key;
    /**
     * @var string
     */
    private $api_secret;
    
    /**
     * @var string
     */
    private $image;
    
    /**
     * @var string
     */
    private $imagePath;
    
    /**
     * Set up the test.
     */
    protected function setUp()
    {
        $this->api_key    = 'app_key';
        $this->api_secret = 'app_secret';
        $this->image = 'http://example.com/image.jpg';
        $this->imagePath = __DIR__ . './test_file.txt';
    }
    
    /**
     * Test Client creation.
     */
    public function testClientCreation()
    {
        $nullNude = new NullNude([
            'api_key'    => $this->api_key, 
            'api_secret' => $this->api_secret
        ]);
        $this->assertInstanceOf('\\NullNude\\NullNudeClient', $nullNude->getClient());
    }
    
    /**
     * Test missing API key.
     */
    public function testMissingApiKey()
    {
        $this->setExpectedException('\\NullNude\\Exceptions\\NullNudeException', 
                                    'Required "api_key" not supplied in config.');

        new NullNude([
            'api_secret' => $this->api_secret
        ]);
    }
    
    /**
     * Test missing API secret.
     */
    public function testMissingApiSecret()
    {
        $this->setExpectedException('\\NullNude\\Exceptions\\NullNudeException', 
                                    'Required "api_secret" not supplied in config.');
        new NullNude([
            'api_key' => $this->api_key
        ]);
    }
    
    /**
     * Test invalid image.
     */
    public function testInvalidImage()
    {
        $this->setExpectedException('\\NullNude\\Exceptions\\NullNudeException', 
                                    'The image path supplied is neither a valid '. 
                                    'url nor an existing file path.');
        $nullNude = new NullNude([
            'api_key'    => $this->api_key, 
            'api_secret' => $this->api_secret
        ]);
        
        $nullNude->checkNudity('not_a_real_file_nor_image_url');
    }
    
    /**
     * Test checkNudity.
     */
    public function testCheckNudity()
    {
        $exec = 
        '{
            "status": "success",
            "url": "'.$this->image.'",
            "data": {
                "covered_nudity": {
                    "confidence": 1.0,
                    "result": true
                },
                "nudity": {
                    "confidence": 1.0,
                    "result": true
                }
            }
        }';
        $http = $this->getHttpMock($exec);
    
        $nullNude = new NullNude([
            'api_key'    => $this->api_key, 
            'api_secret' => $this->api_secret
        ], $http);
        
        $nudityResource = $nullNude->checkNudity($this->image);
        
        $this->assertInstanceOf('\\NullNude\\Resources\\NullNudeNudity', 
                                $nudityResource);
        $this->assertTrue($nudityResource->hasNudity());
        $this->assertTrue($nudityResource->hasCoveredNudity());
        $this->assertEquals(1.0, $nudityResource->getNudityConfidence());
        $this->assertEquals(1.0, $nudityResource->getCoveredNudityConfidence());
        
        $this->assertEquals('success', $nudityResource->getStatus());
        $this->assertEquals($this->image, $nudityResource->getImageUrl());
        $this->assertTrue($nudityResource->isSuccessful());
        $this->assertFalse($nudityResource->isQueued());
        $this->assertFalse($nudityResource->hasFailed());
        $this->assertNull($nudityResource->getErrorMessage());
        $this->assertNull($nudityResource->getErrorCode());
        
        $response = json_decode($exec, true);
        $this->assertEquals($exec, $nudityResource->getJsonResponse());
        $this->assertEquals($response, $nudityResource->getResponse());
        $this->assertEquals($response['data'], $nudityResource->getResponseData());
    }
    
    /**
     * Test moderate.
     */
    public function testModerate()
    {
        $exec = 
        '{
            "status": "success",
            "url": "'.$this->image.'",
            "data": {
                "moderated": {
                    "url": "https://storage.dneural.com/image.jpg",
                    "result": true
                }
            }
        }';
        $http = $this->getHttpMock($exec);
    
        $nullNude = new NullNude([
            'api_key'    => $this->api_key, 
            'api_secret' => $this->api_secret
        ], $http);
        
        $moderateResource = $nullNude->moderate($this->image);
        
        $this->assertInstanceOf('\\NullNude\\Resources\\NullNudeModerate', 
                                $moderateResource);
        $this->assertTrue($moderateResource->isModerated());
        $this->assertEquals("https://storage.dneural.com/image.jpg", 
                            $moderateResource->getModeratedUrl());
    }
    
    /**
     * Test getRoi.
     */
    public function testGetRoi()
    {
        $exec = 
        '{
            "status": "success",
            "url": "'.$this->image.'",
            "data": {
                "roi": [
                    {
                        "category": "n00000001",
                        "confidence": "1.0",
                        "y2": 10,
                        "x2": 10,
                        "y1": 1,
                        "x1": 1
                    },
                    {
                        "category": "n00000002",
                        "confidence": "1.0",
                        "y2": 20,
                        "x2": 20,
                        "y1": 15,
                        "x1": 15
                    }
                ]
            }
        }';
        $http = $this->getHttpMock($exec);
    
        $nullNude = new NullNude([
            'api_key'    => $this->api_key, 
            'api_secret' => $this->api_secret
        ], $http);
        
        $roiResource = $nullNude->getRoi($this->image);
        $this->assertInstanceOf('\\NullNude\\Resources\\NullNudeRoi', 
                                $roiResource);
        
        $rois = $roiResource->getRoi();
        
        $this->assertEquals('Rear', $rois[0]['category']);
        $this->assertEquals('Breasts', $rois[1]['category']);
    }
    
    /**
     * Test post image.
     */
    public function testPostImage()
    {
        $exec = 
        '{
            "status": "success",
            "url": "'.$this->image.'",
            "data": {
                "moderated": {
                    "url": "https://storage.dneural.com/image.jpg",
                    "result": true
                }
            }
        }';
        $http = $this->getHttpMock($exec);
    
        $nullNude = new NullNude([
            'api_key'    => $this->api_key, 
            'api_secret' => $this->api_secret
        ], $http);
        
        $moderateResource = $nullNude->moderate($this->imagePath);
        
        $this->assertTrue($moderateResource->isModerated());
    }
    
    /**
     * Test invalid image.
     */
    public function testInvalidImageSent()
    {
        $exec = 
        '{  
            "status":"failure",
            "error_message":"Could not download photo, got HTTP error '.
                '\"HTTP 404: Not Found\" from the server hosting the photo.",
            "error_code":13
         }';
        $http = $this->getHttpMock($exec);
    
        $nullNude = new NullNude([
            'api_key'    => $this->api_key, 
            'api_secret' => $this->api_secret
        ], $http);
        
        $moderateResource = $nullNude->moderate($this->image);
        
        $this->assertTrue($moderateResource->hasFailed());
        $this->assertEquals(13, $moderateResource->getErrorCode());
        $this->assertEquals('Could not download photo, got HTTP error "HTTP '.
                            '404: Not Found" from the server hosting the photo.', 
                            $moderateResource->getErrorMessage());
    }
    
    /**
     * Test queued request.
     */
    public function testQueuedRequest()
    {
        $exec = 
        '{  
            "status":"queued",
            "url": "'.$this->image.'",
            "data": {}
         }';
        $http = $this->getHttpMock($exec);
    
        $nullNude = new NullNude([
            'api_key'    => $this->api_key, 
            'api_secret' => $this->api_secret
        ], $http);
        
        $moderateResource = $nullNude->moderate($this->image);
        
        $this->assertTrue($moderateResource->isQueued());
    }
    
    /**
     * Test failed request.
     */
    public function testFailedRequest()
    {
        $exec = 
        '{  
            "status":"failure",
            "error_message":"Failed. request.",
            "error_code":12345
         }';
        $http = $this->getHttpMock($exec);
    
        $nullNude = new NullNude([
            'api_key'    => $this->api_key, 
            'api_secret' => $this->api_secret
        ], $http);
        
        $nudityResource = $nullNude->checkNudity($this->image);
        $roiResource = $nullNude->getRoi($this->image);
        
        $this->assertTrue($nudityResource->hasFailed());
        $this->assertTrue($roiResource->hasFailed());
    }
    
    /**
     * Test invalid api key/secret.
     */
    public function testInvalidApiKeySecret()
    {
        $this->setExpectedException('\\NullNude\\Exceptions\\NullNudeException', 
                                    'Incorrect API user key');
        $exec = 
        '{  
            "status":"failure",
            "error_message":"Incorrect API user key",
            "error_code":3
         }';
        $http = $this->getHttpMock($exec);
    
        $nullNude = new NullNude([
            'api_key'    => $this->api_key, 
            'api_secret' => $this->api_secret
        ], $http);
        
        $nullNude->checkNudity($this->image);
    }
    
    /**
     * Test API returns no response.
     */
    public function testApiNoResponse()
    {
        $this->setExpectedException('\\NullNude\\Exceptions\\NullNudeException', 
                                    'API returned an invalid JSON response.');
        $exec = null;
        $http = $this->getHttpMock($exec);
    
        $nullNude = new NullNude([
            'api_key'    => $this->api_key, 
            'api_secret' => $this->api_secret
        ], $http);
        
        $nullNude->checkNudity($this->image);
    }
    
    /**
     * Test Curl error.
     */
    public function testCurlError()
    {
        $this->setExpectedException('\\NullNude\\Exceptions\\NullNudeException',
                                    'Curl error.');
        $exec = 
        '{  
            "status":"failure"
         }';
        $http = $this->getHttpMock($exec, 12345, 'Curl error.');
    
        $nullNude = new NullNude([
            'api_key'    => $this->api_key, 
            'api_secret' => $this->api_secret
        ], $http);
        
        $nullNude->checkNudity($this->image);
    }
    
    /*
     * Get mock httpClient.
     */
    private function getHttpMock($exec, $errorno = null, $error = null) {
        $http = $this->getMockBuilder('\\NullNude\\NullNudeCurl')->getMock();
        
        $http->expects($this->any())->method('init')
                                    ->will($this->returnValue(TRUE));
        $http->expects($this->any())->method('setoptArray')
                                    ->will($this->returnValue(TRUE));
        $http->expects($this->any())->method('errno')
                                    ->will($this->returnValue($errorno));
        $http->expects($this->any())->method('error')
                                    ->will($this->returnValue($error));
        $http->expects($this->any())->method('close')
                                    ->will($this->returnValue(TRUE));
        $http->expects($this->any())->method('exec')
                                    ->will($this->returnValue($exec));
        
        return $http;
    }
}