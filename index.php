<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <link rel="shortcut icon" href="https://www.assets.gopibabu.live/images/favicon-32x32.png" type="image/x-icon">
    <title>PHP API</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" />
    <script src="https://kit.fontawesome.com/078c5ccadb.js" crossorigin="anonymous"></script>
</head>

<body>
    <div class="container">
        <h1 class="text-center display-3">PHP API</h1>
        <h3 class="text-center display-4">List of APIs available</h3>
        <button id="getTask" type="button" class="btn btn-lg btn-primary form-control mt-5" data-toggle="modal" data-target="#urlModal">
            GET TASK
        </button>
        <button id="deleteTask" type="button" class="btn btn-lg btn-warning form-control mt-3" data-toggle="modal" data-target="#urlModal">
            DELETE TASK
        </button>
        <button id="completedTasks" type="button" class="btn btn-lg btn-success form-control mt-3" data-toggle="modal" data-target="#urlModal">
            GET ALL COMPLETED TASKS
        </button>
        <button id="incompletedTasks" type="button" class="btn btn-lg btn-secondary form-control mt-3" data-toggle="modal" data-target="#urlModal">
            GET ALL INCOMPLETED TASKS
        </button>
        <button id="allTasks" type="button" class="btn btn-lg btn-info form-control mt-3" data-toggle="modal" data-target="#urlModal">
            GET ALL TASKS
        </button>

    </div>

    <div class="modal fade" id="urlModal" tabindex="-1" role="dialog" aria-labelledby="urlModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">GET TASK</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div id="mainModalContent" class="modal-body border border-primary shadow-sm m-3">
                    https://restapi.gopibabu.live
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>


    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
    <script src="./js/main.js"></script>
</body>

</html>