<?php

namespace Chat;

require 'vendor/autoload.php';

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class MyChat implements MessageComponentInterface
{
    protected $clients;

    public function __construct()
    {
        $this->clients = new \SplObjectStorage();
    }

    public function onOpen(ConnectionInterface $conn)
    {
        $this->clients->attach($conn);
        echo "Server_side: New connection! ({$conn->resourceId})\n"; // вывод сообщения на сервере при подключении нового клиента
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        echo "Поступило сообщение от клиента {$from->resourceId}: {$msg}\n"; // вывод сообщения на сервере при получении сообщения от клиента
        foreach ($this->clients as $client) {
            if ($from != $client) {
                $server_msg = "Server_side: {$from->resourceId} says {$msg}\n"; // замена сообщения (от клиента) на сервере
                $client->send($server_msg); // отправка клиенту (всем клиентам в цикле) обновленного сообщения
            }
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        $this->clients->detach($conn);
        echo "Server_side: клиент {$conn->resourceId} закрыл соединение\n"; // вывод сообщения на сервере при отключении (какого-либо) клиента
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        $conn->close();
    }
}
