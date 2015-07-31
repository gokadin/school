var app = require('express')();
var http = require('http').Server(app);
var io = require('socket.io')(http);
var Redis = require('ioredis');
var redis = new Redis();

redis.subscribe('messaging.newMessageReceived', function(err, count) {

});

redis.on('message', function(channel, message) {
    console.log('Message received -> channel: ' + channel + ', message: ' + message);

    io.emit(channel, message);
});

http.listen(3000, function() {
    console.log('Listening on *:3000');
});