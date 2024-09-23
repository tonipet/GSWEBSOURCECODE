<?php

$ch = curl_init();
$parameters = array(
    'apikey' => '7b7c346a982f04cb7ac1ca04996abd00', //Your API KEY
    'number' => '09151793391',
    'message' => 'Hello how are you',
    'sendername' => 'SEMAPHORE'
);
curl_setopt( $ch, CURLOPT_URL,'https://semaphore.co/api/v4/messages' );
curl_setopt( $ch, CURLOPT_POST, 1 );

//Send the parameters set above with the request
curl_setopt( $ch, CURLOPT_POSTFIELDS, http_build_query( $parameters ) );

// Receive response from server
curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
$output = curl_exec( $ch );
curl_close ($ch);

//Show the server response
echo $output;

?>