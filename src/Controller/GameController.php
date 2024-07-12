<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

use App\Service\GameService;
use App\Service\SerializerService;

#[Route('/game')]
class GameController extends AbstractController
{
    #[Route('/init')]
    public function init(GameService $gameService, SerializerService $serializerService): JsonResponse
    {
        $game = $gameService->initGame();
        $serializerService->save($game);
        return $this->json($game->normalize());
    }

    #[Route('/{uid}/hand')]
    public function hand(GameService $gameService, SerializerService $serializerService, $uid): JsonResponse
    {
        $game = $serializerService->fetch($uid);
        return $this->json($game->normalize(true));
    }

    #[Route('/{uid}/sort')]
    public function sort(GameService $gameService, SerializerService $serializerService, $uid): JsonResponse
    {
        $game = $serializerService->fetch($uid);
        $game->sort();
        return $this->json($game->normalize(true));
    }
}
