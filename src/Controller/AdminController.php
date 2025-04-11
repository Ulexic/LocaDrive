<?php

namespace App\Controller;

use App\Entity\Admin;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }

    #[Route(path: '/admin', name: 'admin', methods: ['POST'])]
    public function create(): Response
    {
        $email = "admin@test.fr";
        $password = "admin";

        $admin = new Admin($email, $password);

        try {
            $this->entityManager->persist($admin);
            $this->entityManager->flush();
        } catch (\Exception $e) {
            return new Response($this->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ]));
        }

        return new Response($this->json([
            'status' => 'success',
            'message' => 'Admin created successfully'
        ]));
    }
}