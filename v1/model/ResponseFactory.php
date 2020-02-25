<?php

  require_once('Response.php');

  class ResponseFactory
  {

    /**
     * @param PDOException $ex
     */
    public function DatabaseFailureResponse($ex)
    {
      error_log("Database Connection error -" . $ex, 0);
      $this->BasicResponse(
        500,
        'Database Connection Error',
        false
      );
    }

    /**
     * @param int $statusCode
     * @param string|bool $message
     * @param bool $success
     */
    public function BasicResponse($statusCode, $message = false, $success = false)
    {
      $response = new Response();
      $response->setHttpStatusCode($statusCode);
      $response->setSuccess($success);
      $response->addMessage($message);
      $response->send();
      exit;
    }

    /**
     * @param array $data
     * @param  string $message
     */
    public function SuccessResponse(array $data, string $message){
      $response = new Response();
      $response->setHttpStatusCode(200);
      $response->setSuccess(true);
      $response->addMessage($message);
      $response->setData($data);
      $response->send();
      exit;
    }
  }