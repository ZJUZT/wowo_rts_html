<?php
/**
 * Created by PhpStorm.
 * User: think
 * Date: 2015/6/24
 * Time: 20:08
 */

require('connect.php');
session_start();
$username = $_SESSION['username'];
$u_id = $_SESSION['u_id'];
$loser = $_GET['loser'];
$opponent = $_GET['opponent'];
$battle_period = $_GET['last'];
$start_time = $_GET['begin'];



//$sql = "select * from rank where u_name = '$username' ";
//$query = mysql_query($sql);
//$row_num = mysql_num_rows($query);


if($loser!=$username){
    $sql = "insert into battle(begin_time,last_time,user1_name,user2_name,winner_name) VALUES ($start_time,$battle_period,'$username','$opponent','$username')";
    $query = mysql_query($sql)
    or die($username);
    $sql = "update rank set play_count = play_count + 1,win_count=win_count+1 where u_name = '$username'";
    $query = mysql_query(($sql))
    or die("error2");
    $sql = "update rank set win_rate = win_count/play_count where u_name = '$username' ";
    $query = mysql_query(($sql))
    or die("error3");
}

else{
    $sql = "insert into battle(begin_time,last_time,user1_name,user2_name,winner_name) VALUES ($start_time,$battle_period,'$username','$opponent','$opponent')";
    $query = mysql_query($sql)
    or die($username);
    $sql = "update rank set play_count = play_count + 1 where u_name = '$username'";
    $query = mysql_query(($sql))
    or die("error2");
    $sql = "update rank set win_rate = win_count/play_count where u_name = '$username' ";
    $query = mysql_query(($sql))
    or die("error3");
}






header('Location:home.php');
exit;