<?php
require_once "connection.php";
if (isset($_GET['status'])){
    if ($_SESSION['log']){
        $id = ltrim($_GET['status'], 'checkbox');
        mysqli_query($conn, "Update tasks set status = !status where id = $id");
    } else{
        $user = 'First of all authorize please';
    }
}
if (isset($_GET['logout'])){
    unset($_SESSION['log']);
}
$pass = true;
$m = false;
$currentPage = 0;
if(isset($_POST['add'])){
    $u = htmlspecialchars($_POST['username']);
    $e = htmlspecialchars($_POST['email']);
    $d = htmlspecialchars($_POST['description']);
    if (empty($u)){
        $uErr = 'Заполните поле';
    }
    if (empty($e)){
        $eErr = 'Заполните поле';
    }
    else{
        if (!filter_var($e,  FILTER_VALIDATE_EMAIL)) {
            $eErr = 'Неправильный формат email';
        }
    }
    if (empty($d)){
        $dErr = 'Заполните поле';
    }
    $errors = array($uErr, $eErr, $dErr);
    $pass = !($errors[0] || $errors[1] || $errors[2]);
    if ($pass){
        $m = true;
        $res = mysqli_query($conn, "insert into tasks (username, email, description, status) values ('$u', '$e', '$d', false )");
    }
}
if(isset($_POST['modify'])){

    $editD = htmlspecialchars($_POST['editedDescription']);
    if ($_SESSION['log']){
        if (!empty($editD)){
            $id = ltrim($_POST['description'], 'description');
            $prevDescr = mysqli_query($conn, "Select description from tasks where id = $id");
            $prevDescr = mysqli_fetch_assoc($prevDescr);
            $prevDescr = $prevDescr['description'];
            if ($prevDescr != $editD){
                $update = true;
                mysqli_query($conn, "Update tasks set description = '$editD', updated = true where id = $id");
            }
        }
    } else{
        $user = 'First of all authorize please';
    }
}
if (isset($_GET['sort'])){
    $_SESSION['sort'] = $_GET['sort'];
    }
if (isset($_SESSION['sort'])){
    $sort = $_SESSION['sort'];
    if (substr($sort, -1) == '0'){
        $sort = substr_replace($sort ,"",-1);
        $tasks = mysqli_query($conn, "Select * from tasks order by $sort");
    }
    else{
        $tasks = mysqli_query($conn, "Select * from tasks order by $sort desc");
    }
}
else{
    $tasks = mysqli_query($conn, "Select * from tasks");
}
    $constSort = $_SESSION['sort'];

    $L = array();
    while ($t =  mysqli_fetch_assoc($tasks)){
        array_push($L, $t);
    }
    if (isset($_GET['page'])){
        $_SESSION['page'] = $_GET['page'];
    }
        $currentPage = $_SESSION['page'];
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tasks</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/bootstrap-grid.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/bootstrap-reboot.min.css">
    <script type="text/javascript" src="js/bootstrap.min.js"></script>
    <script type="text/javascript" src="js/bootstrap.bundle.min.js"></script>
</head>

<body>
<nav class="bg-dark p-2">
    <a href="index.php" class="text-decoration-none text-light m-2">Home</a>
    <?
    if (isset($_SESSION['log'])){
        echo "<a href='index.php?logout=3' id='logout' class=\"text-decoration-none text-light m-2\">Logout</a>";
    }
    else{
        echo "<a href=\"auth.php\" class=\"text-decoration-none text-light m-2\">Login</a>";
    }
    ?>
</nav>
<main>
    <div class="justify-content-center d-flex">
        <div id="M" style="min-height: 18px; opacity: 85%; z-index: 10; display: <?=($m)? 'block': 'none'?>;"
             class="bg-success text-center text-white m-4 w-50 rounded-lg position-absolute">
            <h2 class="text-white">Task Added Successfully!</h2>
        </div>
        <div id="user" style="min-height: 18px; opacity: 90%; z-index: 10; display: <?=($user)? 'block': 'none'?>;"
             class="bg-danger text-center text-white m-4 w-50 rounded-lg position-absolute">
            <h2 class="text-white"><?=$user?></h2>
        </div>
    </div>
    <div>
        <h1 class="btn-outline-primary font-weight-normal text-center m-2 rounded-lg p-2">Tasks</h1>
    </div>
    <button id="addbtn" onclick="Dialog()" class="btn btn-outline-success position-fixed m-1" style="display: <?=(!$pass)? 'none' : 'block' ?>">
        Add a task
    </button>

    <div class="justify-content-center d-flex">
        <div id="dialog" class="rounded-lg position-fixed p-3 m-5" style="z-index: 2; display: <?=(!$pass)? 'block' : 'none' ?>; background: lightblue;">
           <div>
               <button onclick="Dialog()" class="position-absolute btn btn-danger" style="right: 1%">
                   X</button>
               <h3 class="font-weight-normal text-light text-center">Details of Task</h3>

           </div>
            <form action="index.php" method="post">
                <div class="m-3">
                    <input class="w-100 border p-1 rounded-lg <?if ($errors[0]) echo 'border-danger'?>" maxlength="100" type="text" name="username" placeholder="Username">
                    <span class="text-danger">
                        <?=$errors[0]?>
                    </span>
                </div>
                <div class="m-3">
                    <input class="w-100 border p-1 rounded-lg <?if ($errors[1]) echo 'border-danger'?>" maxlength="100" type="text" name="email" placeholder="E-mail">
                    <span class="text-danger">
                        <?=$errors[1]?>
                    </span>
                </div>
                <div class="m-3">
                    <textarea class="rounded-lg p-1 <?if ($errors[2]) echo 'border-danger'?>" cols="100" rows="8" name="description" placeholder="Description" style="resize: none;"></textarea>
                    <span class="text-danger d-block">
                        <?=$errors[2]?>
                    </span>
                </div>
                <div class="m-3">
                    <input class="btn w-100 btn-outline-dark" type="submit" name="add" value="Add">
                </div>
            </form>
        </div>
    </div>
   <div class="d-flex justify-content-center m-4">
       <div class="card w-75">
           <div class="card-body">
               <div class="row">
                   <div class="btn-group border-right col-3">
                       <div class=" dropdown-toggle" data-toggle="dropdown" style="cursor: pointer">Username</div>
                           <div class="dropdown-menu" aria-labelledby="u">
                               <a class="dropdown-item" href="index.php?sort=username0">A-Z</a>
                               <a class="dropdown-item" href="index.php?sort=username">Z-A</a>
                           </div>
                   </div>
<!--                   <div onclick="sort('username')" class="col-3  dropdown-toggle" style="cursor: pointer">Username</div>-->
                   <div class="btn-group border-right col-3">
                       <div onclick="sort('email')" class="dropdown-toggle" data-toggle="dropdown" style="cursor: pointer">E-mail</div>
                           <div class="dropdown-menu" aria-labelledby="e">
                               <a class="dropdown-item" href="index.php?sort=email0">A-Z</a>
                               <a class="dropdown-item" href="index.php?sort=email">Z-A</a>
                           </div>
                   </div>
                   <div class="col border-right">Description</div>
                   <div class="btn-group col-1">
                       <div class="dropdown-toggle" data-toggle="dropdown" style="cursor: pointer">Status</div>
                       <div class="dropdown-menu">
                           <a class="dropdown-item" href="index.php?sort=status0">Unfinished first</a>
                           <a class="dropdown-item" href="index.php?sort=status">Finished first</a>
                       </div>
                   </div>
               </div>
               <div id="content">
                   <?
                   $p = ceil(count($L)/3);
                   $onePage = $currentPage*3+3;
                   if ($onePage>=count($L)){
                       $onePage = count($L);
                   }
                   for ($i = $currentPage*3; $i < $onePage; $i++){
                       echo "<hr>
               <div class=\"row\">
                   <div class=\"col-3 border-right\">".$L[$i]['username']."</div>
                   <div class=\"col-3 border-right\">".$L[$i]['email']."</div>
                   <div class=\"col border-right\">
                   <form method='post' action='index.php' class=''>
                   <textarea rows='3' name=\"editedDescription\" style='margin: 0; resize: none; width: 100%; height: 100%'
                   ".(($_SESSION['log'])? "": "readonly").">".$L[$i]['description']."</textarea>";
                    if ($_SESSION['log']){
                        echo "<input hidden name='description' value='description".$L[$i]['id']."'>";
                        echo "<input class=\"btn w-100 p-0 btn-outline-dark\" type=\"submit\" name=\"modify\" value=\"Modify\">";
                    }
                   echo "</form>
                   </div>
                   <div class=\"col-1\">
                        <div class=\"form-check text-center\">
                            <input class=\"form-check-input\" id='checkbox".$L[$i]['id']."' type=\"checkbox\"";
                       if ($L[$i]['status']){
                           echo " checked value='1'";
                       }
                       else echo " value='0'";
                       if (!$_SESSION['log']){
                           echo ' disabled';
                       }
                       echo ">".
                           (($L[$i]['updated'])?"<i class=\"fa fa-edit\"></i>":"")
                        ."</div>
                   </div>
                </div>";

                   }
                   ?>
                   <nav class="mt-3">
                       <ul class="pagination">
                           <?
                               for ($i = 0; $i < $p; $i++){
                                   echo "<li class=\"page-item\"><a id='$i' class=\"page-link".(($currentPage == $i)? ' bg-warning': '') ." \" href=\"index.php?page=$i\">".(1+$i)."</a></li>";
                               }
                           ?>
                       </ul>
                   </nav>
               </div>

           </div>
       </div>
   </div>
</main>
<script  type="text/javascript" src="http://code.jquery.com/jquery-1.10.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
<script>
    setTimeout(fade_out, 3000);
    setTimeout(fade_out1, 3000);

    function fade_out() {
        $("#M").fadeOut().empty();
    }
    function fade_out1() {
        $("#user").fadeOut().empty();
    }
    function Dialog() {
        var d = document.getElementById('dialog').style.display;
        document.getElementById('dialog').style.display = document.getElementById('addbtn').style.display;
        document.getElementById('addbtn').style.display = d;
    }
    $(document).ready(function () {
        $('#logout').click(function () {
            $('#formSubmit').submit();
        });
    $('.form-check-input').on('change', function () {
        $v = $(this).attr('id');
        console.log('NN:', $v);
        $.ajax({
            type : 'get',
            url : 'index.php',
            data : {'status': $v},
        });
    })
    });
</script>
</body>
</html>
