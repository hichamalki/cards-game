<?php

namespace App\Service;

use App\Entity\Game;

class SerializerService
{

    const GAMES_DIR = 'games/';

    public function save(Game $game): Game
    {
        file_put_contents(self::GAMES_DIR.$game->getUid(), serialize($game));
        return $game;
    }

    public function fetch(String $uid): Game
    {
        $game = unserialize(@file_get_contents(self::GAMES_DIR.$uid));
        if(!$game) {
            throw new \Exception("Game not found!");
        }
        return $game;
    }

    

}