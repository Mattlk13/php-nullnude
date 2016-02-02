<?php
require __DIR__ . '/vendor/autoload.php';
use NullNude\NullNude;

// Information about your API access.
$config = [
    'api_key'    => 'YOUR_API_KEY',
    'api_secret' => 'YOUR_API_SECRET'
];

// Initialize the NullNude client.
$nullNude = new NullNude($config);

try {
    // Image can be either a local path or an external url.
    $image =  "image_path_or_url";
    $roiResource = $nullNude->getRoi($image);
    
    // Check if the request returned a successful, populated resource.
    if ( $roiResource->isSuccessful() ) {
        // Get the array of regions of interest within the image.
        print_r($roiResource->getRoi());
    }
    
    // Check if the image has been queued.
    if ( $roiResource->isQueued() ) {
        // The image has been queued, you should check it later.
    } 
    
    // Check if the request failed.
    if ( $roiResource->hasFailed() ) {
        // The url might not be an image or there was an API error.
        echo $roiResource->getErrorMessage();
    }
} catch ( Exception $e ) {
    print_r($e->getMessage());
}