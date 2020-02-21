<?php
  require_once('db.php');
  require_once('../model/Task.php');
  require_once('../model/Response.php');

  try {
    $writeDb = DB::connectWriteDB();
    $readDB = DB::connectReadDB();
  } catch (PDOException $ex) {
    error_log("Database Connection error -" . $ex, 0);
    $response = new Response();
    $response->setHttpStatusCode(500);
    $response->setSuccess(false);
    $response->addMessage("Database Connection Error");
    $response->send();
    exit;
  }

  /**
   * Begin Auth Script
   */

  if (!isset($_SERVER['HTTP_AUTHORIZATION']) || strlen($_SERVER['HTTP_AUTHORIZATION']) < 1) {
    $response = new Response();
    $response->setHttpStatusCode(401);
    $response->setSuccess(false);
    (!isset($_SERVER['HTTP_AUTHORIZATION']) ? $response->addMessage("Access token is missing from header") : false);
    (strlen($_SERVER['HTTP_AUTHORIZATION']) < 1 ? $response->addMessage("Access token cannot be blank") : false);
    $response->send();
    exit;
  }

  $accessToken = $_SERVER['HTTP_AUTHORIZATION'];

  try {

    $query = $writeDb->prepare('
    SELECT 
        userid, 
        accesstokenexpiry, 
        useractive, 
        loginattempts
    FROM
        tbl_sessions,
        tbl_users
    WHERE
        tbl_sessions.userid = tbl_users.id
        AND
        accesstoken = :accesstoken
  ');

    $query->bindParam(':accesstoken', $accessToken, PDO::PARAM_STR);
    $query->execute();

    $rowCount = $query->rowCount();

    if ($rowCount === 0) {
      $response = new Response();
      $response->setHttpStatusCode(401);
      $response->setSuccess(false);
      $response->addMessage("Invalid access token");
      $response->send();
      exit;
    }

    $row = $query->fetch(PDO::FETCH_ASSOC);

    $returned_userid = $row['userid'];
    $returned_accesstokenexpiry = $row['accesstokenexpiry'];
    $returned_useractive = $row['useractive'];
    $returned_loginattempts = $row['loginattempts'];

    if ($returned_useractive !== 'Y') {
      $response = new Response();
      $response->setHttpStatusCode(401);
      $response->setSuccess(false);
      $response->addMessage("User account not active");
      $response->send();
      exit;
    }

    if ($returned_loginattempts >= 3) {
      $response = new Response();
      $response->setHttpStatusCode(401);
      $response->setSuccess(false);
      $response->addMessage("User account is locked out");
      $response->send();
      exit;
    }

    if (strtotime($returned_accesstokenexpiry) < time()) {
      $response = new Response();
      $response->setHttpStatusCode(401);
      $response->setSuccess(false);
      $response->addMessage("Access token expired");
      $response->send();
      exit;
    }

  } catch (PDOException $ex) {
    error_log("Database Connection error -" . $ex, 0);
    $response = new Response();
    $response->setHttpStatusCode(500);
    $response->setSuccess(false);
    $response->addMessage("There was an issue authenticating - please try again");
    $response->send();
    exit;
  }

  /**
   * End of Auth Script
   */

  if (array_key_exists("task_id", $_GET)) {
    $task_id = $_GET['task_id'];

    if ($task_id == '' || !is_numeric($task_id)) {
      $response = new Response();
      $response->setHttpStatusCode(400);
      $response->setSuccess(false);
      $response->addMessage("Task ID cannot be blank or must be numeric");
      $response->send();
      exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
      try {
        $query = $readDB->prepare('
            SELECT 
                id, 
                title, 
                description, 
                deadline, 
                completed 
            FROM 
                tbl_tasks 
            WHERE 
                id = :task_id
                AND
                userid = :userid
        ');
        $query->bindParam(':task_id', $task_id, PDO::PARAM_INT);
        $query->bindParam(':userid', $returned_userid, PDO::PARAM_INT);
        $query->execute();

        $rowCount = $query->rowCount();

        if ($rowCount === 0) {
          $response = new Response();
          $response->setHttpStatusCode(404);
          $response->setSuccess(false);
          $response->addMessage("Task not found");
          $response->send();
          exit;
        }

        $tasksArray = [];
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
          $task = new Task(
            $row['id'],
            $row['title'],
            $row['description'],
            $row['deadline'],
            $row['completed']
          );
          $tasksArray[] = $task->returnTaskAsArray();
        }

        $returnData = [];
        $returnData['rows_returned'] = $rowCount;
        $returnData['tasks'] = $tasksArray;

        $response = new Response();
        $response->setHttpStatusCode(200);
        $response->setSuccess(true);
        $response->toCache(true);
        $response->setData($returnData);
        $response->send();
        exit;
      } catch (PDOException $ex) {
        error_log("Database Query error -" . $ex, 0);
        $response = new Response();
        $response->setHttpStatusCode(500);
        $response->setSuccess(false);
        $response->addMessage("Failed to get Task Error");
        $response->send();
        exit;
      } catch (TaskException $ex) {
        $response = new Response();
        $response->setHttpStatusCode(500);
        $response->setSuccess(false);
        $response->addMessage($ex->getMessage());
        $response->send();
        exit;
      }
    } elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {

      try {
        $query = $writeDb->prepare('
            DELETE FROM 
                tbl_tasks 
            WHERE 
                id= :task_id
                AND 
                userid = :userid
        ');

        $query->bindParam(':task_id', $task_id, PDO::PARAM_INT);
        $query->bindParam(':userid', $returned_userid, PDO::PARAM_INT);
        $query->execute();
        $rowCount = $query->rowCount();

        if ($rowCount === 0) {
          $response = new Response();
          $response->setHttpStatusCode(404);
          $response->setSuccess(false);
          $response->addMessage("Task not found");
          $response->send();
          exit;
        }

        $response = new Response();
        $response->setHttpStatusCode(200);
        $response->setSuccess(true);
        $response->addMessage("Task deleted");
        $response->send();
        exit;
      } catch (PDOException $ex) {
        $response = new Response();
        $response->setHttpStatusCode(500);
        $response->setSuccess(false);
        $response->addMessage("Failed to delete Task");
        $response->send();
        exit;
      }
    } elseif ($_SERVER['REQUEST_METHOD'] === 'PATCH') {

      try {

        if ($_SERVER['CONTENT_TYPE'] !== 'application/json') {
          $response = new Response();
          $response->setHttpStatusCode(400);
          $response->setSuccess(false);
          $response->addMessage("Content type is not set to JSON");
          $response->send();
          exit;
        }

        $rawPatchData = file_get_contents('php://input');

        if (!$jsonData = json_decode($rawPatchData)) {
          $response = new Response();
          $response->setHttpStatusCode(400);
          $response->setSuccess(false);
          $response->addMessage("Request body is not a valid JSON");
          $response->send();
          exit;
        }


        $title_updated = false;
        $description_updated = false;
        $deadline_updated = false;
        $completed_updated = false;

        $queryFields = "";

        if (isset($jsonData->title)) {
          $title_updated = true;
          $queryFields .= "title = :title, ";
        }

        if (isset($jsonData->description)) {
          $description_updated = true;
          $queryFields .= "description = :description, ";
        }

        if (isset($jsonData->deadline)) {
          $deadline_updated = true;
          $queryFields .= "deadline = STR_TO_DATE(:deadline, '%d/%m/%Y %H:%i'), ";
        }

        if (isset($jsonData->completed)) {
          $completed_updated = true;
          $queryFields .= "completed = :completed, ";
        }

        $queryFields = rtrim($queryFields, ", ");

        if (
          $title_updated === false &&
          $description_updated === false &&
          $deadline_updated === false &&
          $completed_updated === false
        ) {
          $response = new Response();
          $response->setHttpStatusCode(400);
          $response->setSuccess(false);
          $response->addMessage("No Task Fields Provided");
          $response->send();
          exit;
        }

        $query = $writeDb->prepare('
            SELECT 
                id, 
                title, 
                description, 
                DATE_FORMAT(deadline, "%d/%m/%Y %H:%i") as deadline, 
                completed
            FROM 
                tbl_tasks 
            WHERE 
                id = :taskid
                AND
                userid = :userid
            ');

        $query->bindParam(":taskid", $task_id, PDO::PARAM_INT);
        $query->bindParam(":userid", $returned_userid, PDO::PARAM_INT);
        $query->execute();

        $rowCount = $query->rowCount();

        if ($rowCount === 0) {
          $response = new Response();
          $response->setHttpStatusCode(404);
          $response->setSuccess(false);
          $response->addMessage("No Task found to update");
          $response->send();
          exit;
        }

        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
          $task = new Task(
            $row['id'],
            $row['title'],
            $row['description'],
            $row['deadline'],
            $row['completed']
          );
        }

        $queryString = '
            UPDATE 
                tbl_tasks 
            SET 
                ' . $queryFields . ' 
            WHERE 
              id= :taskid
            AND
              userid= :userid
        ';

        $query = $writeDb->prepare($queryString);

        if ($title_updated === true) {
          $task->setTitle($jsonData->title);
          $up_title = $task->getTitle();
          $query->bindParam(':title', $up_title, PDO::PARAM_STR);
        }

        if ($description_updated === true) {
          $task->setDescription($jsonData->description);
          $up_description = $task->getDescription();
          $query->bindParam(':description', $up_description, PDO::PARAM_STR);
        }

        if ($deadline_updated === true) {
          $task->setDeadline($jsonData->deadline);
          $up_deadline = $task->getDeadline();
          $query->bindParam(':deadline', $up_deadline, PDO::PARAM_STR);
        }

        if ($completed_updated === true) {
          $task->setCompleted($jsonData->completed);
          $up_completed = $task->getCompleted();
          $query->bindParam(':completed', $up_completed, PDO::PARAM_STR);
        }

        $query->bindParam(':taskid', $task_id, PDO::PARAM_INT);
        $query->bindParam(':userid', $returned_userid, PDO::PARAM_INT);
        $query->execute();

        if ($rowCount === 0) {
          $response = new Response();
          $response->setHttpStatusCode(400);
          $response->setSuccess(false);
          $response->addMessage("Task not updated");
          $response->send();
          exit;
        }

        $query = $writeDb->prepare('
            SELECT 
                id, 
                title, 
                description, 
                DATE_FORMAT(deadline, "%d/%m/%Y %H:%i") as deadline, 
                completed
            FROM 
                tbl_tasks 
            WHERE 
                id= :taskid
                AND
                userid = :userid
        ');

        $query->bindParam(':taskid', $task_id, PDO::PARAM_INT);
        $query->bindParam(':userid', $returned_userid, PDO::PARAM_INT);
        $query->execute();
        $rowCount = $query->rowCount();

        if ($rowCount === 0) {
          $response = new Response();
          $response->setHttpStatusCode(404);
          $response->setSuccess(false);
          $response->addMessage("No task found after update");
          $response->send();
          exit;
        }

        $taskArray = [];

        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
          $task = new Task(
            $row['id'],
            $row['title'],
            $row['description'],
            $row['deadline'],
            $row['completed']
          );
          $taskArray[] = $task->returnTaskAsArray();
        }
        $returnData = [];
        $returnData['rows_returned'] = $rowCount;
        $returnData['tasks'] = $taskArray;

        $response = new Response();
        $response->setHttpStatusCode(200);
        $response->setSuccess(true);
        $response->toCache(true);
        $response->setData($returnData);
        $response->send();
        exit;
      } catch (TaskException $ex) {
        $response = new Response();
        $response->setHttpStatusCode(400);
        $response->setSuccess(false);
        $response->addMessage($ex->getMessage());
        $response->send();
        exit;
      } catch (PDOException $ex) {
        error_log("Database query error -" . $ex, 0);
        $response = new Response();
        $response->setHttpStatusCode(400);
        $response->setSuccess(false);
        $response->addMessage("Failed to update task");
        $response->send();
        exit;
      }
    } else {
      $response = new Response();
      $response->setHttpStatusCode(405);
      $response->setSuccess(false);
      $response->addMessage("Request method is not allowed");
      $response->send();
      exit;
    }
  } else if (array_key_exists("completed", $_GET)) {
    $completed = $_GET['completed'];

    if ($completed !== 'Y' && $completed !== 'N') {
      $response = new Response();
      $response->setHttpStatusCode(400);
      $response->setSuccess(false);
      $response->addMessage("Completed filter must be Y or N");
      $response->send();
      exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {

      try {
        $query = $readDB->prepare('
            SELECT 
                id, 
                title, 
                description, 
                DATE_FORMAT(deadline, "%d/%m/%Y %H:%i") as deadline, 
                completed
            FROM 
                tbl_tasks 
            WHERE 
                completed = :completed
                AND
                userid = :userid
        ');

        $query->bindParam(':completed', $completed, PDO::PARAM_STR);
        $query->bindParam(':userid', $returned_userid, PDO::PARAM_INT);
        $query->execute();

        $rowCount = $query->rowCount();
        $taskArray = [];

        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
          $task = new Task(
            $row['id'],
            $row['title'],
            $row['description'],
            $row['deadline'],
            $row['completed']
          );
          $taskArray[] = $task->returnTaskAsArray();
        }

        $returnData = [];
        $returnData['rows_returned'] = $rowCount;
        $returnData['tasks'] = $taskArray;

        $response = new Response();
        $response->setHttpStatusCode(200);
        $response->setSuccess(true);
        $response->toCache(true);
        $response->setData($returnData);
        $response->send();
        exit;
      } catch (TaskException $ex) {
        $response = new Response();
        $response->setHttpStatusCode(500);
        $response->setSuccess(false);
        $response->addMessage($ex->getMessage());
        $response->send();
        exit;
      } catch (PDOException $ex) {
        error_log("Database query error -" . $ex, 0);
        $response = new Response();
        $response->setHttpStatusCode(500);
        $response->setSuccess(false);
        $response->addMessage("Failed to get Tasks");
        $response->send();
        exit;
      }
    } else {
      $response = new Response();
      $response->setHttpStatusCode(405);
      $response->setSuccess(false);
      $response->addMessage("Request method not allowed");
      $response->send();
      exit;
    }
  } elseif (array_key_exists("page", $_GET)) {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {

      $page = $_GET['page'];

      if ($page == '' || !is_numeric($page)) {
        $response = new Response();
        $response->setHttpStatusCode(400);
        $response->setSuccess(false);
        $response->addMessage("Page number cannot be blank and must be numeric");
        $response->send();
        exit;
      }

      $limitPerPage = 20;

      try {
        $query = $readDB->prepare('
            SELECT 
                count(id) as totalNoOfTasks 
            FROM 
                tbl_tasks
            WHERE
                userid = :userid
        ');

        $query->bindParam(':userid', $returned_userid, PDO::PARAM_INT);

        $query->execute();

        $row = $query->fetch(PDO::FETCH_ASSOC);
        $taskCount = intval($row['totalNoOfTasks']);

        $numOfPages = ceil($taskCount / $limitPerPage);

        if ($numOfPages == 0) {
          $numOfPages = 1;
        }

        if ($page > $numOfPages || $page == 0) {
          $response = new Response();
          $response->setHttpStatusCode(404);
          $response->setSuccess(false);
          $response->addMessage('Page not found');
          $response->send();
          exit;
        }

        $offset = ($page == 1 ? 0 : ($limitPerPage * ($page - 1)));

        $query = $readDB->prepare('
            SELECT 
                id, 
                title, 
                description, 
                DATE_FORMAT(deadline, "%d/%m/%Y %H:%i") as deadline, 
                completed
            FROM 
                tbl_tasks
            WHERE
                userid = :userid
            LIMIT :pgLimit 
            OFFSET :offset
        ');

        $query->bindParam(':userid', $returned_userid, PDO::PARAM_INT);
        $query->bindParam(':pgLimit', $limitPerPage, PDO::PARAM_INT);
        $query->bindParam(':offset', $offset, PDO::PARAM_INT);
        $query->execute();

        $rowCount = $query->rowCount();
        $taskArray = [];

        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
          $task = new Task(
            $row['id'],
            $row['title'],
            $row['description'],
            $row['deadline'],
            $row['completed']
          );
          $taskArray[] = $task->returnTaskAsArray();
        }
        $returnData = [];
        $returnData['rows_returned'] = $rowCount;
        $returnData['total_rows'] = $taskCount;
        $returnData['total_pages'] = $numOfPages;
        $returnData['has_next_page'] = ($page < $numOfPages) ? true : false;
        $returnData['has_previous_page'] = ($page > 1) ? true : false;
        $returnData['tasks'] = $taskArray;

        $response = new Response();
        $response->setHttpStatusCode(200);
        $response->setSuccess(true);
        $response->toCache(true);
        $response->setData($returnData);
        $response->send();
        exit;
      } catch (TaskException $ex) {
        $response = new Response();
        $response->setHttpStatusCode(500);
        $response->setSuccess(false);
        $response->addMessage($ex->getMessage());
        $response->send();
        exit;
      } catch (PDOException $ex) {
        error_log("Database Connection error -" . $ex, 0);
        $response = new Response();
        $response->setHttpStatusCode(500);
        $response->setSuccess(false);
        $response->addMessage("Failed to get Tasks");
        $response->send();
        exit;
      }
    } else {
      $response = new Response();
      $response->setHttpStatusCode(405);
      $response->setSuccess(false);
      $response->addMessage("Request Method not allowed");
      $response->send();
      exit;
    }
  } elseif (empty($_GET)) {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
      try {
        $query = $readDB->prepare('
            SELECT 
                id, 
                title, 
                description, 
                DATE_FORMAT(deadline, "%d/%m/$Y %H:%i") as deadline, 
                completed 
            FROM 
                tbl_tasks
            WHERE
                userid = :userid
        ');

        $query->bindParam(':userid', $returned_userid, PDO::PARAM_INT);

        $query->execute();


        $rowCount = $query->rowCount();

        $tasksArray = [];
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
          $task = new Task(
            $row['id'],
            $row['title'],
            $row['description'],
            $row['deadline'],
            $row['completed']
          );
          $tasksArray[] = $task->returnTaskAsArray();
        }

        $returnData = [];
        $returnData['rows_returned'] = $rowCount;
        $returnData['tasks'] = $tasksArray;

        $response = new Response();
        $response->setHttpStatusCode(200);
        $response->setSuccess(true);
        $response->toCache(true);
        $response->setData($returnData);
        $response->send();
        exit;
      } catch (TaskException $ex) {
        $response = new Response();
        $response->setHttpStatusCode(500);
        $response->setSuccess(false);
        $response->addMessage($ex->getMessage());
        $response->send();
        exit;
      } catch (PDOException $ex) {
        error_log("Database query error - " . $ex, 0);
        $response = new Response();
        $response->setHttpStatusCode(500);
        $response->setSuccess(false);
        $response->addMessage("Failed to get tasks");
        $response->send();
        exit;
      }
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
      try {

        if ($_SERVER['CONTENT_TYPE'] !== 'application/json') {
          $response = new Response();
          $response->setHttpStatusCode(400);
          $response->setSuccess(false);
          $response->addMessage("Content type header is not set to JSON");
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

        if (!isset($jsonData->title) || !isset($jsonData->completed)) {
          $response = new Response();
          $response->setHttpStatusCode(400);
          $response->setSuccess(false);
          (!isset($jsonData->title) ? $response->addMessage('Title field is mandatory') : false);
          (!isset($jsonData->completed) ? $response->addMessage('Completed field is mandatory') : false);
          $response->send();
          exit;
        }

        $newTask = new Task(
          null,
          $jsonData->title,
          (isset($jsonData->description) ? $jsonData->description : null),
          (isset($jsonData->deadline) ? $jsonData->deadline : null),
          $jsonData->completed
        );

        $title = $newTask->getTitle();
        $description = $newTask->getDescription();
        $deadline = $newTask->getDeadline();
        $completed = $newTask->getCompleted();

        $query = $writeDb->prepare('
          INSERT INTO 
            tbl_tasks 
              (title, description, deadline, completed, userid) 
            VALUES 
              (:title, :description, STR_TO_DATE(:deadline, \'%d/%m/%Y %H:%i\'), :completed, :userid)
        ');

        $query->bindParam(':title', $title, PDO::PARAM_STR);
        $query->bindParam(':description', $description, PDO::PARAM_STR);
        $query->bindParam(':deadline', $deadline, PDO::PARAM_STR);
        $query->bindParam(':completed', $completed, PDO::PARAM_STR);
        $query->bindParam(':userid', $returned_userid, PDO::PARAM_INT);

        $query->execute();

        $rowCount = $query->rowCount();

        if ($rowCount === 0) {
          $response = new Response();
          $response->setHttpStatusCode(500);
          $response->setSuccess(false);
          $response->addMessage("Failed to create Task");
          $response->send();
          exit;
        }

        $lastTaskId = $writeDb->lastInsertId();

        $query = $writeDb->prepare('
            SELECT 
                id, 
                title, 
                description, 
                DATE_FORMAT(deadline, "%d/%m/%Y %H:%i") as deadline, 
                completed
            FROM 
                tbl_tasks 
            WHERE 
                id = :taskid
                AND 
                userid = :userid
        ');

        $query->bindParam(':taskid', $lastTaskId, PDO::PARAM_INT);
        $query->bindParam(':userid', $returned_userid, PDO::PARAM_INT);
        $query->execute();

        $rowCount = $query->rowCount();

        if ($rowCount === 0) {
          $response = new Response();
          $response->setHttpStatusCode(500);
          $response->setSuccess(false);
          $response->addMessage("Failed to retrieve task after creation");
          $response->send();
          exit;
        }

        $tasksArray = [];
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
          $task = new Task(
            $row['id'],
            $row['title'],
            $row['description'],
            $row['deadline'],
            $row['completed']
          );
          $tasksArray[] = $task->returnTaskAsArray();
        }

        $returnData = [];
        $returnData['rows_returned'] = $rowCount;
        $returnData['tasks'] = $tasksArray;

        $response = new Response();
        $response->setHttpStatusCode(200);
        $response->setSuccess(true);
        $response->addMessage("Task Created");
        $response->setData($returnData);
        $response->send();
        exit;
      } catch (TaskException $ex) {
        $response = new Response();
        $response->setHttpStatusCode(400);
        $response->setSuccess(false);
        $response->addMessage($ex->getMessage());
        $response->send();
        exit;
      } catch (PDOException $ex) {
        error_log("Database query error - " . $ex, 0);
        $response = new Response();
        $response->setHttpStatusCode(500);
        $response->setSuccess(false);
        $response->addMessage("Failed to insert task into database");
        $response->send();
        exit;
      }
    } else {
      $response = new Response();
      $response->setHttpStatusCode(405);
      $response->setSuccess(false);
      $response->addMessage("Request method not allowed");
      $response->send();
      exit;
    }
  } else {
    $response = new Response();
    $response->setHttpStatusCode(404);
    $response->setSuccess(false);
    $response->addMessage("Endpoint not found");
    $response->send();
    exit;
  }
