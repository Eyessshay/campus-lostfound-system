<?php

$servername = "localhost";
$username = "root";
$password = "241002961";
$database = "campus_lostfound";

$conn = mysqli_connect(
    $servername,
    $username,
    $password,
    $database
);

if (!$conn) {
    die("数据库连接失败：" . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8mb4");

?>