<?php

function response($array) {
    echo json_encode($array);
}

function response_code($num) {
    http_response_code($num);
    exit;
}