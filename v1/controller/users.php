<?php

  require_once('db.php');
  require_once('../model/Response.php');

  try {

    $writeDb = DB::connectWriteDB();

  } catch (PDOException $ex) {
    error_log("Database Connection error -" . $ex, 0);
    $response = new Response();
    $response->setHttpStatusCode(500);
    $response->setSuccess(false);
    $response->addMessage("Database Connection Error");
    $response->send();
    exit;
  }

  if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response = new Response();
    $response->setHttpStatusCode(405);
    $response->setSuccess(false);
    $response->addMessage("Request methods not allowed");
    $response->send();
    exit;
  }

  if ($_SERVER['CONTENT_TYPE'] !== 'application/json') {
    $response = new Response();
    $response->setHttpStatusCode(400);
    $response->setSuccess(false);
    $response->addMessage("Content type is not set to json");
    $response->send();
    exit;
  }

  $rawPostData = file_get_contents('php://input');

  if (!$jsonData = json_decode($rawPostData)) {
    $response = new Response();
    $response->setHttpStatusCode(400);
    $response->setSuccess(false);
    $response->addMessage("Request body is not valid json");
    $response->send();
    exit;
  }

  if (
    !isset($jsonData->fullname) ||
    !isset($jsonData->username) ||
    !isset($jsonData->password)
  ) {
    $response = new Response();
    $response->setHttpStatusCode(400);
    $response->setSuccess(false);
    (!isset($jsonData->fullname) ? $response->addMessage("Full name is not supplied") : false);
    (!isset($jsonData->username) ? $response->addMessage("username is not supplied") : false);
    (!isset($jsonData->password) ? $response->addMessage("password is not supplied") : false);
    $response->send();
    exit;
  }

  if (
    strlen($jsonData->fullname) < 1 ||
    strlen($jsonData->fullname) > 255 ||
    strlen($jsonData->username) < 1 ||
    strlen($jsonData->username) > 255 ||
    strlen($jsonData->password) < 1 ||
    strlen($jsonData->password) > 255
  ) {
    $response = new Response();
    $response->setHttpStatusCode(400);
    $response->setSuccess(false);
    strlen($jsonData->fullname) < 1 ? $response->addMessage("Full name cannot be blank") : false;
    strlen($jsonData->fullname) > 255 ? $response->addMessage("Full name cannot be more than 255 characters") : false;
    strlen($jsonData->username) < 1 ? $response->addMessage("user name cannot be blank") : false;
    strlen($jsonData->username) > 255 ? $response->addMessage("user name cannot be more than 255 characters") : false;
    strlen($jsonData->password) < 1 ? $response->addMessage("password cannot be blank") : false;
    strlen($jsonData->password) > 255 ? $response->addMessage("password cannot be more than 255 characters") : false;
    $response->send();
    exit;
  }

  $fullname = trim($jsonData->fullname);
  $username = trim($jsonData->username);
  $password = $jsonData->password;

  try {

    $query = $writeDb->prepare('SELECT id FROM tbl_users WHERE username = :username');
    $query->bindParam(':username', $username, PDO::PARAM_STR);
    $query->execute();

    $rowCount = $query->rowCount();

    if ($rowCount !== 0) {
      $response = new Response();
      $response->setHttpStatusCode(409);
      $response->setSuccess(false);
      $response->addMessage("Username already exists");
      $response->send();
      exit;
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $query = $writeDb->prepare(
      'INSERT INTO tbl_users (fullname, username, password) values (:fullname, :username, :password)'
    );
    $query->bindParam(':fullname', $fullname, PDO::PARAM_STR);
    $query->bindParam(':username', $username, PDO::PARAM_STR);
    $query->bindParam(':password', $hashed_password, PDO::PARAM_STR);
    $query->execute();

    $rowCount = $query->rowCount();

    if($rowCount === 0){
      $response = new Response();
      $response->setHttpStatusCode(500);
      $response->setSuccess(false);
      $response->addMessage("There was a issue creating a user account - please try again");
      $response->send();
      exit;
    }

    $lastUserId = $writeDb->lastInsertId();

    $returnData = [];

    $returnData['user_id'] = $lastUserId;
    $returnData['fullname'] = $fullname;
    $returnData['username'] = $username;

    $response = new Response();
    $response->setHttpStatusCode(200);
    $response->setSuccess(true);
    $response->addMessage("User created");
    $response->setData($returnData);
    $response->send();
    exit;

  } catch (PDOException $ex) {
    error_log("Database Query error -" . $ex, 0);
    $response = new Response();
    $response->setHttpStatusCode(500);
    $response->setSuccess(false);
    $response->addMessage("There was a issue creating a user account - please try again");
    $response->send();
    exit;
  }