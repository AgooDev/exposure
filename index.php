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
require_once 'lib/functions.php';
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
    $state = false;

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
        $state = true;
    } else {
        $messageResult = "Error invalid username or incorrect password";
    }
    unset($db);

    $data = array(
        "result"    => $state,
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
// MODULES
$app->get('/modules', function ($request, $response, $args) {
    $response->getBody()->write("Email Recovery");
    return $response;
});

$app->post('/modules', function ($request, $response){
    # Get body data
    $data = $request->getParsedBody();

    # Email Variables
    # postData
    $email      = filter_var($data['email'],    FILTER_SANITIZE_EMAIL);
    $messageResult = "";
    $state = false;

    # Create a MySQL connecton
    $db = new Database();
    # Check if email exists like user
    $columns = array(
        "ID",
        "user_login",
        "display_name"
    );
    $where = "user_login = '$email'";
    $user = $db->fetch('wp_users', $columns, $where);
    if($user["user_login"] === $email) {
        $messageResult = "Welcome " . $user["display_name"];
        $state = true;
    } else {
        $messageResult = "Error invalid username or incorrect password";
        $state = false;
    }

    # Now get the list of available modules for this user
    $where = "se_factura.idusuario = " . $user["ID"];
    $columns = array(
        "se_modulos.id",
        "se_modulos.programa",
        "se_modulos.nombre",
        "se_modulos.descripcion",
        "se_modulos.valor",
        "se_modulos.nivel",
        "se_factura.email",
    );
    $modules = $db->fetchMultiple('se_modulos INNER JOIN se_factura ON se_modulos.id = se_factura.idmodulo', $columns, $where);
    $final = array();

    foreach ($modules as $row){
        if($row[6] === $email) {
            $state = true;
        } else {
            $messageResult = "Error list of modules not found";
            $state = false;
        }

        $description = str_replace("<br />", ", ", $row[3]);
        $description = str_replace("<strong>", "", $description);
        $description = str_replace("</strong>", "", $description);
        $description = Functions::normalizeChars($description);


        $data = array(
            "result"        => $state,
            "id"            => $row[0],
            "program"       => $row[1],
            "name"          => $row[2],
            "description"   => $description,
            "value"         => $row[4],
            "level"         => $row[5],
            "message"       => $messageResult
        );
        array_push($final, $data);
    }
    unset($db);
    $response->withJson($final);
});


// ================================================================
// RECOVERY
$app->get('/recovery', function ($request, $response, $args) {
    $response->getBody()->write("Recovery");
    return $response;
});


// ================================================================
// VIDEOS
$app->get('/videos', function ($request, $response, $args) {
    $response->getBody()->write("Videos");
    return $response;
});

$app->post('/videos', function ($request, $response){
    # Get body data
    $data = $request->getParsedBody();

    # Email Variables
    # postData
    $email      = filter_var($data['email'],    FILTER_SANITIZE_EMAIL);
    $messageResult = "";
    $state = false;

    # Create a MySQL connecton
    $db = new Database();
    # Check if email exists like user
    $columns = array(
        "ID",
        "user_login",
        "display_name"
    );
    $where = "user_login = '$email'";
    $user = $db->fetch('wp_users', $columns, $where);
    if($user["user_login"] === $email) {
        $messageResult = "Welcome " . $user["display_name"];
        $state = true;
    } else {
        $messageResult = "Error invalid username or incorrect password";
    }

    # Now get the list of available videos for this user
    $where = "idusuario = " . $user["ID"];
    $columns = array(
        "ID",
        "user_login",
        "display_name"
    );
    $videos = $db->fetch('se_videosusuarios', $columns, $where);
    unset($db);

    $data = array(
        "result"    => $state,
        "id"        => $user["ID"],
        "email"     => $user["user_login"],
        "name"      => $user["display_name"],
        "message"   => $messageResult
    );

    $response->withJson($data);
});

$app->run();