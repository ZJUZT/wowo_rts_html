<!DOCTYPE html>
<?php
session_start(); 
if(!isset($_SESSION['valid'])|| $_SESSION['valid']!=true){
	?>
		<script language="javascript">
    		window.location= "index.php";
		</script>
	<?php
}
?>
<html>
	<head>
		<meta http-equiv="Content-type" content="text/html; charset=utf-8">
		<title>Conquer</title>
		<script src="js/common.js" type="text/javascript" charset="utf-8"></script>		
		<script src="js/jquery.min.js" type="text/javascript" charset="utf-8"></script>
		<script src="js/game.js" type="text/javascript" charset="utf-8"></script>
		<script src="js/mouse.js" type="text/javascript" charset="utf-8"></script>		
		<script src="js/singleplayer.js" type="text/javascript" charset="utf-8"></script>
		<script src="js/maps.js" type="text/javascript" charset="utf-8"></script>
		<link rel="stylesheet" href="styles.css" type="text/css" media="screen" charset="utf-8">
		<link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap.min.css" />

	</head>

	<body>

    <?php  require 'navigation.php'; ?>

	<!--主体!-->
	    <div class = "box">
	        <div id="gamestartscreen" class="gamelayer">
	            <span id="singleplayer" onclick = "singleplayer.start();">个人战役</span><br>
	            <span id="multiplayer" onclick = "multiplayer.start();">多人游戏</span><br>
	            <span id="lookuprank" onclick = "lookuprank.start();">查看排名</span><br>
	            <span id="personalinfo" onclick = "personalinfo.start();">个人战绩</span><br>
	        </div>
	        <div id="missionscreen" class="gamelayer">
	            <input type="button" id="entermission" onclick = "singleplayer.play();">
	            <input type="button" id="exitmission" onclick = "singleplayer.exit();">
	            <div id="missonbriefing">Welcome to your first mission.
	            </div>
	        </div>
	        <div id="gameinterfacescreen" class="gamelayer">
	            <div id="gamemessages"></div>        
	            <div id="callerpicture"></div>
	            <div id="cash"></div>
	            <div id="sidebarbuttons">                                
	            </div>
	            <canvas id="gamebackgroundcanvas" height="400" width="480"></canvas>
	            <canvas id="gameforegroundcanvas" height="400" width="480"></canvas>
	        </div>
	        <div id="loadingscreen" class="gamelayer">
	            <div id="loadingmessage"></div>
	        </div>            
	    </div>
	    <div style="text-align:center;">
			<p>copyright © Jason-Zhang@ZJU</p>
		</div>
	</body>
	

</html>