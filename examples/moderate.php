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
    $moderateResource = $nullNude->moderate($image);
    
    // Check if the request returned a successful, populated resource.
    if ( $moderateResource->isSuccessful() ) {
        // Check if the image has been moderated,
        // moderate.json moderates the image only
        // if there was nudity present.
        if ( $moderateResource->isModerated() ) {
            // Download and save the moderated image.
            echo 'Moderated image url: ' . 
                 $moderateResource->getModeratedUrl();
        }
    }
    
    // Check if the image has been queued.
    if ( $moderateResource->isQueued() ) {
        // The image has been queued, you should check it later.
    } 
    
    // Check if the request failed.
    if ( $moderateResource->hasFailed() ) {
        // The url might not be an image or there was an API error.
        echo $moderateResource->getErrorMessage();
    }
} catch ( Exception $e ) {
    print_r($e->getMessage());
}