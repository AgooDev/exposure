<?php
/**
 * Copyright (c) 2016-present, Agoo.com.co <http://www.agoo.com.co>.
 * All rights reserved.
 *
 * This source code is licensed under the BSD-style license found in the
 * LICENSE file in the root directory of this source tree or translated in the assets folder.
 */
require_once 'config/config.php';
require_once 'lib/database.php';
require_once 'lib/hash.php';
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

$app->post('/login', function ($request, $response){
    # Get body data
    $data = $request->getParsedBody();

    # Email Variables
    # postData
    $email      = filter_var($data['email'],    FILTER_SANITIZE_EMAIL);
    $password   = filter_var($data['password'],       FILTER_SANITIZE_STRING);
    $passwordReturned = "";
    $messageResult = "";

    # Create a MySQL connecton
    $db = new Database();
    $columns = array(
        "ID",
        "user_login",
        "user_pass",
        "display_name"
    );
    $where = "user_login = '$email'";
    $user = $db->fetch('wp_users', $columns, $where);

    # Now check the password stored with password hashed
    $passwordReturned = $user["user_pass"];

    $hasher = new PasswordHash(8, TRUE);
    if($hasher->CheckPassword($password, $passwordReturned)) {
        $messageResult = "Welcome " . $user["display_name"];
    } else {
        $messageResult = "Error invalid username or incorrect password";
    }

    unset($db);

    $data = array(
        "result"    => true,
        "id"        => $user["ID"],
        "email"     => $user["user_login"],
        "message"   => $messageResult
    );

    $response->withJson($data);
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