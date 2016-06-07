<?php
/**
 * Copyright (c) 2016-present, Agoo.com.co <http://www.agoo.com.co>.
 * All rights reserved.
 *
 * This source code is licensed under the BSD-style license found in the
 * LICENSE file in the root directory of this source tree or translated in the assets folder.
 */
require_once 'config/config.php';
//require_once 'lib/database.php';
require 'vendor/autoload.php';

$app = new \Slim\App();

$app->get('/', function ($request, $response, $args) {
    return $response->withStatus(200)->write('Service Available');
});

// LEGACY FUNCTIONS
// ================================================================
// LOGIN
$app->get('/login', function ($request, $response, $args) {
    $response->getBody()->write("Login");
    return $response;
});


// ================================================================
// EMAIL
$app->get('/email/recovery', function ($request, $response, $args) {
    $response->getBody()->write("Email Recovery");
    return $response;
});



// ================================================================
// RECOVERY
$app->get('/recovery', function ($request, $response, $args) {
    $response->getBody()->write("Recovery");
    return $response;
});


// ================================================================
// USER VIDEOS
$app->get('/users/videos', function ($request, $response, $args) {
    $response->getBody()->write("User Videos");
    return $response;
});


// ================================================================
// VIDEOS
$app->get('/videos', function ($request, $response, $args) {
    $response->getBody()->write("Videos");
    return $response;
});



$app->run();