<?
session_start();
$conn = mysqli_connect('127.0.0.1', 'root', '', 'beejee');
if (!$conn){
    echo 'ERROR';
    exit();
}
