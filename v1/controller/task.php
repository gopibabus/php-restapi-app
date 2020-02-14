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
}
