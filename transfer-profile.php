<?php

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $oldSecureDomain .'/api/v1/profile.json');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

if(!empty($_POST['oldApi'])) {
    $headers = array();
    $headers[] = 'api-secret: ' . $oldHash;
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
}

$result = curl_exec($ch);

if (curl_errno($ch)) {
    echo 'Error:' . curl_error($ch);
}



curl_close($ch);

$arr = json_decode($result);

$newArray = [];

if(!empty($arr->message) && $arr->message = 'Unauthorized') { ?>
    <script>
        window.location.href = '/?e=a';
    </script>
    <?php
    exit();
}

$arr2 = json_decode($result, true);

foreach($arr2 as $item) {
    unset($item['_id']);
    array_push($newArray, $item);
}

$newJSON = json_encode($newArray);

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $newSecureDomain . '/api/v1/profile');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $newJSON);

$headers = array();
$headers[] = 'Content-Type: application/json';
$headers[] = 'api-secret: ' . $hashedSecret;
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$result = curl_exec($ch);

$arr = json_decode($result);
if(!empty($arr->message) && $arr->message = 'Unauthorized') { ?>
    <script>
        window.location.href = '/?e=a';
    </script>
    <?php
    exit();
}

if (curl_errno($ch)) {
    echo 'Error:' . curl_error($ch);
}

curl_close($ch);