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
    public function __construct(private readonly MapElementRepository $elementRepository) {}

    #[Route('/crimes', methods: ['GET'])]
    public function getCrimes(): Response
    {
        return $this->json($this->elementRepository->getCrimes());
    }

    #[Route('/crimes/{id}', methods: ['GET'])]
    public function getCrimeById(int $id): Response
    {
        return $this->json($this->elementRepository->find($id));
    }
}
