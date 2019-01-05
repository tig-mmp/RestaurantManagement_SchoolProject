/*jshint esversion: 6 */

var app = require('http').createServer();

// Se tiverem problemas com "same-origin policy" deverão activar o CORS.

// Aqui, temos um exemplo de código que ativa o CORS (alterar o url base) 

// var app = require('http').createServer(function(req,res){
// Set CORS headers
//  res.setHeader('Access-Control-Allow-Origin', 'http://---your-base-url---');
//  res.setHeader('Access-Control-Request-Method', '*');
//  res.setHeader('Access-Control-Allow-Methods', 'UPGRADE, OPTIONS, GET');
//  res.setHeader('Access-Control-Allow-Credentials', true);
//  res.setHeader('Access-Control-Allow-Headers', req.header.origin);
//  if ( req.method === 'OPTIONS' || req.method === 'UPGRADE' ) {
//      res.writeHead(200);
//      res.end();
//      return;
//  }
// });

// NOTA: A solução correta depende da configuração do próprio servidor, 
// e alguns casos do próprio browser.
// Assim sendo, não se garante que a solução anterior funcione.
// Caso não funcione é necessário procurar/investigar soluções alternativas

var io = require('socket.io')(app);

var LoggedUsers = require('./loggedusers.js');

app.listen(8080, function(){
    console.log('listening on *:8080');
});

// ------------------------
// Estrutura dados - server
// ------------------------

// loggedUsers = the list (map) of logged users. 
// Each list element has the information about the user and the socket id
// Check loggedusers.js file

let loggedUsers = new LoggedUsers();

io.on('connection', function (socket) {
    console.log('client has connected (socket ID = '+socket.id+')' );
    
	socket.on('user_enter', function (user) {
	if (user !== undefined && user !== null)
		console.log(user.name + ' joined ' + user.type);
		socket.join(user.type);
		loggedUsers.addUserInfo(user, socket.id);
	});
	socket.on('user_exit', function (user) {
		if (user !== undefined && user !== null)
			console.log(user.name + ' exit ' + user.type);
			socket.leave(user.type);
			loggedUsers.removeUserInfoByID(user.id);
	});

	socket.on('msg_from_worker_to_managers', function (msg, userInfo) {
		if (userInfo !== undefined) {
			let mensage = userInfo.name + ' (' + userInfo.type + '): "' + msg + '"';
			io.sockets.to('manager').emit('msg_from_server_managers', mensage, userInfo);
		}
	});

	socket.on('orderAddCook', function (order) {//order mudou o estado para confirmed
		io.sockets.to('cook').emit('cookNewOrder', order);
	});
	socket.on('orderRemoveWaiterPendingAddAllCook', function (orderId, destUserId) {
		let userInfo = loggedUsers.userInfoByID(destUserId);
		let socket_id = userInfo !== undefined ? userInfo.socketID : null;
		if (socket_id !== null) {
			io.to(socket_id).emit('waiterRemovePending', orderId);
		}
		io.sockets.to('cook').emit('cookRemoveOrder', orderId);
	});
	socket.on('orderAddWaiterPrepared', function (order, destUserId) {
		let userInfo = loggedUsers.userInfoByID(destUserId);
		let socket_id = userInfo !== undefined ? userInfo.socketID : null;
		if (socket_id !== null) {
			io.to(socket_id).emit('orderPrepared', order);
		}
	});
	socket.on('orderRemoveAllCook', function (orderId) {
		io.sockets.to('cook').emit('cookRemoveOrder', orderId);
	});
    socket.on('orderRemoveCook', function (orderId, destUserId) {
        let userInfo = loggedUsers.userInfoByID(destUserId);
        let socket_id = userInfo !== undefined ? userInfo.socketID : null;
        if (socket_id !== null) {
            io.to(socket_id).emit('cookRemoveOrder', orderId);
        }
    });

	socket.on('mealCreated', function (table) {//os waiters so precisam de receber a table
		io.sockets.to('waiter').emit('mealCreated', table);
		io.sockets.to('manager').emit('mealCreated');
	});

	socket.on('mealRemoved', function (table, mealId) {
		io.sockets.to('waiter').emit('mealRemoved', table);
		io.sockets.to('manager').emit('mealRemoved', mealId);
	});

	socket.on('setAsNotPaid', function () {
		io.sockets.to('manager').emit('setAsNotPaid');
	});

	socket.on('newInvoice', function (invoice) {
		io.sockets.to('cashier').emit('newInvoice', invoice);
	});

	socket.on('invoicePaid', function (invoice) {
		io.sockets.to('cashier').emit('newInvoice', invoice);
		io.sockets.to('manager').emit('invoicePaid');
	});

	socket.on('updateItems', function () {
		io.sockets.to('waiter').emit('updateItems');
	});
	socket.on('updateTables', function () {
		io.sockets.to('waiter').emit('updateTables');
	});
	socket.on('updateOrder', function (mealId) {
		io.sockets.to('waiter').emit('updateManagerOrders', mealId);
	});


});
