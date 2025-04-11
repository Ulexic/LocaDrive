<?php

namespace App\Controller;

use App\Application\Client\CreateClientUseCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ClientController extends AbstractController
{
    private CreateClientUseCase $createClientUseCase;

    public function __construct(CreateClientUseCase $createClientUseCase){
        $this->createClientUseCase = $createClientUseCase;
    }

    #[Route(path: '/client', name: 'create_client', methods: ['POST'])]
    public function create(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);

        $firstname = $data['firstname'] ?? null;
        $lastname = $data['lastname'] ?? null;
        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;
        $licenceIssueDate = $data['licenceIssueDate'] ?? null;

        try {
            $client = $this->createClientUseCase->execute($firstname, $lastname, $email, $password, $licenceIssueDate);
        } catch (\Exception $e) {
            return new Response($this->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ]), Response::HTTP_BAD_REQUEST);
        }

        return new Response($this->json([
            'status' => 'success',
            'client' => $client
        ]));
    }
}