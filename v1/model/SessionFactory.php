<?php

  require_once('../controller/db.php');
  require_once('ResponseFactory.php');

  /**
   * Class SessionFactory
   */
  class SessionFactory
  {
    public $writeDb;

    public $responseFactory;

    /**
     * SessionFactory constructor.
     */
    public function __construct()
    {
      $this->responseFactory = new ResponseFactory();
      try {

        $this->writeDb = DB::connectWriteDB();

      } catch (PDOException $ex) {
        $responseFactory = new ResponseFactory();
        $responseFactory->DatabaseFailureResponse($ex);
      }

    }

    /**
     * $requestGetObj = $_GET
     * @param array $requestGetObj
     * @param array $requestServerObj
     */
    public function processSessionData(array $requestGetObj, array $requestServerObj)
    {
      $sessionid = $requestGetObj['sessionid'];

      if ($sessionid === '' || !is_numeric($sessionid)) {
        $message = false;
        ($sessionid === '' ? $message = 'Session id cannot be blank' : false);
        (!is_numeric($sessionid) ? $message = 'Session id should be numeric' : false);
        $this->responseFactory->BasicResponse(400, $message, false);
      }

      if (!isset($requestServerObj['HTTP_AUTHORIZATION']) || strlen($requestServerObj['HTTP_AUTHORIZATION']) < 1) {
        $message = false;
        (!isset($_SERVER['HTTP_AUTHORIZATION']) ? $message = 'Access token is missing from header' : false);
        (strlen($_SERVER['HTTP_AUTHORIZATION']) < 1 ? $message = 'Access token cannot be blank' : false);
        $this->responseFactory->BasicResponse(401, $message, false);
      }

      $accessToken = $requestServerObj['HTTP_AUTHORIZATION'];

      if ($requestServerObj['REQUEST_METHOD'] === 'DELETE') {
        $this->deleteSession($requestGetObj, $requestServerObj);
      } elseif ($requestServerObj['REQUEST_METHOD'] === 'PATCH') {
        $this->updateSession($requestGetObj, $requestServerObj);
      } else {
        $this->responseFactory->BasicResponse(
          405,
          'Request method not allowed',
          false
        );
      }
    }

    /**
     * Update Session Data
     * @param array $requestGetObj
     * @param array $requestServerObj
     */
    public function updateSession($requestGetObj, $requestServerObj)
    {
      $accessToken = $requestServerObj['HTTP_AUTHORIZATION'];
      $sessionid = $requestGetObj['sessionid'];

      if ($requestServerObj['CONTENT_TYPE'] !== 'application/json') {
        $this->responseFactory->BasicResponse(
          400,
          'Content type header is not set to jso',
          false
        );
      }

      $rawPatchData = file_get_contents('php://input');

      if (!$jsonData = json_decode($rawPatchData)) {
        $this->responseFactory->BasicResponse(
          400,
          'Request body is not valid json',
          false
        );
      }

      if (
        !isset($jsonData->refresh_token) ||
        strlen($jsonData->refresh_token) < 1
      ) {
        $message = false;
        (!isset($jsonData->refresh_token) ? $message = 'Refresh token not supplied' : false);
        (strlen($jsonData->refresh_token) < 1 ? $message = 'Refresh token cannot be blank' : false);
        $this->responseFactory->BasicResponse(
          400,
          $message,
          false
        );
      }

      try {

        $refreshToken = $jsonData->refresh_token;

        $query = $this->writeDb->prepare('
                    SELECT 
                        tbl_sessions.id as sessionid, 
                        tbl_sessions.userid as userid, 
                        accesstoken,
                        refreshtoken,
                        useractive,
                        loginattempts,
                        accesstokenexpiry,
                        refreshtokenexpiry
                    FROM 
                        tbl_sessions, 
                        tbl_users
                    WHERE 
                        tbl_users.id = tbl_sessions.userid
                        AND
                        tbl_sessions.id = :sessionid
                        AND 
                        tbl_sessions.accesstoken = :accesstoken
                        AND
                        tbl_sessions.refreshtoken = :refreshtoken
                    ');

        $query->bindParam(':sessionid', $sessionid, PDO::PARAM_INT);
        $query->bindParam(':accesstoken', $accessToken, PDO::PARAM_STR);
        $query->bindParam(':refreshtoken', $refreshToken, PDO::PARAM_STR);
        $query->execute();

        $rowCount = $query->rowCount();

        if ($rowCount === 0) {
          $this->responseFactory->BasicResponse(
            401,
            'Access token or refresh token is incorrect',
            false
          );
        }

        $row = $query->fetch(PDO::FETCH_ASSOC);

        $returned_sessionid = $row['sessionid'];
        $returned_userid = $row['userid'];
        $returned_accesstoken = $row['accesstoken'];
        $returned_refreshtoken = $row['refreshtoken'];
        $returned_useractive = $row['useractive'];
        $returned_loginattempts = $row['loginattempts'];
        $returned_accesstokenexpiry = $row['accesstokenexpiry'];
        $returned_refreshtokenexpiry = $row['refreshtokenexpiry'];

        if ($returned_useractive !== 'Y') {

          $this->responseFactory->BasicResponse(
            401,
            'User account is not active',
            false
          );
        }

        if ($returned_loginattempts >= 3) {

          $this->responseFactory->BasicResponse(
            401,
            'User account is currently locked out',
            false
          );
        }

        if (strtotime($returned_refreshtokenexpiry) < time()) {
          $this->responseFactory->BasicResponse(
            401,
            'Refresh token is expired - please login again',
            false
          );
        }

        $accessToken = base64_encode(bin2hex(openssl_random_pseudo_bytes(24)) . time());
        $refreshToken = base64_encode(bin2hex(openssl_random_pseudo_bytes(24)) . time());

        $access_token_expiry_time = 1200;
        $refresh_token_expiry_time = 1209600;

        $query = $this->writeDb->prepare('
          UPDATE
            tbl_sessions
          SET
            accesstoken = :accesstoken,
            accesstokenexpiry = date_add(NOW(), INTERVAL :accesstokenexpiryseconds SECOND),
            refreshtoken = :refreshtoken,
            refreshtokenexpiry = date_add(NOW(), INTERVAL :refreshtokenexpiryseconds SECOND)
          WHERE
            id = :sessionid
            AND
            userid = :userid
            AND 
            accesstoken = :returnedaccesstoken
            AND 
            refreshtoken = :returnedrefreshtoken
        ');


        $query->bindParam(':userid', $returned_userid, PDO::PARAM_INT);
        $query->bindParam(':sessionid', $returned_sessionid, PDO::PARAM_INT);
        $query->bindParam(':accesstoken', $accessToken, PDO::PARAM_STR);
        $query->bindParam(':refreshtoken', $refreshToken, PDO::PARAM_STR);
        $query->bindParam(':accesstokenexpiryseconds', $access_token_expiry_time, PDO::PARAM_INT);
        $query->bindParam(':refreshtokenexpiryseconds', $refresh_token_expiry_time, PDO::PARAM_INT);
        $query->bindParam(':returnedaccesstoken', $returned_accesstoken, PDO::PARAM_STR);
        $query->bindParam(':returnedrefreshtoken', $returned_refreshtoken, PDO::PARAM_STR);

        $query->execute();

        $rowCount = $query->rowCount();

        if ($rowCount === 0) {
          $this->responseFactory->BasicResponse(
            401,
            'Access token could not be refreshed - please login again',
            false
          );
        }

        $returnData = [];
        $returnData['session_id'] = $returned_sessionid;
        $returnData['access_token'] = $accessToken;
        $returnData['access_token_expiry'] = $access_token_expiry_time;
        $returnData['refresh_token'] = $refreshToken;
        $returnData['refresh_token_expiry'] = $refresh_token_expiry_time;

        $this->responseFactory->SuccessResponse(
          $returnData,
          'Token refreshed'
        );

      } catch (PDOException $ex) {
        $this->responseFactory->BasicResponse(
          500,
          'There was an issue refreshing access token - please login again',
          false
        );
      }
    }

    /**
     * Delete Session data
     * @param array $requestGetObj
     * @param array $requestServerObj
     */
    public function deleteSession($requestGetObj, $requestServerObj)
    {
      $accessToken = $requestServerObj['HTTP_AUTHORIZATION'];
      $sessionid = $requestGetObj['sessionid'];

      try {
        $query = $this->writeDb->prepare('
                    DELETE 
                    FROM tbl_sessions 
                    WHERE 
                        id = :sessionid 
                        AND 
                        accesstoken = :accesstoken
                    ');
        $query->bindParam(':sessionid', $sessionid, PDO::PARAM_INT);
        $query->bindParam(':accesstoken', $accessToken, PDO::PARAM_STR);
        $query->execute();

        $rowCount = $query->rowCount();

        if ($rowCount === 0) {
          $this->responseFactory->BasicResponse(
            400,
            'Failed to logout using access token provided',
            false
          );
        }

        $returnData = [];
        $returnData['session_id'] = intval($sessionid);

        $this->responseFactory->SuccessResponse($returnData, 'Logged Out');

      } catch (PDOException $ex) {
        $this->responseFactory->BasicResponse(
          500,
          'There was an issue logging out - please try again',
          false
        );
      }
    }

    /**
     * Create a new Session
     * @param array $requestServerObj
     */
    public function createSession(array $requestServerObj)
    {
      if ($requestServerObj['REQUEST_METHOD'] !== 'POST') {
        $this->responseFactory->BasicResponse(
          405,
          'Request method not allowed',
          false
        );
      }

      sleep(1);

      if ($requestServerObj['CONTENT_TYPE'] !== 'application/json') {
        $this->responseFactory->BasicResponse(
          400,
          'Content type is not set to json',
          false
        );
      }

      $rawPostData = file_get_contents('php://input');

      if (!$jsonData = json_decode($rawPostData)) {
        $this->responseFactory->BasicResponse(
          400,
          'Request body is not valid json',
          false
        );
      }

      if (!isset($jsonData->username) || !isset($jsonData->password)) {
        $message = false;
        (!isset($jsonData->username) ? $message = "username is not supplied" : false);
        (!isset($jsonData->password) ? $message = "password is not supplied" : false);
        $this->responseFactory->BasicResponse(
          400,
          $message,
          false
        );
      }

      if (
        strlen($jsonData->username) < 1 ||
        strlen($jsonData->username) > 255 ||
        strlen($jsonData->password) < 1 ||
        strlen($jsonData->password) > 255
      ) {
        $message = false;
        strlen($jsonData->username) < 1 ? $message = "user name cannot be blank" : false;
        strlen($jsonData->username) > 255 ? $message = "user name cannot be more than 255 characters" : false;
        strlen($jsonData->password) < 1 ? $message = "password cannot be blank" : false;
        strlen($jsonData->password) > 255 ? $message = "password cannot be more than 255 characters" : false;
        $this->responseFactory->BasicResponse(
          400,
          $message,
          false
        );
      }

      $username = trim($jsonData->username);
      $password = $jsonData->password;

      try {
        $query = $this->writeDb->prepare('
                    SELECT 
                        id, 
                        fullname, 
                        username, 
                        password, 
                        useractive, 
                        loginattempts 
                    FROM 
                        tbl_users 
                    WHERE 
                        username = :username
                    ');
        $query->bindParam(':username', $username, PDO::PARAM_STR);
        $query->execute();

        $rowCount = $query->rowCount();

        if ($rowCount == 0) {
          $this->responseFactory->BasicResponse(
            401,
            "Username or password is incorrect",
            false
          );
        }

        $row = $query->fetch(PDO::FETCH_ASSOC);

        $returned_id = $row['id'];
        $returned_fullname = $row['fullname'];
        $returned_username = $row['username'];
        $returned_password = $row['password'];
        $returned_useractive = $row['useractive'];
        $returned_loginattempts = $row['loginattempts'];

        if ($returned_useractive !== 'Y') {
          $this->responseFactory->BasicResponse(
            401,
            "User account not active",
            false
          );
        }

        if ($returned_loginattempts >= 3) {
          $this->responseFactory->BasicResponse(
            401,
            "User account is currently locked out",
            false
          );
        }

        if (!password_verify($password, $returned_password)) {
          $query = $this->writeDb->prepare('
                    UPDATE 
                        tbl_users 
                    SET 
                        loginattempts = loginattempts+1 
                    WHERE 
                        id = :id
                    ');

          $query->bindParam(':id', $returned_id, PDO::PARAM_INT);
          $query->execute();

          $this->responseFactory->BasicResponse(
            401,
            "User name or password is incorrect",
            false
          );
        }

        $accessToken = base64_encode(bin2hex(openssl_random_pseudo_bytes(24)) . time());
        $refreshToken = base64_encode(bin2hex(openssl_random_pseudo_bytes(24)) . time());

        /**
         * 12 seconds
         */
        $access_token_expiry = 1200;
        /**
         * 14 days
         */
        $refresh_token_expiry = 1209600;
      } catch (PDOException $ex) {
        $this->responseFactory->BasicResponse(
          500,
          "There was a issue logging in",
          false
        );
      }

      try {
        $this->writeDb->beginTransaction();

        $query = $this->writeDb->prepare('
                    UPDATE 
                        tbl_users 
                    SET 
                        loginattempts = 0 
                    WHERE 
                        id = :id
      ');

        $query->bindParam(':id', $returned_id, PDO::PARAM_INT);
        $query->execute();

        $query = $this->writeDb->prepare('
                    INSERT 
                    INTO 
                        tbl_sessions 
                        (userid, accesstoken, accesstokenexpiry, refreshtoken, refreshtokenexpiry) 
                    VALUES (
                        :userid, 
                        :accesstoken, 
                        date_add(NOW(), INTERVAL :accesstokenexpiryseconds SECOND), 
                        :refreshtoken, 
                        date_add(NOW(), INTERVAL :refreshtokenexpiryseconds SECOND))'
                    );

        $query->bindParam(':userid', $returned_id, PDO::PARAM_INT);
        $query->bindParam(':accesstoken', $accessToken, PDO::PARAM_STR);
        $query->bindParam(':accesstokenexpiryseconds', $access_token_expiry, PDO::PARAM_INT);
        $query->bindParam(':refreshtoken', $refreshToken, PDO::PARAM_STR);
        $query->bindParam(':refreshtokenexpiryseconds', $refresh_token_expiry, PDO::PARAM_INT);

        $query->execute();

        $lastSessionId = $this->writeDb->lastInsertId();
        $this->writeDb->commit();

        $returnData = [];
        $returnData['session_id'] = intval($lastSessionId);
        $returnData['access_token'] = $accessToken;
        $returnData['access_token_expires_in'] = $access_token_expiry;
        $returnData['refresh_token'] = $refreshToken;
        $returnData['refresh_token_expires_in'] = $refresh_token_expiry;

        $this->responseFactory->SuccessResponse($returnData, true);

      } catch (PDOException $ex) {
        $this->writeDb->rollBack();
        $this->responseFactory->BasicResponse(
          500,
          "There was a issue logging in - please try again",
          false
        );
      }
    }
  }