
<!--导航栏-->
<nav class="navbar navbar-inverse" role="navigation">
   <div class="navbar-header">
      <a class="navbar-brand" href="home.php">Hello commander </a>
   </div>
   <div>
      	<ul class="nav navbar-nav navbar-right">
      	<li class="dropdown">
	        <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php if(isset($_SESSION['username']))  echo $_SESSION['username'];?><b class="caret"></b></a>
	        <ul class="dropdown-menu">
	          <li><a href="history.php">History</a></li>
	          <li><a href="rank.php">Rank</a></li>
	          <li class="divider"></li>
	          <li><a href="logout.php">logout</a></li>
	        </ul>
	     </li>
         	
		<!--
		<ul class="nav navbar-nav navbar-right">
			<li class="default"><a href="logout.php">Logout</a></li>
		</ul>
		-->
	</div>
</nav>