<?php

include_once 'core/lib/api.php';

$db = DB::connect('localhost:8889', 'doit_test', 'root', 'root');

if ($_PAGE_NAME == 'sign-up') {

    $result = $db->select_one('users', ['token'], ['email' => $_DATA['email']]);
    if (empty($result)) {
        $token = md5($data->email . uniqid());
        $db->insert('users', ['email' => $_DATA['email'],
            'password' => $_DATA['password'], 'token' => $token]);
        response(['token' => $token]);
    } else {
        response_code(403);
    }

}

if ($_PAGE_NAME == 'sign-in') {

    $result = $db->select_one('users', ['token'],
        ['email' => $_DATA['email'], 'password' => $_DATA['password']]);
    if (!empty($result)) {
        response(['token' => $result['token']]);
    } else {
        response_code(401);
    }

}