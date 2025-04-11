<?php

namespace App\Application\Client;

use App\Entity\Client;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;

class CreateClientUseCase
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
        } catch (UniqueConstraintViolationException) {
            throw new \InvalidArgumentException('Email already in use');
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