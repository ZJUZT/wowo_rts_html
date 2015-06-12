<?php
/**
 * Created by PhpStorm.
 * User: think
 * Date: 2015/6/10
 * Time: 23:47
 */
?>

<!--导航栏-->
	<nav class="navbar navbar-inverse" role="navigation">
	   <div class="navbar-header">
	      <a class="navbar-brand" href="#">Hello commander </a>
	   </div>
	   <div>
	      <ul class="nav navbar-nav">
	         <li class="default"><a href="#"><?php if (isset($_SESSION['username']))  echo $_SESSION['username'];?></a></li>
</ul>
<ul class="nav navbar-nav navbar-right">
    <li class="default"><a href="logout.php">Logout</a></li>
</ul>
</div>
</nav>