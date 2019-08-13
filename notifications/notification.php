<?php
    require 'vendor/autoload.php';
    use vendor\Minishlink\WebPush\WebPush;
    use vendor\Minishlink\WebPush\Subscription;
    
    $subscription = Subscription::create(json_decode(file_get_contents('php://input'), true));
?>