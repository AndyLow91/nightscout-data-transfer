<?php

$today = date("Y-m-d", time() + 3600*24);

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $oldSecureDomain .'/api/v1/entries.json?count=all&find[dateString][$lte]=' . $toDate . '&find[dateString][$gte]=' . $fromDate);
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

$arr = json_decode($result, true);

$newArray = [];

foreach($arr as $item) {
    unset($item['_id']);
    array_push($newArray, $item);
}

$newJSON = json_encode($newArray);

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $newSecureDomain . '/api/v1/entries');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $newJSON);

$headers = array();
$headers[] = 'Content-Type: application/json';
$headers[] = 'api-secret: ' . $hashedSecret;
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$result = curl_exec($ch);

if (curl_errno($ch)) {
    echo 'Error:' . curl_error($ch);
}

curl_close($ch);