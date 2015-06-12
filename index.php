<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<!--启用session!-->
<?php 
session_start(); 
//session_unset();
?>


<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1.0" />
<title>Conquer</title>

<link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap.min.css" />
<link rel="stylesheet" href="styles.css" type="text/css" media="screen" charset="utf-8">

<style type="text/css">


/*
.box {
	filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#6699FF', endColorstr='#6699FF');
	background-image:linear-gradient(bottom, #6699FF 0%, #6699FF 100%);
	background-image:-o-linear-gradient(bottom, #6699FF 0%, #6699FF 100%);
	background-image:-moz-linear-gradient(bottom, #6699FF 0%, #6699FF 100%);
	background-image:-webkit-linear-gradient(bottom, #6699FF 0%, #6699FF 100%);
	background-image:-ms-linear-gradient(bottom, #6699FF 0%, #6699FF 100%);
	
	margin: 0 auto;
	position: relative;
	width: 100%;
	height: 100%;
}
*/

.login-box {
	width: 100%;
	max-width:500px;
	height: 400px;
	position: absolute;
	top: 50%;

	margin-top: -200px;
	/*设置负值，为要定位子盒子的一半高度*/
	
}
@media screen and (min-width:500px){
	.login-box {
		left: 50%;
		/*设置负值，为要定位子盒子的一半宽度*/
		margin-left: -250px;
	}
}	

.form {
	width: 100%;
	max-width:500px;
	height: 275px;
	margin: 25px auto 0px auto;
	padding-top: 25px;
}	
.login-content {
	height: 300px;
	width: 100%;
	max-width:500px;
	background-color: rgba(255, 250, 2550, .6);
	float: left;
}		
	
	
.input-group {
	margin: 0px 0px 30px 0px !important;
}
.form-control,
.input-group {
	height: 40px;
}

.form-group {
	margin-bottom: 0px !important;
}
.login-title {
	padding: 20px 10px;
	background-color: rgba(0, 0, 0, .6);
}
.login-title h1 {
	margin-top: 10px !important;
}
.login-title small {
	color: #fff;
}

.link p {
	line-height: 20px;
	margin-top: 30px;
}
.btn-sm {
	padding: 8px 24px !important;
	font-size: 16px !important;
}
</style>
<script type="text/javascript">
	function lackInfo(){
		alert("用户名和密码不能为空！");
	}

	function wrongUser(){
		alert("用户名或密码错误！");
	}
</script>
</head>

<body>
<?php
// define variables and set to empty values
//$name = $email = $gender = $comment = $website = "";
$username = $password = "";
if ($_SERVER["REQUEST_METHOD"] == "POST")
{
   //$name = test_input($_POST["name"]);
   //$email = test_input($_POST["email"]);
   //$website = test_input($_POST["website"]);
   //$comment = test_input($_POST["comment"]);
   //$gender = test_input($_POST["gender"]);

	//echo 'hello';
	$username = test_input($_POST["username"]);
	$password = test_input($_POST["password"]);
	$_SESSION['valid'] = false;
	$_SESSION['username'] = $username;
	$_SESSION['password'] = $password;
	//$username = $_POST["username"];
	//$password = $_POST["password"];
}


//convert to HTML
function test_input($data)
{
   $data = trim($data);
   $data = stripslashes($data);
   $data = htmlspecialchars($data);
   return $data;
}
?>

<nav class="navbar navbar-inverse" role="navigation">
   <div class="navbar-header">
      <a class="navbar-brand" href="#">Hello commander </a>
   </div>
   <div>
      <ul class="nav navbar-nav">
         <li class="default"><a href="#"><?php echo ""?></a></li>
      </ul>
   </div>
</nav>

<div class="box">
		<div class="login-box">
			<div class="login-title text-center">
				<h1><small>登录</small></h1>
			</div>
			<div class="login-content ">
			<div class="form">
			<form  method="post" action="#">
				<div class="form-group">
					<div class="col-xs-12  ">
						<div class="input-group">
							<span class="input-group-addon"><span class="glyphicon glyphicon-user"></span></span>
							<input type="text" id="username" name="username" class="form-control" placeholder="用户名">
						</div>
					</div>
				</div>
				<div class="form-group">
					<div class="col-xs-12  ">
						<div class="input-group">
							<span class="input-group-addon"><span class="glyphicon glyphicon-lock"></span></span>
							<input type="password" id="password" name="password" class="form-control" placeholder="密码">
						</div>
					</div>
				</div>
				<div class="form-group form-actions">
					<div class="col-xs-4 col-xs-offset-5 ">
						<button type="submit" class="btn btn-sm btn-info"> 登录</button>
					</div>
				</div>
				<div class="form-group">
					<!--
					<div class="col-xs-6 link">
						<p class="text-center remove-margin"><small>忘记密码？</small> <a href="javascript:void(0)" ><small>找回</small></a>
						</p>
					</div>
					-->
					<div class="col-xs-6 link">
						<p class="text-center remove-margin"><small>还没注册?</small> <a href="register.php" ><small>注册</small></a>
						</p>
					</div>
				</div>
			</form>
			</div>
		</div>
	</div>
</div>


<!--++++++++++++++++++++++++++++++++=验证登录信息++++++++++++++++++++++++++++++++++++++++++++++++-->
<?php
	//echo $username;
	//echo "<br>";
	//echo $password;
	//echo "<br>";
	if($username && $password){
		$servername = "localhost";
		$db_username="root";
		$db_password = "19940807";
		$db_name = "rts";

		$conn = new mysqli($servername,$db_username,$db_password,$db_name);
		if ($conn->connect_error){
	    	die("Connection failed: " . $conn->connect_error);
		}
		//echo "success";

		$sql = "select * from user_info where uname = '$username' and passwd = '$password'";
		$result = $conn->query($sql)
			or die ("Fatal error");

		//if($result->num_rows == 0)
		//	echo "用户名或密码错误！";
		//else
		//echo "登陆成功！";
		$conn->close();

		if($result->num_rows>0){
			//验证成功，进行跳转
			$_SESSION['valid'] = true;
			$_SESSION['username'] = $username;
			$_SESSION['password'] = $password;
			?>

			<script language="javascript">
    			window.location= "home.php";
			</script>
			<?php
		}   
		else{
			?>
			<script type="text/javascript">wrongUser();</script>
			<?php

		}
	}
	else{
		if($_SERVER["REQUEST_METHOD"] == "POST"){
			?>
			<script type="text/javascript">lackInfo();</script>
			<?php
		}

		
	}
	
?>


<!--+++++++++++++++++++++++++++++++++foot++++++++++++++++++++++++++++++++++++++-->
<div style="text-align:center;">
	<p>copyright © Jason-Zhang@ZJU</p>
</div>

</body>

</html>