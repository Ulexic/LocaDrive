<?php

namespace App\Application;

use App\Entity\Client;
use Doctrine\ORM\EntityManagerInterface;

// TODO: check auth
class LoginUseCase
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }

    private function saveClient($client): void
    {
        try {
            $this->entityManager->persist($client);
            $this->entityManager->flush();
        } catch (\Exception) {
            throw new \RuntimeException('Failed to save client');
        }
    }

    public function execute($firstname, $lastname, $email, $password, $licenceIssueDate): array
    {
        try {
            $client = new Client($firstname, $lastname, $email, $password, $licenceIssueDate);
        } catch (\InvalidArgumentException $e) {
            throw new \InvalidArgumentException('Invalid client data: ' . $e->getMessage());
        }

        $this->saveClient($client);

        return $client->serialize();
    }
}