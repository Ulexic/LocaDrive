<?php

namespace App\Application\Payment;

use App\Entity\Payment;
use App\Repository\ClientRepository;
use App\Repository\PaymentRepository;
use Doctrine\ORM\EntityManagerInterface;

class ChoosePaymentMethodUseCase
{
    private EntityManagerInterface $entityManager;
    private  PaymentRepository $paymentRepository;

    private  ClientRepository $clientRepository;


    public function __construct(EntityManagerInterface $entityManager, PaymentRepository $paymentRepository, ClientRepository $clientRepository){
        $this->entityManager = $entityManager;
        $this->paymentRepository = $paymentRepository;
        $this->clientRepository = $clientRepository;
    }

    private function getPayment($id): Payment {
        $payment = $this->paymentRepository->find($id);
        if (!$payment) {
            throw new \InvalidArgumentException('Payment not found');
        }
        return $payment;
    }

    private function savePayment(Payment $payment): void {
        try {
            $this->entityManager->persist($payment);
            $this->entityManager->flush();
        } catch (\Exception) {
            throw new \RuntimeException('Failed to save payment');
        }
    }

    public function execute($paymentId, $method, $clientId): array
    {
        $payment = $this->getPayment($paymentId);
        $client = $this->clientRepository->find($clientId);
        $client->checkHasPayment($payment);

        $payment->setMethod($method);

        $this->savePayment($payment);

        return $payment->serialize();
    }
}