<?php

namespace App\Http\Sockets;

use Exception;
use Orchid\Socket\BaseSocketListener;
use Ratchet\ConnectionInterface;
use SplObjectStorage;

class GameSocket extends BaseSocketListener
{
    /**
     * Current clients.
     *
     * @var SplObjectStorage
     */
    protected $clients;
    protected $games;
    protected $games_clients;


    /**
     * GameSocket constructor.
     */
    public function __construct()
    {
        $this->clients = new SplObjectStorage();
        $this->games_clients = [];
        $this->games = [];
    }

    /**
     * @param ConnectionInterface $conn
     */
    public function onOpen(ConnectionInterface $conn)
    {
        $this->clients->attach($conn);
    }

    /**
     * @param ConnectionInterface $from
     * @param $msg
     */
    public function onMessage(ConnectionInterface $from, $msg)
    {
        $secret_token = null;
        $token = null;
        $data = json_decode($msg);

        $from_id = $data->id;

        switch($data->command) {
            case "find":
                foreach ($this->games as $game_token => $game) {
                    if (count($game['players']) < 2 && $game) {
                        $token = $game_token;
                        $game['players'][$from_id] = 0;
                        $this->games_clients[$token]->attach($from);
                        $result['creator'] = $this->games[$game_token]['creator'];

                        echo "[DEBUG] Joined the lobby $token\n";

                        $result['message'] = $data->message;
                        $result['token'] = $token;
                        $result['player_id'] = $from_id;

                        foreach ($this->games_clients[$token] as $client) {
                            $client->send(json_encode($result));
                        }

                        break;
                    }
                }

                if (!$token) {
                    $token = md5(uniqid(bin2hex(random_bytes(200)), true));
                    $secret_token = md5(uniqid(bin2hex(random_bytes(200)), true));

                    $this->games[$token]["players"][$from_id] = 0;
                    $this->games[$token]["secret_token"] = $secret_token;
                    $this->games[$token]["creator"] = $from_id;
                    $this->games[$token]["players_online"] = 0;
                    $this->games_clients[$token] = new SplObjectStorage();
                    $this->games_clients[$token]->attach($from);

                    echo "[DEBUG] Created lobby $token\n";
                }

                break;
            case "connect":
                $token = $data->token;

                if ($this->games[$token]['players_online'] < 2) {
                    $this->games[$token]['players_online']++;
                    $players = $this->games[$token]['players'];

                    foreach ($players as $id => $isConnected) {
                        if ($id != $from_id) {
                            $recipient_id = $id;
                            $this->games_clients[$recipient_id]->send();
                        }
                    }

                    echo "[DEBUG] Joined the game $token\n";

                    break;
                }
                break;
            default:

                foreach ($this->games_clients[$data->token] as $client) {
                    if ($from !== $client) {
                        $client->send($data);
                    }

                    echo "[DEBUG] Sent $token\n";
                }
        }
    }

    /**
     * @param ConnectionInterface $conn
     */
    public function onClose(ConnectionInterface $conn)
    {
        $this->clients->detach($conn);
        foreach ($this->games_clients as $token => $games_clients) {
            foreach ($games_clients as $player) {
                if ($player == $conn) {
                    foreach ($games_clients as $dump_player) {
                        if ($dump_player !== $conn) {
                            $dump_player->send("{
                                \"command\": \"end\",
                                \"token\": \"$token\",
                                \"secret_token\": " . bcrypt($this->games[$token]["secret_token"]) . "
                            }");
                        }
                    }

                    echo "[DEBUG] Left the game $token";

                    return;
                }
            }
        }
        // for game_clients tokens where player == $conn
    }

    /**
     * @param ConnectionInterface $conn
     * @param Exception          $e
     */
    public function onError(ConnectionInterface $conn, Exception $e)
    {
        echo "An error has occurred: {$e->getMessage()}\n";

        $conn->close();
    }
}
