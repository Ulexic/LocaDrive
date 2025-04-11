<?php

namespace App\Application\Order;

use App\Entity\Order;
use App\Repository\ClientRepository;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;

class PayUseCase
{
    private EntityManagerInterface $entityManager;
    private OrderRepository $orderRepository;
    private ClientRepository $clientRepository;

    public function __construct(EntityManagerInterface $entityManager, OrderRepository $orderRepository, ClientRepository $clientRepository){
        $this->entityManager = $entityManager;
        $this->orderRepository = $orderRepository;
        $this->clientRepository = $clientRepository;
    }

    private function getOrder($id): Order {
        $order = $this->orderRepository->find($id);
        if (!$order) {
            throw new \InvalidArgumentException('Order not found');
        }
        return $order;
    }

    private function saveOrder(Order $order): void {
        try {
            $this->entityManager->persist($order);
            $this->entityManager->flush();
        } catch (\Exception) {
            throw new \RuntimeException('Failed to save order');
        }
    }

    public function execute($orderId, $clientId): array
    {
        $order = $this->getOrder($orderId);
        $client = $this->clientRepository->find($clientId);
        $client->checkHasOrder($order);
        $order->pay();

        $this->saveOrder($order);

        return $order->serialize();
    }
}