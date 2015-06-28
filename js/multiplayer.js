var battle_period,start_time,loser,opponent;
var map_id;

var multiplayer = {
    // Open multiplayer game lobby
    //username:$('#username')[0].value,
    websocket_url:"ws://218.244.137.223:8081/",
    websocket:undefined,

start:function(){
		$('.gamelayer').hide();
		game.type = "multiplayer";
		var WebSocketObject = window.WebSocket || window.MozWebSocket;
		if (!WebSocketObject){
			game.showMessageBox("Your browser does not support WebSocket. Multiplayer will not work.");
			return;
		}
		this.websocket = new WebSocketObject(this.websocket_url);
		this.websocket.onmessage = multiplayer.handleMsgfromServer;
		this.websocket.onopen = function(){
			$('#roomscreen').show();	 //显示房间背景图片
		}
	
		this.websocket.onclose = function(){			
			multiplayer.endGame("Error connecting to server.");
		}
	
		this.websocket.onerror = function(){			
			multiplayer.endGame("Error connecting to server.");
		}
	},
    handleMsgfromServer:function(message){
        var messageObject = JSON.parse(message.data);
        switch (messageObject.type){
            case "init_level":
                multiplayer.initMultiplayerLevel(messageObject);
                break;
            case "room_list":
                multiplayer.updateRoomStatus(messageObject.status);
                break;    
	        case "joined_room":
	            multiplayer.roomId = messageObject.roomId;
	            multiplayer.color = messageObject.color;
	            break;
	        case "start_game":
	            multiplayer.startGame();
	            break;
	        case "latency_ping":
	            multiplayer.sendWebSocketMessage({type:"latency_pong"});
	            break;    
	        case "game_tick":
	            multiplayer.lastReceivedTick = messageObject.tick;
	            multiplayer.commands[messageObject.tick] = messageObject.commands;
	            break;  
	        case "end_game":
                var username = $('#username')[0].value;
                //battle_period = Math.floor(messageObject.last_time/1000);
                //start_time = Math.floor(messageObject.start_time/1000);
                battle_period = messageObject.last_time;
                start_time = messageObject.start_time;
                var msg;
                loser = messageObject.loser;
                opponent = messageObject.opponent;
                if( loser == username)
                    msg = "Commander "+username+"\n:You lose this battle!";
                else
                    msg = "Commander "+username+"\n:You win this battle!";
                msg = msg + "\nThis battle lasts "+ (Math.floor(battle_period/60)) +  ' minutes and ' + (battle_period-Math.floor(battle_period/60)*60) + " seconds";
                multiplayer.endGame(msg);

	            break;
            case "disconnect":
                multiplayer.endGame(messageObject.reason);
                break;
            case "chat":
				game.showMessage(messageObject.from,messageObject.message);
				break;        
        }        
    },
    statusMessages:{
        'starting':'游戏开始',
        'running':'游戏中',
        'waiting':'等待第二个玩家进入房间',
        'empty':'房间开放'
    },
    updateRoomStatus:function(status){
        var $list = $("#multiplayergameslist");
        $list.empty(); // remove old options

        //依次列出10个房间
        for (var i=0; i < status.length; i++) {
            var key = "Game "+(i+1)+". "+this.statusMessages[status[i]];            
            $list.append($("<option></option>").attr("disabled",status[i]== "running"||status[i]== "starting").attr("value", (i+1)).text(key).addClass(status[i]).attr("selected", (i+1)== multiplayer.roomId));
        };    
    },
	join:function(){
	    var selectedRoom = document.getElementById('multiplayergameslist').value;
	    if(selectedRoom){            
	    	//将用户名传递给websocket
	        //multiplayer.sendWebSocketMessage({type:"join_room",roomId:selectedRoom});  
            var username = $('#username')[0].value;
	        multiplayer.sendWebSocketMessage({type:"join_room",roomId:selectedRoom,player_name:username})
	        document.getElementById('multiplayergameslist').disabled = true;
	        document.getElementById('multiplayerjoin').disabled = true;        
	    } else {
	    	//$('#gamestartscreen').hide();
	    	//$('.gamelayer').hide();
	    	//$('#gameinterfacescreen').hide();
	        game.showMessageBox("Please select a game room to join.");            
	    }
	}, 
	cancel:function(){
		// Leave any existing game room
		if(multiplayer.roomId){			
			multiplayer.sendWebSocketMessage({type:"leave_room",roomId:multiplayer.roomId});
			document.getElementById('multiplayergameslist').disabled = false;
			document.getElementById('multiplayerjoin').disabled = false;
			delete multiplayer.roomId;
			delete multiplayer.color;
			return;
		} else {
			// Not in a room, so leave the multiplayer screen itself
			multiplayer.closeAndExit();
		}
	},
	closeAndExit:function(){
		// clear handlers and close connection
		multiplayer.websocket.onopen = null;
		multiplayer.websocket.onclose = null;
		multiplayer.websocket.onerror = null;		
		multiplayer.websocket.close();
	
		document.getElementById('multiplayergameslist').disabled = false;
		document.getElementById('multiplayerjoin').disabled = false;		
		// Show the starting menu layer
		$('.gamelayer').hide();
	    $('#gamestartscreen').show();
        $('#money_collected').html(0);
        $('#destroyed_num').html(0);
        $('#gamemessages').html("");
        //if(typeof(loser)!="undefined")
        //    window.location.href = "battle.php?loser="+loser+"&opponent="+opponent+"&begin="+start_time+"&last="+battle_period;
	},
	sendWebSocketMessage:function(messageObject){
	    this.websocket.send(JSON.stringify(messageObject));
	},
	currentLevel:0,
	initMultiplayerLevel:function(messageObject){

	    $('.gamelayer').hide();        
	    var spawnLocations = messageObject.spawnLocations;

	    // Initialize multiplayer related variables
	    multiplayer.commands = [[]];    
	    multiplayer.lastReceivedTick = 0;
	    multiplayer.currentTick = 0;    

	    game.team = multiplayer.color;

	    // Load all the items for the level
	    multiplayer.currentLevel = messageObject.level;
	    var level = maps.multiplayer[multiplayer.currentLevel];

	    // Load all the assets for the level
        map_id = messageObject.map_id;
        console.log(level.mapImage[map_id]);
	    game.currentMapImage = loader.loadImage(level.mapImage[map_id]);

	    game.currentLevel = level;

	    // Setup offset based on spawn location sent by server


	    // Load level Requirements 
	    game.resetArrays();
	    for (var type in level.requirements){
	           var requirementArray = level.requirements[type];
	           for (var i=0; i < requirementArray.length; i++) {
	               var name = requirementArray[i];
	               if (window[type]){
	                   window[type].load(name);
	               } else {
	                   console.log('Could not load type :',type);
	               }
	           };
	     }

	    for (var i = level.items.length - 1; i >= 0; i--){
	        var itemDetails = level.items[i];
	        game.add(itemDetails);
	    };        

	    // Add starting items for both teams at their respective spawn locations
	    for (team in spawnLocations){
	        var spawnIndex = spawnLocations[team];
	        for (var i=0; i < level.teamStartingItems.length; i++) {
	            var itemDetails = $.extend(true,{},level.teamStartingItems[i]);
	            itemDetails.x += level.spawnLocations[spawnIndex].x+itemDetails.x;
	            itemDetails.y += level.spawnLocations[spawnIndex].y+itemDetails.y;
	            itemDetails.team = team;
	            game.add(itemDetails);            
	        };

	        if (team==game.team){                
	            game.offsetX = level.spawnLocations[spawnIndex].startX*game.gridSize;
	            game.offsetY = level.spawnLocations[spawnIndex].startY*game.gridSize;
	        }
	    }


	    // Create a grid that stores all obstructed tiles as 1 and unobstructed as 0
	    game.currentMapTerrainGrid = [];
	    for (var y=0; y < level.mapGridHeight; y++) {
	        game.currentMapTerrainGrid[y] = [];
	           for (var x=0; x< level.mapGridWidth; x++) {
	            game.currentMapTerrainGrid[y][x] = 0;
	        }
	    };
        var mapObstructedTerrain;
        switch (map_id){
            case 0:
                mapObstructedTerrain = level.mapObstructedTerrain0;
                break;
            case 1:
                mapObstructedTerrain = level.mapObstructedTerrain1;
                break;
            case 2:
                mapObstructedTerrain = level.mapObstructedTerrain2;
                break;
            case 3:
                mapObstructedTerrain = level.mapObstructedTerrain3;
                break;
        }
	    for (var i = mapObstructedTerrain.length - 1; i >= 0; i--){
	        var obstruction = mapObstructedTerrain[i];
	        game.currentMapTerrainGrid[obstruction[1]][obstruction[0]] = 1;
	    };
	    game.currentMapPassableGrid = undefined;

	    // Load Starting Cash For Game
	    game.cash = $.extend([],level.cash);
        game.money_collected = $.extend([],level.money_collected);
        game.destroyed_num = $.extend([],level.destroyed_num);
	    // Enable the enter mission button once all assets are loaded
	    if (loader.loaded){
	        multiplayer.sendWebSocketMessage({type:"initialized_level"});

	    } else {
	        loader.onload = function(){
	            multiplayer.sendWebSocketMessage({type:"initialized_level"});
	        }
	    }
	},
	startGame:function(){
	    fog.initLevel();
	    game.animationLoop();                                
	    multiplayer.animationInterval = setInterval(multiplayer.tickLoop, game.animationTimeout);
	    game.start();                
	},
	sendCommand:function(uids,details){
	    multiplayer.sentCommandForTick = true;
	    multiplayer.sendWebSocketMessage({type:"command",uids:uids, details:details,currentTick:multiplayer.currentTick});
	},
	tickLoop:function(){        
	    // if the commands for that tick have been received
	    // execute the commands and move on to the next tick
	    // otherwise wait for server to catch up        
	    if(multiplayer.currentTick <= multiplayer.lastReceivedTick){
	        var commands = multiplayer.commands[multiplayer.currentTick];
	        if(commands){
	            for (var i=0; i < commands.length; i++) {                
	                game.processCommand(commands[i].uids,commands[i].details);
	            };
	        }

	        game.animationLoop();

	        // In case no command was sent for this current tick, send an empty command to the server
	        // So that the server knows that everything is working smoothly
	        if (!multiplayer.sentCommandForTick){
	            multiplayer.sendCommand();
	        }
	        multiplayer.currentTick++;
	        multiplayer.sentCommandForTick = false;
	    }
	},    
	//游戏失败
	loseGame:function(){
		
	    multiplayer.sendWebSocketMessage({type:"lose_game"});
	},

    updateMoneyCollect:function(){
      multiplayer.sendWebSocketMessage({type:"updateMoneyCollect",roomId:multiplayer.roomId});
    },

    updateDestroyed:function(){
      multiplayer.sendWebSocketMessage(({type:"updateDestroyed",roomId:multiplayer.roomId}));
    },

	endGame:function(reason){
		
	    game.running = false;
	    clearInterval(multiplayer.animationInterval);
	    game.showMessageBox(reason,multiplayer.closeAndExit);
	}
};

$(window).keydown(function(e){
    // Chatting only allowed in multiplayer when game is running
    if(game.type != "multiplayer" || !game.running){
        return;
    }

    if (e.which == 13){ // Enter key pressed    
        var isVisible = $('#chatmessage').is(':visible');    
        if (isVisible){
            // if chat box is visible, pressing enter sends the message and hides the chat box
            if ($('#chatmessage').val()!= ''){
                multiplayer.sendWebSocketMessage({type:"chat",message:$('#chatmessage').val()});
                $('#chatmessage').val('');
            }
            $('#chatmessage').hide();
        } else {
            // if chat box is not visible, pressing enter shows the chat box
            $('#chatmessage').show();
            $('#chatmessage').focus();    
        }
        e.preventDefault();
    } else if (e.which==27){ // Escape key pressed
        $('#chatmessage').hide();
        $('#chatmessage').val('');        
        e.preventDefault();
    }
});

