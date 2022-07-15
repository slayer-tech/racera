<?php

namespace App\Console\Commands;

use App\Http\Sockets\GameSocket;
use Illuminate\Console\Command;
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;

class GameServer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'game_server:serve';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->info('Start server');

        $server = IoServer::factory(
            new HttpServer(
                new WsServer(
                    new GameSocket()
                )
            ),
            8080
        );

        $server->run();
    }
}
