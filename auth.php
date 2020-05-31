<?php
session_start();
    if (isset($_POST['login'])){
        $u = $_POST['username'];
        $p = $_POST['password'];
        if (empty($u) || empty($p)){
            $error = 'Все поля должны быть заполнены';
        }
        else{
            if ($u == 'admin' && $p == '123'){
                $_SESSION['log'] = 'admin';
                header('Location: index.php');
            }
            else{

                $error = 'Неверный логин или пароль';
            }
        }
    }
    unset($_SESSION['page']);
    unset($_SESSION['sort']);
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <title>Login</title>
</head>
<body>
<nav class="bg-dark p-2">
    <a href="index.php" class="text-decoration-none text-light m-2">Home</a>
    <a href="auth.php" class="text-decoration-none text-light m-2">Login</a>
</nav>
<div class="m-5">
   <div class="justify-content-center d-flex">
       <div class="w-50">
           <h3 class="text-center text-dark">Login</h3>
           <div class="bg-light text-center rounded-lg border">
               <form action="auth.php" method="post" class="p-3">
                   <div class="d-flex justify-content-center">
                       <div class="w-75">
                           <div class="m-3">
                               <input class="w-100 input-group-text" maxlength="100" name="username" type="text" placeholder="Username">
                           </div>
                           <div class="m-3">
                               <input class="w-100 input-group-text" maxlength="100" name="password" type="password" placeholder="Password">
                           </div>
                           <span class="text-danger"><?=$error?></span>
                           <div class="m-3">
                               <input class="btn btn-dark" name="login" type="submit" value="Login">
                           </div>
                       </div>
                   </div>
               </form>
           </div>
       </div>
   </div>
</div>
</body>
</html>
