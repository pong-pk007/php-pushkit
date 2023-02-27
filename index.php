<?php
require 'vendor/autoload.php';

use \Pushok\AuthProvider;
use \Pushok\Client;
use \Pushok\Notification;
use \Pushok\Payload;
use \Pushok\Payload\Alert;

$options = [
    'key_id' => 'XXXXXXX', // The Key ID obtained from Apple developer account
    'team_id' => 'YYYYYYY', // The Team ID obtained from Apple developer account
    'app_bundle_id' => 'your.app.packageName', // The bundle ID for app obtained from Apple developer account
    'private_key_path' => __DIR__ . '/certs/AuthKey_XXXXXXYYYYYY.p8', // Path to private key
    'private_key_secret' => null // Private key secret [optional]
];

// Be aware of thing that Token will stale after one hour, so you should generate it again.
// Can be useful when trying to send pushes during long-running tasks
$authProvider = AuthProvider\Token::create($options);

$alert = Alert::create();
// $alert = $alert->setBody('First push notification');

$payload = Payload::create()->setAlert('Pongsathon Call!');
$payload->setPushType('voip');

//set notification sound to default
// $payload->setSound('default');

//add custom value to your notification, needs to be customized
$payload->setCustomValue('id', '44d915e1-5ff4-4bed-bf13-c423048ec97a');
$payload->setCustomValue('nameCaller', 'Pongsathon');
$payload->setCustomValue('handle', '0123456789');
$payload->setCustomValue('isVideo', true);

$deviceTokens = ['your device token'];

// print_r($payload);
// echo '<br>';
echo json_encode($payload);

$notifications = [];
foreach ($deviceTokens as $deviceToken) {
    $notifications[] = new Notification($payload,$deviceToken);
}

// If you have issues with ssl-verification, you can temporarily disable it. Please see attached note.
// Disable ssl verification
// $client = new Client($authProvider, $production = false, [CURLOPT_SSL_VERIFYPEER=>false] );
$client = new Client($authProvider, $production = false, [CURLOPT_SSL_VERIFYPEER=>false]);
$client->addNotifications($notifications);



$responses = $client->push(); // returns an array of ApnsResponseInterface (one Response per Notification)

foreach ($responses as $response) {
    // The device token
    $response->getDeviceToken();
    // A canonical UUID that is the unique ID for the notification. E.g. 123e4567-e89b-12d3-a456-4266554400a0
    $response->getApnsId();
    
    // Status code. E.g. 200 (Success), 410 (The device token is no longer active for the topic.)
    $response->getStatusCode();
    // E.g. The device token is no longer active for the topic.
    $response->getReasonPhrase();
    // E.g. Unregistered
    $response->getErrorReason();
    // E.g. The device token is inactive for the specified topic.
    $response->getErrorDescription();
    $response->get410Timestamp();
}
echo '<br>';
print_r($responses);