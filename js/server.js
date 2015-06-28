var WebSocketServer = require('websocket').server;
var mysql  = require('mysql');  //调用MySQL模块
var mysql_conn = mysql.createConnection({
    host     : 'localhost',       //主机
    user     : 'root',               //MySQL认证用户名
    password  : '19940812zc',
    port: '3306',                   //端口号
    database: 'rts'
});
var http = require('http');

var start_time;
var end_time;   //游戏开始与结束时间

// Create a simple web server that returns the same response for any request
var server = http.createServer(function(request,response){
    response.writeHead(200, {'Content-Type': 'text/plain'});
    response.end("This is the node.js HTTP server.");
});

server.listen(8081,function(){
    console.log('Server has started listening on port 8081');
});

var wsServer = new WebSocketServer({
    httpServer:server,
    autoAcceptConnections: false        
});


function connectionIsAllowed(request){
    return true;
}

// 初始化房间
var gameRooms = [];

var players = [];  //每个房间允许两个玩家进入

for (var i=0; i < 10; i++) {
    gameRooms.push({status:"empty",players:[],roomId:i+1});
};


wsServer.on('request',function(request){
    if(!connectionIsAllowed(request)){
        request.reject();
        console.log('Connection from ' + request.remoteAddress + ' rejected.');
        return;
    }
    
    var connection = request.accept();
    console.log('Connection from ' + request.remoteAddress + ' accepted.');
    
    // Add the player to the players array
    var player = {
        connection:connection,
        latencyTrips:[]    
    };

    players.push(player);   //将该玩家加入到房间中
    
    // Measure latency for player
    measureLatency(player);

    // Send a fresh game room status list the first time player connects
    sendRoomList(connection);

	// On Message event handler for a connection
	connection.on('message', function(message) {
	    if (message.type === 'utf8') {
	        var clientMessage = JSON.parse(message.utf8Data);
	        switch (clientMessage.type){
	            case "join_room":
	                var room = joinRoom(player,clientMessage.roomId,clientMessage.player_name);
					//player.player_name = clientMessage.player_name;
	                sendRoomListToEveryone();
	                if(room.players.length == 2){
	                    initGame(room);
	                }
	                break;                
	            case "leave_room":
	                leaveRoom(player,clientMessage.roomId);
	                sendRoomListToEveryone();
	                break;
	            case "initialized_level":
	                player.room.playersReady++;
	                if (player.room.playersReady==2){
	                    startGame(player.room);
	                }
	                break;
                case "updateMoneyCollect":
                    //player.money_collected += 5;
                    var room_id = clientMessage.roomId;
                    if(gameRooms[room_id-1].players[0].player_name==player.player_name){
                        gameRooms[room_id-1].players[0].money_collected+=5;
                    }
                    else{
                        gameRooms[room_id-1].players[1].money_collected+=5;
                    }
                    console.log('updateMoneyCollect ' + player.player_name);
                    break;
                case "updateDestroyed":
                    //player.destroyed_num += 1;
                    room_id = clientMessage.roomId;
                    if(gameRooms[room_id-1].players[0].player_name==player.player_name){
                        gameRooms[room_id-1].players[0].destroyed_num+=1;
                    }
                    else{
                        gameRooms[room_id-1].players[1].destroyed_num+=1;
                    }
                    console.log('updateDestroyed ' + player.player_name);
                    break;
                case "latency_pong":
                    finishMeasuringLatency(player,clientMessage);
                    // Measure latency at least thrice
                    if(player.latencyTrips.length<3){
                        measureLatency(player);
                    }
                    break;    
	            case "command":
	                if (player.room && player.room.status=="running"){
	                    if(clientMessage.uids){
	                        player.room.commands.push({uids:clientMessage.uids, details:clientMessage.details});
	                    }                    
	                    player.room.lastTickConfirmed[player.color] = clientMessage.currentTick + player.tickLag;
	                }
	                break;    
				case "lose_game":
					endGame(player.room, player.player_name,player.opponent);
					break;
				case "chat":
					if (player.room && player.room.status=="running"){
						var cleanedMessage = clientMessage.message.replace(/[<>]/g,"");
                        //var username =  '<%=Session["username"] %>';
					   	sendRoomWebSocketMessage(player.room,{type:"chat", from:player.player_name, message:cleanedMessage});
						console.log(clientMessage.message,"was cleaned to",cleanedMessage)
					}
					break;                                                                                                                                                                             
	        }
	    }
	});

    connection.on('close', function(reasonCode, description) {
	    console.log('Connection from ' + request.remoteAddress + ' disconnected.');

	    for (var i = players.length - 1; i >= 0; i--){
	        if (players[i]==player){
	            players.splice(i,1);
	        }
	    };

	    // If the player is in a room, remove him from room and notify everyone
	    if(player.room){
	        var status = player.room.status;
	        var roomId = player.room.roomId;
	        // If the game was running, end the game as well            
	        if(status=="running"){
                disconnectGame(player.room, "Commander "+ player.player_name +" has disconnected.");
	        } else {
	            leaveRoom(player,roomId);
	        }            
	        sendRoomListToEveryone();            
	    }

	});
});

function sendRoomList(connection){
    var status = [];
    for (var i=0; i < gameRooms.length; i++) {
        status.push(gameRooms[i].status);
    };
    var clientMessage = {type:"room_list",status:status};
    connection.send(JSON.stringify(clientMessage));
}

function sendRoomListToEveryone(){
    // Notify all connected players of the room status changes
    var status = [];
    for (var i=0; i < gameRooms.length; i++) {
        status.push(gameRooms[i].status);
    };
    var clientMessage = {type:"room_list",status:status};
    var clientMessageString = JSON.stringify(clientMessage);
    for (var i=0; i < players.length; i++) {
        players[i].connection.send(clientMessageString);
    };
}

function joinRoom(player,roomId,player_name){
    var room = gameRooms[roomId-1];
    console.log("Adding player to room",roomId);
    // Add the player to the room
    room.players.push(player);
    player.room = room;
    player.player_name = player_name;
    // Update room status 
    if(room.players.length == 1){
        room.status = "waiting";
        player.color = "blue";
    } else if (room.players.length == 2){
        room.status = "starting";
        player.color = "green";
        room.players[0].opponent = room.players[1].player_name;
        room.players[1].opponent = room.players[0].player_name;
        room.players[0].money_collected = room.players[1].money_collected=0;
        room.players[0].destroyed_num = room.players[1].destroyed_num=0;
        //标记对手名字
    }
    // Confirm to player that he was added
    var confirmationMessageString = JSON.stringify({type:"joined_room", roomId:roomId, color:player.color});
    player.connection.send(confirmationMessageString);
    return room;
}

function leaveRoom(player,roomId){
    var room = gameRooms[roomId-1];
    console.log("Removing player from room",roomId);
     
    for (var i = room.players.length - 1; i >= 0; i--){
        if(room.players[i]==player){
            room.players.splice(i,1);
        }
    };
    delete player.room;
    // Update room status 
    if(room.players.length == 0){
        room.status = "empty";    
    } else if (room.players.length == 1){
        room.status = "waiting";
    }
}

function initGame(room){
    console.log("Both players Joined. Initializing game for Room "+room.roomId);

    // Number of players who have loaded the level
    room.playersReady = 0;
    
    // Load the first multiplayer level for both players 
    // This logic can change later to let the players pick a level
    var currentLevel = 0;
    
    // Randomly select two spawn locations between 0 and 3 for both players. 
    var spawns = [0,1,2];
    var spawnLocations = {"blue":spawns.splice(Math.floor(Math.random()*spawns.length),1), "green":spawns.splice(Math.floor(Math.random()*spawns.length),1)};
    var map_id = Math.floor(Math.random()*1);
    sendRoomWebSocketMessage(room,{type:"init_level", spawnLocations:spawnLocations, level:currentLevel,map_id:map_id});
}

function startGame(room){
    console.log("Both players are ready. Starting game in room",room.roomId);
    room.status = "running";
    sendRoomListToEveryone();
    // Notify players to start the game
    sendRoomWebSocketMessage(room,{type:"start_game"});
    start_time = Date.now();//游戏开始时间

    room.commands = [];    
    room.lastTickConfirmed = {"blue":0,"green":0};
    room.currentTick = 0;
    
    // Calculate tick lag for room as the max of both player's tick lags
    var roomTickLag = Math.max(room.players[0].tickLag,room.players[1].tickLag);
        
    room.interval = setInterval(function(){
        // Confirm that both players have send in commands for upto present tick
        if(room.lastTickConfirmed["blue"] >= room.currentTick && room.lastTickConfirmed["green"] >= room.currentTick){        
            // Commands should be executed after the tick lag
            sendRoomWebSocketMessage(room,{type:"game_tick", tick:room.currentTick+roomTickLag, commands:room.commands});                            
            room.currentTick++;
            room.commands = [];
        } else {
            // One of the players is causing the game to lag. Handle appropriately
            if(room.lastTickConfirmed["blue"] < room.currentTick){
                console.log ("Room",room.roomId,"Blue is lagging on Tick:",room.currentTick,"by", room.currentTick-room.lastTickConfirmed["blue"]);
            }
            if(room.lastTickConfirmed["green"] < room.currentTick){
                console.log ("Room",room.roomId,"Green is lagging on Tick:", room.currentTick, "by", room.currentTick-room.lastTickConfirmed["green"]);
            }        
        }
    },100);
}

function sendRoomWebSocketMessage(room,messageObject){
    var messageString = JSON.stringify(messageObject);
    for (var i = room.players.length - 1; i >= 0; i--){
        room.players[i].connection.send(messageString);
    }; 
}

function measureLatency(player){
    var connection = player.connection;    
    var measurement = {start:Date.now()};
    player.latencyTrips.push(measurement);
    var clientMessage = {type:"latency_ping"};
    connection.send(JSON.stringify(clientMessage));
}

function finishMeasuringLatency(player,clientMessage){
    var measurement = player.latencyTrips[player.latencyTrips.length-1];
    measurement.end = Date.now();
    measurement.roundTrip = measurement.end - measurement.start;
    player.averageLatency = 0;
    for (var i=0; i < player.latencyTrips.length; i++) {
        player.averageLatency += measurement.roundTrip/2;
    };
    player.averageLatency = player.averageLatency/player.latencyTrips.length;
    player.tickLag = Math.round(player.averageLatency * 2/100)+1;     
    console.log("Measuring Latency for player. Attempt", player.latencyTrips.length, "- Average Latency:",player.averageLatency, "Tick Lag:", player.tickLag);
}
function endGame(room,loser,opponent){
    clearInterval(room.interval);
    room.status = "empty";
    end_time = Date.now();
    var battle_period = end_time-start_time;
    var winner_name;
    if(room.players[0].player_name==loser){
        winner_name = room.players[1].player_name;
    }
    else{
        winner_name = room.players[0].player_name;
    }
    mysql_conn.connect(function(err){
        if(err){
            console.log('[query] - :'+err);
            return;
        }
        console.log('[connection connect]  succeed!');
    });
    start_time = Math.floor(start_time/1000);
    battle_period = Math.floor(battle_period/1000);
    var sql = "insert into battle(begin_time,last_time,user1_name,user2_name,user1_money,user2_money,user1_destroy,user2_destroy,winner_name) VALUES (?,?,?,?,?,?,?,?,?)";
    var para = [
        start_time,
        battle_period,
        room.players[0].player_name,
        room.players[1].player_name,
        room.players[0].money_collected,
        room.players[1].money_collected,
        room.players[1].destroyed_num,
        room.players[0].destroyed_num,
        winner_name];
    mysql_conn.query(sql,para,function (err, result) {
        if(err){
            console.log('[INSERT ERROR] - ',err.message);
            return;
        }
        console.log('--------------------------INSERT----------------------------');
        //console.log('INSERT ID:',result.insertId);
        console.log('INSERT ID:',result);
        console.log('-----------------------------------------------------------------');
    });

    //update loser's rank
    sql = "update rank set play_count = play_count + 1,win_rate = win_count/play_count where u_name = ?";
    para = [loser];
    mysql_conn.query(sql,para,function (err, result) {
        if(err){
            console.log('[INSERT ERROR] - ',err.message);
            return;
        }
        console.log('--------------------------UPDATE----------------------------');
        //console.log('INSERT ID:',result.insertId);
        console.log('UPDATE ID:',result);
        console.log('-----------------------------------------------------------------');
    });

    //update winner's rank
    sql = "update rank set play_count = play_count + 1,win_count=win_count+1,win_rate = win_count/play_count where u_name = ?";
    para = [winner_name];
    mysql_conn.query(sql,para,function (err, result) {
        if(err){
            console.log('[INSERT ERROR] - ',err.message);
            return;
        }
        console.log('--------------------------UPDATE----------------------------');
        //console.log('INSERT ID:',result.insertId);
        console.log('UPDATE ID:',result);
        console.log('-----------------------------------------------------------------');
    });

    sendRoomWebSocketMessage(room,{type:"end_game",loser:loser,opponent:opponent,last_time:battle_period,start_time:start_time});
    for (var i = room.players.length - 1; i >= 0; i--){
        leaveRoom(room.players[i],room.roomId);        
    };     
    sendRoomListToEveryone();
}

function disconnectGame(room,reason){
    clearInterval(room.interval);
    room.status = "empty";
    sendRoomWebSocketMessage(room,{type:"disconnect",reason:reason});
    for (var i = room.players.length - 1; i >= 0; i--){
        leaveRoom(room.players[i],room.roomId);
    };
    sendRoomListToEveryone();
}

