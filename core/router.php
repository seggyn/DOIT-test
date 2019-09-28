<?php

$uri_path = str_replace($_CONFIG['path'], '', $_SERVER[REQUEST_URI]);
$uri = explode('/', $uri_path);

$_PAGE_NAME = explode('?', $uri[0])[0];
$_DATA = json_decode(file_get_contents('php://input'), true);

if (is_numeric($uri[1])) {
    $_REQUEST_NUMBER = intval($uri[1]);
}

switch ($_PAGE_NAME) {
    case '':
    case 'index':
    case 'login':
        include_once 'views/login.php';
        break;

    case 'registration':
        include_once 'views/registration.php';
        break;

    case 'list':
        include_once 'views/list.php';
        break;

    case 'task':
        include_once 'core/api/task.php';
        break;

    case 'sign-up':
    case 'sign-in':
        include_once 'core/api/auth.php';
        break;

}
