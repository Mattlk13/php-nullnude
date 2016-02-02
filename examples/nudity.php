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
    $image = "image_path_or_url";
    $nudityResource = $nullNude->checkNudity($image);
    
    // Check if the request returned a successful, populated resource.
    if ( $nudityResource->isSuccessful() ) {
        // Check if the image has nuidty in it.
        if ( $nudityResource->hasNudity() ) {
            // Take action based on your confidence preference.
            echo 'Image nudity confidence: ' . 
                 $nudityResource->getNudityConfidence();
        }
        
        // Check if the image has covered nuidty in it.
        if ( $nudityResource->hasCoveredNudity() ) {
            // Take action based on your confidence preference.
            echo '<br> Image covered nudity confidence: ' . 
                 $nudityResource->getCoveredNudityConfidence();
        }
    }
    
    // Check if the image has been queued.
    if ( $nudityResource->isQueued() ) {
        // The image has been queued, you should check it later.
    } 
    
    // Check if the request failed.
    if ( $nudityResource->hasFailed() ) {
        // The url might not be an image or there was an API error.
        echo $nudityResource->getErrorMessage();
    }
} catch ( Exception $e ) {
    print_r($e->getMessage());
}