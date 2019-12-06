<?php
require 'sendMail.php';

function main($phone, $password, $email, $emailPassword)
{

    $data = '{"password":"' . $password . '","mobile":"' . $phone . '"}';

    $ch = curl_init();

    $headerArray = array(
        "X-Service-Id:userauth",
        "client_id:eplus_app",
        "Content-Type:application/json;charset=UTF-8",
        'CLIENT-IP: 60.173.87.90',
        'X-FORWARDED-FOR: 60.173.87.90',
    );
    $url = "https://v2-app.delicloud.com/api/v2.0/auth/loginMobile";

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headerArray);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST"); //设置请求方式
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data); //设置提交的字符串
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //设为TRUE把curl_exec()结果转化为字串，而不是直接输出
    $output = curl_exec($ch);

    $data = json_decode($output);

    if (property_exists($data, "data")) {
        $token = $data->data->token;
        $user_id = $data->data->user_id;

        $headerArray = array(
            "token:" . $token,
            "org_id:605351107295707136",
            "user_id:" . $user_id,
            "client_type:eplus_app",
            "uuid:5cf6c604-9adc-37ea-be8b-f98c7f9218c7",
            "Content-Type:application/json",
            "Content-Length:86",
            "Host:v2-kq.delicloud.com",
            "Connection:Keep-Alive",
            "Accept-Encoding:gzip",
            "User-Agent:okhttp/3.11.0",
            'CLIENT-IP: 60.173.87.90',
            'X-FORWARDED-FOR: 60.173.87.90',
        );

        $url = "https://v2-kq.delicloud.com/attend/check/check";
        $data = '{"device_type":0,"device_id":"196581","lat":31.1335409483509,"lng":119.19756557859677}';

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headerArray);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST"); //设置请求方式
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data); //设置提交的字符串
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip,deflate');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //设为TRUE把curl_exec()结果转化为字串，而不是直接输出
        $output = curl_exec($ch);

        $data = json_decode($output);
        if (property_exists($data, "data")) {
            $url = $data->data->url;
            $conent = file_get_contents($url);

            $conent = str_replace("/attend/index/record", "https://v2-kq-cdn.delicloud.com/attend/index/record", $conent);
            $conent = str_replace("/statics/attend/wechat/images/s.png", "https://v2-kq-cdn.delicloud.com/statics/attend/wechat/images/s.png", $conent);

            sendMail($email, $conent, $emailPassword);
        }

    } else {
        die("登录失败！");
    }

    curl_close($ch);
}
