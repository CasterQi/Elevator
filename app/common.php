<?php

function send_postX($url, $post_data) {
        $postdata = http_build_query($post_data);
        $options = array(
          'http' => array(
            'method' => 'POST',
            'header' => 'Content-type:application/x-www-form-urlencoded',
            'content' => $postdata,
            'timeout' => 3 // 超时时间（单位:s）
            )
        );
        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        return $result;
      }


function send_post_jsonX($url, $post_data, $token) {
    $postdata = http_build_query($post_data);
    $options = array(
        'http' => array(
        'method' => 'POST',
        'header' => 'Authorization:Bearer '.$token.'\r\n'.
                    'Content-Type:application/json\n',
                    
        'content' => $postdata,
        'timeout' => 3 // 超时时间（单位:s）
        )
    );
    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    return $result;
}
function send_post_jsonX2($url, $post_data, $token) {
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); //不验证证书
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-type: application/json; charset=utf-8",'Authorization:Bearer '.$token));
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
    $response = curl_exec($curl);
    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);
    //dump($httpCode);
    
    return  array($httpCode,$response);
}
      