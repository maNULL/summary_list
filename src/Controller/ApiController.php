<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\MapElementRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api')]
class ApiController extends AbstractController
{
    #[Route('/get_crimes')]
    public function index(MapElementRepository $elementRepository): Response
    {
        return $this->json($elementRepository->getCrimes());
    }
}
