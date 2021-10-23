/*jshint esversion: 6 */

require("dotenv").config({ path: __dirname + "./../.env" });

var app = require("http").createServer();
const io = require("socket.io")(app, {
    allowEIO3: true,
    cors: {
        origin: process.env.CURRENT_URL,
        methods: ["GET", "POST"],
        credentials: true
    }
});

var LoggedUsers = require("./loggedusers.js");

app.listen(8080, function() {
    console.log("listening on *:8080");
});

// ------------------------
// Estrutura dados - server
// ------------------------

// loggedUsers = the list (map) of logged users.
// Each list element has the information about the user and the socket id
// Check loggedusers.js file

let loggedUsers = new LoggedUsers();

io.on("connection", function(socket) {
    console.log("client has connected (socket ID = " + socket.id + ")");

    socket.on("user_enter", function(user) {
        if (user !== undefined && user !== null)
            console.log(user.name + " joined " + user.type);
        socket.join(user.type);
        loggedUsers.addUserInfo(user, socket.id);
    });
    socket.on("user_exit", function(user) {
        if (user !== undefined && user !== null)
            console.log(user.name + " exit " + user.type);
        socket.leave(user.type);
        loggedUsers.removeUserInfoByID(user.id);
    });

    socket.on("msg_from_worker_to_managers", function(msg, userInfo) {
        if (userInfo !== undefined) {
            let mensage =
                userInfo.name + " (" + userInfo.type + '): "' + msg + '"';
            io.sockets
                .to("manager")
                .emit("msg_from_server_managers", mensage, userInfo);
        }
    });

    socket.on("orderAddCook", function(order) {
        //order mudou o estado para confirmed
        io.sockets.to("cook").emit("cookNewOrder", order);
    });
    socket.on("orderRemoveWaiterPendingAddAllCook", function(
        orderId,
        destUserId
    ) {
        let userInfo = loggedUsers.userInfoByID(destUserId);
        let socket_id = userInfo !== undefined ? userInfo.socketID : null;
        if (socket_id !== null) {
            io.to(socket_id).emit("waiterRemovePending", orderId);
        }
        io.sockets.to("cook").emit("cookRemoveOrder", orderId);
    });
    socket.on("orderAddWaiterPrepared", function(order, destUserId) {
        let userInfo = loggedUsers.userInfoByID(destUserId);
        let socket_id = userInfo !== undefined ? userInfo.socketID : null;
        if (socket_id !== null) {
            io.to(socket_id).emit("orderPrepared", order);
        }
    });
    socket.on("orderRemoveAllCook", function(orderId) {
        io.sockets.to("cook").emit("cookRemoveOrder", orderId);
    });
    socket.on("orderRemoveCook", function(orderId, destUserId) {
        let userInfo = loggedUsers.userInfoByID(destUserId);
        let socket_id = userInfo !== undefined ? userInfo.socketID : null;
        if (socket_id !== null) {
            io.to(socket_id).emit("cookRemoveOrder", orderId);
        }
    });
    socket.on("orderAddWaiterPreparedDireto", function(order, destUserId) {
        let userInfo = loggedUsers.userInfoByID(destUserId);
        let socket_id = userInfo !== undefined ? userInfo.socketID : null;
        if (socket_id !== null) {
            io.to(socket_id).emit("waiterRemovePendingWithoutToast", order.id);
            io.to(socket_id).emit("orderPrepared", order);
        }
        io.sockets.to("cook").emit("cookRemoveOrder", order.id);
    });

    socket.on("mealCreated", function(table) {
        //os waiters so precisam de receber a table
        io.sockets.to("waiter").emit("mealCreated", table);
        io.sockets.to("manager").emit("mealCreated");
    });

    socket.on("mealRemoved", function(table, mealId) {
        io.sockets.to("waiter").emit("mealRemoved", table);
        io.sockets.to("manager").emit("managerMealRemoved", mealId);
    });

    socket.on("setAsNotPaid", function(waiterId) {
        io.sockets.to("manager").emit("setAsNotPaid");
        io.sockets.to("cashier").emit("updateInvoicesNotPaid");
        io.sockets.to("cook").emit("mealTerminated");
        let userInfo = loggedUsers.userInfoByID(waiterId);
        let socket_id = userInfo !== undefined ? userInfo.socketID : null;
        if (socket_id !== null) {
            io.to(socket_id).emit("waiterUpdateOrders");
        }
    });

    socket.on("invoicePaid", function() {
        io.sockets.to("manager").emit("setAsPaid");
        io.sockets.to("cashier").emit("invoicePaid");
    });

    socket.on("newInvoice", function() {
        io.sockets.to("cashier").emit("newInvoice");
        io.sockets.to("manager").emit("managerNewInvoice");
    });

    socket.on("updateItems", function() {
        io.sockets.to("waiter").emit("updateItems");
    });
    socket.on("updateTables", function() {
        io.sockets.to("waiter").emit("updateTables");
    });

    socket.on("updateOrder", function(mealId) {
        io.sockets.to("manager").emit("updateManagerOrders", mealId);
    });
});