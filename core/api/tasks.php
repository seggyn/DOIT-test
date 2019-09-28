<?php

include_once 'core/lib/api.php';

$LIMIT = 5;

// VERIFY TOKEN
if (!isset($_COOKIE['token']) || empty($_COOKIE['token'])) {
    header('Location: '.$_CONFIG['base_url'].'login');
}

// CONNECT TO DB
$db = DB::connect('localhost:8889', 'doit_test', 'root', 'root');

// FUNCTION FOR CONVERTING PRIORITY NUMBER TO WORD
function get_priority($num) {
    $array = ['High', 'Normal', 'Low'];
    return $array[$num-1];
}

// GET EMAIL FOR GETTING TASK
$result = $db->select_one('users', ['email'], ['token' => $_COOKIE['token']]);
$user_email = $result['email'];

// FILTERS
if (isset($_GET['field']) && !empty($_GET['field']) &&
    isset($_GET['order']) && !empty($_GET['order'])) {
    $tasks = $db->select('tasks', ['id','title','due_date','priority','is_done'],
        ['email' => $user_email], [$_GET['field'] => $_GET['order']]);
} else {
    $tasks = $db->select('tasks', ['id','title','due_date','priority','is_done'], ['email' => $user_email]);
}

// PAGINATION
$page_count = ceil((count($tasks)/$LIMIT));
$page_slice = ($_GET['page'] > 1) ? $LIMIT * ($_GET['page'] - 1) : 0;

$tasks = array_slice($tasks, $page_slice, $LIMIT);

if (!empty($tasks)) {
    response($tasks);
} else {
    response_code(404);
}