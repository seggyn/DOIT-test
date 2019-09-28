<?php

$LIMIT = 5;

// VERIFY TOKEN
if (!isset($_COOKIE['token']) || empty($_COOKIE['token'])) {
    header('Location: '.$_CONFIG['base_url'].'login');
}

// CONNECT TO DB
$db = DB::connect('localhost:8889', 'doit_test', 'root', 'root');

// FUNCTION FOR CONVERTING PRIORITY NUMBER TO WORD
function get_priority($num) {
    $array = ['High', 'Normal', 'Low'];
    return $array[$num-1];
}

// GET EMAIL FOR GETTING TASK
$result = $db->select_one('users', ['email'], ['token' => $_COOKIE['token']]);
$user_email = $result['email'];

// FILTERS
if (isset($_GET['field']) && !empty($_GET['field']) &&
    isset($_GET['order']) && !empty($_GET['order'])) {
    $tasks = $db->select('tasks', ['id','title','due_date','priority','is_done'],
        ['email' => $user_email], [$_GET['field'] => $_GET['order']]);
} else {
    $tasks = $db->select('tasks', ['id','title','due_date','priority','is_done'], ['email' => $user_email]);
}

// PAGINATION
$page_count = ceil((count($tasks)/$LIMIT));
$page_slice = ($_GET['page'] > 1) ? $LIMIT * ($_GET['page'] - 1) : 0;

$tasks = array_slice($tasks, $page_slice, $LIMIT);

?>
<html>
<head>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/css/fontawesome.min.css" integrity="sha256-/sdxenK1NDowSNuphgwjv8wSosSNZB0t5koXqd7XqOI=" crossorigin="anonymous" />    <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/css/all.css" integrity="sha256-46qynGAkLSFpVbEBog43gvNhfrOj+BmwXdxFgVK/Kvc=" crossorigin="anonymous" />

    <title>TODO List</title>
    <?php include "views/const.php"; ?>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <a class="navbar-brand" href="#">TODO List</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
            <div class="navbar-nav">
                <a class="nav-item nav-link active" href="<?=$_CONFIG['path']?>list">List</a>
            </div>
        </div>
    </nav>
    <div class="col-10 offset-1">
        <div class="row">
            <div class="col-6">
                <button type="button" class="btn btn-success my-2" data-toggle="modal" data-target="#taskModal">Create new task</button>
            </div>
            <div class="col-6">
                <form class="form-inline mt-2">
                    <div class="col-3">
                        Filters:
                    </div>
                    <div class="form-group col-3">
                        <select class="form-control form-control-sm" name="field" data-task="<?=$task['id'] ?>">
                            <option value="id" <?php if ($_GET['field'] == 'id'): ?>selected<?php endif; ?>>ID</option>
                            <option value="due_date" <?php if ($_GET['field'] == 'due_date'): ?>selected<?php endif; ?>>Due date</option>
                            <option value="priority" <?php if ($_GET['field'] == 'priority'): ?>selected<?php endif; ?>>Priority</option>
                        </select>
                    </div>
                    <div class="form-group col-3">
                        <select class="form-control form-control-sm" name="order" data-task="<?=$task['id'] ?>">
                            <option value="ASC" <?php if ($_GET['order'] == 'ASC'): ?>selected<?php endif; ?>>ASC</option>
                            <option value="DESC" <?php if ($_GET['order'] == 'DESC'): ?>selected<?php endif; ?>>DESC</option>
                        </select>
                    </div>
                    <div class="form-group col-3">
                        <button class="btn btn-primary" type="submit">Apply</button>
                    </div>
                </form>
            </div>

        </div>

        <div class="modal fade" id="taskModal" tabindex="-1" role="dialog" aria-labelledby="taskModal" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">

                    <form id="newTask">
                        <div class="modal-header">
                            <h5 class="modal-title task-title" id="exampleModalLongTitle">Add new task</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>

                        <div class="modal-body">
                            <div class="form-group">
                                <input type="text" name="title" class="form-control" id="title" placeholder="Title">
                            </div>
                            <div class="form-group">
                                <input type="text" name="due_date" pattern="[0-9]{4}.[0-9]{2}.[0-9]{2} [0-9]{2}:[0-9]{2}" class="form-control" id="due_date" placeholder="Due date: 2019.01.21 13:00">
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button class="btn btn-primary" type="submit">Add</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>

        <div class="modal fade" id="editTaskModal" tabindex="-1" role="dialog" aria-labelledby="editTaskModal" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">

                    <form id="editTask">
                        <div class="modal-header">
                            <h5 class="modal-title task-title" id="exampleModalLongTitle">Edit task</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>

                        <div class="modal-body">
                            <div class="form-group">
                                <input type="text" name="title" class="form-control" id="task_title" placeholder="Title">
                            </div>
                            <div class="form-group">
                                <input type="text" name="due_date" pattern="[0-9]{4}.[0-9]{2}.[0-9]{2} [0-9]{2}:[0-9]{2}" class="form-control" id="task_due_date" placeholder="Due date: 2019.01.21 13:00">
                            </div>
                            <input type="hidden" name="task_id" class="form-control" id="task_id">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button class="btn btn-primary btn-task-submit" type="submit">Edit</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>

        <table class="table table-striped table-dark">
            <tbody>
            <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Due date</th>
                <th>Priority</th>
                <th>Action</th>
            </tr>
            </thead>
            <?php foreach ($tasks as $task): ?>
            <tr class="<?php if ($task['is_done'] == 1): ?>bg-success<?php endif; ?>">
                <th scope="row"><?=$task['id'] ?></th>
                <td><?=$task['title'] ?></td>
                <td><?=date('Y.m.d h:i', $task['due_date']) ?></td>
                <td>
                    <?php if ($task['is_done'] == 0): ?>
                    <select class="form-control form-control-sm" id="priority" data-task="<?=$task['id'] ?>">
                        <option <?php if ($task['priority'] == 3): ?>selected<?php endif; ?> value="3">Low</option>
                        <option <?php if ($task['priority'] == 2): ?>selected<?php endif; ?> value="2">Normal</option>
                        <option <?php if ($task['priority'] == 1): ?>selected<?php endif; ?> value="1">High</option>
                    </select>
                    <?php else: ?>
                        <?=get_priority($task['priority']) ?>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if ($task['is_done'] == 0): ?>
                    <div>
                        <a role="button" class="btn btn-success btn-done text-dark" data-task="<?=$task['id'] ?>"><i class="fa fa-check"></i></a>
                        <a data-toggle="modal" data-target="#editTaskModal" role="button" class="btn btn-warning btn-edit text-dark" data-task="<?=$task['id'] ?>"><i class="fa fa-pencil-alt"></i></a>
                        <a role="button" class="btn btn-danger btn-remove text-dark" data-task="<?=$task['id'] ?>"><i class="fa fa-trash"></i></a>
                    </div>
                    <?php endif ?>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <div class="pagination d-flex justify-content-center">
            <nav class="text-center">
                <ul class="pagination">
                    <li class="page-item <?php if($_GET['page'] == 1 || (!isset($_GET['page']) && empty($_GET['page']))): ?>disabled<?php endif; ?>">
                        <a class="page-link" href="?page=1" tabindex="-1">1</a>
                    </li>
                    <?php for($i = 2; $i <= $page_count; $i++): ?>
                    <li class="page-item <?php if($i == $_GET['page']): ?>disabled<?php endif; ?>">
                        <a class="page-link" href="?page=<?=$i ?>"><?=$i ?></a>
                    </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        </div>
    </div>
<!--    <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>-->
    <script src="https://code.jquery.com/jquery-3.4.1.js" integrity="sha256-WpOohJOqMqqyKL9FccASB9O0KwACQJpFTUBLTYOVvVU=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>

    <script src="assets/js/cookie.js" type="application/javascript"></script>
    <script src="assets/js/script.js" type="application/javascript"></script>

</body>
</html>
