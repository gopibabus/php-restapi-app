<?php
  require_once('db.php');
  require_once('../model/Response.php');
  require_once('../model/ResponseFactory.php');
  require_once ('../model/SessionFactory.php');

  $responseFactory = new ResponseFactory();
  $sessionFactory = new SessionFactory();

  try {

    $writeDb = DB::connectWriteDB();

  }
  catch (PDOException $ex) {
    $responseFactory->DatabaseFailureResponse($ex);
  }

  if (array_key_exists("sessionid", $_GET)) {
    $sessionFactory->processSessionData($_GET, $_SERVER);
  }
  elseif (empty($_GET)) {
    $sessionFactory->createSession($_SERVER);
  } else {
    $responseFactory->BasicResponse(
      404,
      "Endpoint not found",
      false
    );
  }
