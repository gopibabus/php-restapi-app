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
            $query = $readDB->prepare(
                'SELECT id, title, description, deadline, completed 
                            FROM tbl_tasks WHERE id = :task_id'
            );
            $query->bindParam(':task_id', $task_id, PDO::PARAM_INT);
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
            $query = $writeDb->prepare('DELETE FROM tbl_tasks WHERE id= :task_id');
            $query->bindParam(':task_id', $task_id, PDO::PARAM_INT);
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
            $query = $readDB->prepare('SELECT id, title, description, DATE_FORMAT(deadline, "%d/%m/%Y %H:%i") as deadline, completed
            FROM tbl_tasks WHERE completed = :completed');
            $query->bindParam(':completed', $completed, PDO::PARAM_STR);
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
                $taskArray[] =  $task->returnTaskAsArray();
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
            $query = $readDB->prepare('SELECT count(id) as totalNoOfTasks FROM tbl_tasks');
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

            $query = $readDB->prepare('SELECT id, title, description, DATE_FORMAT(deadline, "%d/%m/%Y %H:%i") as deadline, completed
            FROM tbl_tasks limit :pgLimit offset :offset');

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
                $taskArray[] =  $task->returnTaskAsArray();
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
            $query = $readDB->prepare(
                'SELECT id, title, description, DATE_FORMAT(deadline, "%d/%m/$Y %H:%i") as deadline, completed 
                            FROM tbl_tasks'
            );
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
            INSERT INTO tbl_tasks (title, description, deadline, completed) values 
            (:title, :description, STR_TO_DATE(:deadline, \'%d/%m/%Y %H:%i\'), :completed)
            ');

            $query->bindParam(':title', $title, PDO::PARAM_STR);
            $query->bindParam(':description', $description, PDO::PARAM_STR);
            $query->bindParam(':deadline', $deadline, PDO::PARAM_STR);
            $query->bindParam(':completed', $completed, PDO::PARAM_STR);

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
            SELECT id, title, description, DATE_FORMAT(deadline, "%d/%m/%Y %H:%i") as deadline, completed
            FROM tbl_tasks WHERE id = :taskid
            ');

            $query->bindParam(':taskid', $lastTaskId, PDO::PARAM_INT);
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
