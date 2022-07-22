<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\MapElementRepository;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api')]
class ApiController extends AbstractController
{
    public function __construct(private readonly MapElementRepository $elementRepository) {}

    /**
     * @throws \Exception
     */
    #[Route('/crimes', methods: ['GET'])]
    public function getCrimes(Request $request): Response
    {
        $from = new DateTimeImmutable($request->query->get('from'));
        $to   = new DateTimeImmutable($request->query->get('to'));

        return $this->json($this->elementRepository->getCrimes($from, $to));
    }

    #[Route('/crimes/{id}', methods: ['GET'])]
    public function getCrimeById(int $id): Response
    {
        return $this->json($this->elementRepository->getCrimeById($id));
    }
}
