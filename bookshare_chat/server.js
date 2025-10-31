const express = require('express');
const app = express();
const http = require('http').createServer(app);
const io = require('socket.io')(http, {
    cors: {
        origin: "http://localhost",
        methods: ["GET", "POST"]
    }
});

io.on('connection', (socket) => {
    console.log('Un utilisateur connecté');

    socket.on('join_room', (bookId) => {
        socket.join(`book_${bookId}`);
    });

    socket.on('chat_message', (data) => {
        io.to(`book_${data.book_id}`).emit('message', {
            sender_id: data.sender_id,
            message: data.message,
            created_at: new Date()
        });
    });

    socket.on('disconnect', () => {
        console.log('Utilisateur déconnecté');
    });
});

const PORT = process.env.PORT || 3000;
http.listen(PORT, () => {
    console.log(`Serveur en écoute sur le port ${PORT}`);
});