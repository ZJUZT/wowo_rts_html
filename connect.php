<?php header("Content-type:text/html;charset=utf8");?>
<?php
// Connect database
$con = mysql_connect("localhost","root","123456");

mysql_select_db('rts', $con);

mysql_query("SET NAMES 'utf8'");
?>