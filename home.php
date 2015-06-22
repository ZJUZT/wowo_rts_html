<!DOCTYPE html>
<?php
require('checkvalid.php');
?>
<html>
	<head>
		<meta http-equiv="Content-type" content="text/html; charset=utf-8">
		<title>Last Colony</title>
		<script src="js/common.js" type="text/javascript" charset="utf-8"></script>		
		<script src="js/jquery.min.js" type="text/javascript" charset="utf-8"></script>
		<script src="js/game.js" type="text/javascript" charset="utf-8"></script>
		<script src="js/mouse.js" type="text/javascript" charset="utf-8"></script>		
		<script src="js/singleplayer.js" type="text/javascript" charset="utf-8"></script>
		<script src="js/maps.js" type="text/javascript" charset="utf-8"></script>

		<!-- Definitions for game entities -->        
	    <script src="js/buildings.js" type="text/javascript" charset="utf-8"></script>
	    <script src="js/vehicles.js" type="text/javascript" charset="utf-8"></script>
	    <script src="js/aircraft.js" type="text/javascript" charset="utf-8"></script>        
	    <script src="js/terrain.js" type="text/javascript" charset="utf-8"></script>
	
		<!-- A* Implementation by Andrea Giammarchi -->
		<script src="js/astar.js" type="text/javascript" charset="utf-8"></script>
		
		<script src="js/sidebar.js" type="text/javascript" charset="utf-8"></script>
		
		<script src="js/bullets.js" type="text/javascript" charset="utf-8"></script>
		<script src="js/fog.js" type="text/javascript" charset="utf-8"></script>
		
		<script src="js/sounds.js" type="text/javascript" charset="utf-8"></script>
		
		<script src="js/multiplayer.js" type="text/javascript" charset="utf-8"></script>

        <link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap.min.css" />
		
		<link rel="stylesheet" href="css/styles.css" type="text/css" media="screen" charset="utf-8">
	</head>
	<body>
    <?php  require ('navigation.php'); ?>
	    <div id="gamecontainer" class = "box">
	        <div id="gamestartscreen" class="gamelayer">
	            <!--<span id="singleplayer" onclick = "singleplayer.start();">单人模式</span><br>!-->
	            <span id="multiplayer" onclick = "multiplayer.start();">开始游戏</span><br>
                <!--<span id="lookuprank" onclick = "lookuprank.start();">查看排名</span><br>-->
                <!--<span id="personalinfo" onclick = "personalinfo.start();">个人战绩</span><br>-->
	        </div>
	        <!--
	        <div id="missionscreen" class="gamelayer">
	            <input type="button" id="entermission" onclick = "singleplayer.play();">
	            <input type="button" id="exitmission" onclick = "singleplayer.exit();">
	            <div id="missonbriefing">Welcome to your first mission.
	            </div>
	        </div>
	        -->
			<div id="gameinterfacescreen" class="gamelayer">
			    <div id="gamemessages"></div>        
			    <div id="callerpicture"></div>
			    <div id="cash"></div>
			    <div id="sidebarbuttons">                    
			        <input type="button" id="starportbutton" title = "Starport"> &nbsp;
			        <input type="button" id="turretbutton" title = "Turret">

			        <input type="button" id="scouttankbutton" title = "Scout Tank">&nbsp;
			        <input type="button" id="heavytankbutton" title = "Heavy Tank">
			        <input type="button" id="harvesterbutton" title = "Harvester">&nbsp;

			        <input type="button" id="chopperbutton" title = "Copter">
			        <input type="button" id="wraithbutton" title = "Wraith">

			    </div>
			    <!--Canvas-->
			    <canvas id="gamebackgroundcanvas" height="430" width="760"></canvas>
			    <canvas id="gameforegroundcanvas" height="430" width="760"></canvas>

			    <!--chat-->
			    <input type="text" id="chatmessage"></input>  
			</div>
			
			<div id="messageboxscreen" class="gamelayer">
	            <div id="messagebox">
	                <span id="messageboxtext"></span>
	                <input type="button" id="messageboxok" onclick="game.messageBoxOK();">
	                <input type="button" id="messageboxcancel" onclick="game.messageBoxCancel();">
	            </div>
	        </div>	        
	        <div id="loadingscreen" class="gamelayer">
	            <div id="loadingmessage"></div>
	        </div>
			<div id="multiplayerlobbyscreen" class="gamelayer">
			    <select class="form-control" id="multiplayergameslist" size="10">
			    </select>
			    <input type="button" id="multiplayerjoin" onclick="multiplayer.join();">
			    <input type="button" id="multiplayercancel" onclick="multiplayer.cancel();">
			</div>	
	    </div>
        <div style="text-align:center;">
            <p>copyright © Jason-Zhang@ZJU</p>
        </div>
	</body>
</html>