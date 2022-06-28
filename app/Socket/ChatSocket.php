<?php

namespace App\Socket;

use App\Socket\Base\BaseSocket;
use Ratchet\ConnectionInterface;
use function MongoDB\BSON\fromJSON;

class ChatSocket extends BaseSocket
{
    protected $clients;

    protected $chats;

    public function __construct()
    {
        $this->clients = new \SplObjectStorage;
        $this->chats = [];
    }

    public function onOpen(ConnectionInterface $conn)
    {
        $this->clients->attach($conn);

        echo "New connection ! ({$conn->resourceId})\n";

    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        $data = json_decode($msg);

        switch($data->command) {
            case "subscribe":
                $this->chats[$data->chat] ?? $this->chats[$data->chat] = $from;
                break;
            case "send":
                $recipient = $this->chats[$data->chat];
                if ($from !== $recipient) {
                    $recipient->send($data->message);
                }
                break;
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        $this->clients->detach($conn);

        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "An error has occurred: {$e->getMessage()}\n";

        $conn->close();
    }
}
