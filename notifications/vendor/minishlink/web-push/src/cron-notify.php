<?php
    // require '/home/sqing/edu.sqi.ng/edozzier/notifications/vendor/autoload.php';
    // require '/home/sqing/edu.sqi.ng/edozzier/portal-classes/Crud.php';

    require '/home/nbtsedun/public_html/edozzier/notifications/vendor/autoload.php';
    require '/home/nbtsedun/public_html/edozzier/portal-classes/Crud.php';

    // require '/home/bctoyoor/public_html/edozzier/notifications/vendor/autoload.php';
    // require '/home/bctoyoor/public_html/edozzier/portal-classes/Crud.php';
    $companyDetails = new Crud();
    $query = "SELECT * FROM company_tb";
    $stmt = $companyDetails->read2($query);
    $row = $stmt->fetch_assoc();
    $companyLogo = $row['logo'];
    $companyName = $row['adm_initial'];
    use Minishlink\WebPush\WebPush;
    use Minishlink\WebPush\Subscription;
    //$__data = json_decode(file_get_contents('php://input'),true);
    //$_data = $__data['sub'];
    //$student_id = $__data['username'];
    $auth = array(
        'VAPID' => array(
            'subject' => 'mailto:noreply@edu.sqi.ng',
            'publicKey' => 'BIrxCacdxpxiM2l5GWMDEsgEAfJA8lmZP7p7n9cQW6EfibpgBFiPz3PEPv2_AXUWyFE8fXEt2BqxhaAYgv3T_70',
            'privateKey' => 'CDHb4ZZjZtBkmqFqC96rXrt4XWR4c2o8_aGlsEqEXVI'
        )
    );
    $webPush = new WebPush($auth);
    $q = new Crud();
    $query = "SELECT * FROM student_notification_tb INNER JOIN student_table 
                ON student_notification_tb.student_id = student_table.student_id 
                WHERE _read = 'N'";
    $fetch = $q->read2($query);
    $no = $fetch->num_rows;
    while($row = $fetch->fetch_assoc()){
         $_data = json_decode($row['notification_json'], true);
         $title = $row['message_title']; 
         $body = $row['message'];
         $date = $row['date'];
         $target_component = $row['target_component'];
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
              "icon": "' . api_base_href . '/' .$companyLogo . '",
              "badge": "' . api_base_href . '/' .$companyLogo . '",
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
                "url": "' . base_href . '/#/home/' . $target_component . '"
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