<?php

include_once 'core/lib/api.php';

// CONNECT TO DB
$db = DB::connect('localhost:8889', 'doit_test', 'root', 'root');

// SEARCH TASK_ID AND TOKEN
foreach (['task_id', 'token'] as $key) {
    foreach ([$_GET[$key], $_DATA[$key]] as $item) {
        if (isset($item) && !empty($item)) {
            ${$key} = $item;
        }
    }
}

// VERIFY TOKEN
if (!isset($token) || empty($token)) {
    response_code(401);
}

// GET EMAIL
$result = $db->select_one('users', ['email'], ['token' => $token]);
$email = $result['email'];

// CREATE WHERE ARRAY FOR DB REQUEST
if (isset($task_id) && !empty($task_id)) {
    $where = ['id' => $task_id, 'email' => $email];
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') {

    if (isset($_GET['task_id']) && !empty($_GET['task_id'])) {
        $result = $db->select_one('tasks', ['title', 'due_date', 'priority'], $where);
        if (isset($result['due_date']) && !empty($result['due_date'])) {
            $result['due_date'] = date('Y.m.d h:i', $result['due_date']);
            response($result);
        }
        else {
            response_code(404);
        }
    } else {
        response_code(400);
    }

}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if ((isset($_DATA['title']) && !empty($_DATA['title'])) &&
        (isset($_DATA['due_date']) && !empty($_DATA['due_date'])))
    {
        $due_date = strtotime(str_replace('.', '-', $_DATA['due_date']));
        $db->insert('tasks', ['title' => $_DATA['title'], 'due_date' => $due_date, 'email' => $email]);
    } else {
        response_code(400);
    }

}

if ($_SERVER['REQUEST_METHOD'] == 'PUT') {

    if ((isset($_DATA['task_id']) && !empty($_DATA['task_id'])) &&
        ((isset($_DATA['title']) && !empty($_DATA['title'])) ||
        (isset($_DATA['due_date']) && !empty($_DATA['due_date'])) ||
        (isset($_DATA['priority']) && !empty($_DATA['priority'])) ||
        (isset($_DATA['is_done']) && !empty($_DATA['is_done']))))
    {
        $array = ['title', 'priority', 'due_date', 'is_done'];
        foreach ($array as $value) {
            if (isset($_DATA[$value]) && !empty($_DATA[$value])) {
                $temp[$value] = $_DATA[$value];
            }
        }
        if (array_key_exists('due_date', $temp)) {
            $temp['due_date'] = $due_date = strtotime(str_replace('.', '-', $_DATA['due_date']));
        }
        $db->update('tasks', $temp, $where);

    } else {
        response_code(400);
    }

}

if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {

    if (isset($_DATA['task_id']) && !empty($_DATA['task_id']))
    {
        $db->delete('tasks', $where);
    } else {
        response_code(400);
    }

}