<?php

namespace App\Service;

use App\Entity\Game;
use App\Entity\Card;

use Symfony\Component\HttpFoundation\RequestStack;
use Doctrine\ORM\EntityManagerInterface;

class GameService
{

    private $games;

    public function __construct(private RequestStack $requestStack, EntityManagerInterface $entityManager) {
        $this->requestStack = $requestStack;
        $this->entityManager = $entityManager;
    }

    public function initGame(): Game
    {
        $game = new Game();
        array_map(fn($card) => $game->addHand($card), $this->pullHand());
        return $game;
    }

    public function pullHand($size = 10) {
        $allCards = $this->generateCards();
        if($size > count($allCards)) {
            throw new \Exception("Cannot pull more than ".count($allCards)." cards");
        }
        $hand = array_map(fn($key) => $allCards[$key], array_rand($allCards, $size));
        return $hand;
    }

    public function generateCards()
    {
        $allCards = array_map(fn($value) => [
            new Card($value, 'Carreaux'),
            new Card($value, 'Cœur'),
            new Card($value, 'Pique'),
            new Card($value, 'Trèfle')
        ], range(1, 13));
        $allCards = array_merge(...$allCards);
        shuffle($allCards);
        return $allCards;
    }


}