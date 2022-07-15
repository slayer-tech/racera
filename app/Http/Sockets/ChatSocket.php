<?php

namespace App\Http\Sockets;

use Orchid\Socket\BaseSocketListener;
use Ratchet\ConnectionInterface;
use function MongoDB\BSON\fromJSON;

class ChatSocket extends BaseSocketListener
{
    /**
     * Current clients.
     *
     * @var \SplObjectStorage
     */
    protected $clients;

    /**
     * Current chats.
     *
     * @var array
     */
    protected $chats;

    /**
     * ChatSocket constructor.
     */
    public function __construct()
    {
        $this->clients = new \SplObjectStorage;
        $this->chats = [];
    }

    /**
     * @param ConnectionInterface $conn
     */
    public function onOpen(ConnectionInterface $conn)
    {
        $this->clients->attach($conn);

        echo "New connection ! ({$conn->resourceId})\n";

    }

    /**
     * @param ConnectionInterface $from
     * @param $msg
     */
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

    /**
     * @param ConnectionInterface $conn
     */
    public function onClose(ConnectionInterface $conn)
    {
        $this->clients->detach($conn);

        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    /**
     * @param ConnectionInterface $conn
     * @param \Exception          $e
     */
    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "An error has occurred: {$e->getMessage()}\n";

        $conn->close();
    }
}
