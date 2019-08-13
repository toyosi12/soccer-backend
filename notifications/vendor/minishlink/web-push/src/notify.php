<?php
    
    require '../../../../../notifications/vendor/autoload.php';
    require '../../../../../../soccer-api/classes/Crud.php';
    

  
    $companyLogo = 'https://ionicbasis.com/soccer-api/notifications/vendor/minishlink/web-push/src/logo.png';
    $companyName = 'IONICBASIS';
    use Minishlink\WebPush\WebPush;
    use Minishlink\WebPush\Subscription;
    $__data = json_decode(file_get_contents('php://input'),true);
    $_data = $__data['sub'];
    $user_id = $__data['user_id'];
    $user_id2 = $__data['user_id2'];
    // echo $user_id;
    // echo "  ";
    // echo $user_id2;
    $auth = array(
        'VAPID' => array(
            'subject' => 'mailto:toyosioyelayo@gmail.com',
            'publicKey' => 'BF-OaBBppQBiUC9Xv7A-BpRyJfqJRnsfBt4ZP8oyqA4wJ1izxGyC39dlP0oZwK3FjYS53AjC2nfyQF9JyDityPI',
            'privateKey' => 'YDQ7oRvE6IkBzvessc2dAX2MNP8LSd-fuRsplHOoOSk'
        )
    );
    $webPush = new WebPush($auth);
    //SELECT * FROM chats INNER JOIN users 
    //ON chats.recipient_id = users.user_id 
    //WHERE chats.recipient_id = '$user_id' ORDER BY date_sent ASC
    $q = new Crud();
    $query = "SELECT * FROM users WHERE user_id = '$user_id'";
    $query2 = "SELECT * FROM users WHERE user_id = '$user_id2'";
    $fetch = $q->read2($query);
    $fetch2 = $q->read2($query2);
    $row2 = $fetch2->fetch_assoc();
    $no = $fetch->num_rows;
    while($row = $fetch->fetch_assoc()){
         $_data = json_decode($row2['notification_json'], true);
         $title = $row['user_name']; 
         $body = $__data['message'];
        //  echo $body;
         $date = Date("Y-m-d");
         $target_component = "";//$row['target_component'];
// array of notifications
$notifications = [
    [
        'subscription' => Subscription::create([
            'endpoint' => $_data['endpoint'], // Firefox 43+,
            'publicKey' => $_data['keys']['p256dh'], // base 64 encoded, should be 88 chars
            'authToken' => $_data['keys']['auth'], // base 64 encoded, should be 24 chars
        ]),
        'payload' => '{
            "notification": {
              "title": "' . $title . '",
              "actions": [],
              "body": "' . $body . '",
              "dir": "auto",
              "icon": "' . $companyLogo . '",
              "badge": "'. $companyLogo. '' . '",
              "lang": "en",
              "renotify": true,
              "requireInteraction": true,
              "tag": 92679601234092030,
              "vibrate": [
                300,
                100,
                400
              ],
              "date": "' . $date . '",
              "no": "' . $no . '",
              "data": {
                "url": ""
              }
            }
          }',
    ]
];


//print_r($notifications[0]);
// send multiple notifications with payload
// foreach ($notifications as $notification) {
//     $x = $webPush->sendNotification(
//         $notification['subscription'],
//         $notification['payload'] // optional (defaults null)
//     );
//     //print_r($x);
// }



//send one notification and flush directly
$y = $webPush->sendNotification(
    $notifications[0]['subscription'],
    $notifications[0]['payload'], // optional (defaults null)
    true // optional (defaults false)
);
echo $y;
}


?>