<?php

namespace App\Controller;

use App\Application\Order\PayUseCase;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
{
    private PayUseCase $payUseCase;

    public function __construct(PayUseCase $payUseCase)
    {
        $this->payUseCase = $payUseCase;
    }

    #[Route(path: 'client/{clientId}/order/{orderId}/pay', name: 'pay_order', methods: ['POST'])]
    public function pay(Request $request): Response
    {
        $orderId = $request->get('orderId');
        $clientId = $request->get('clientId');

        $order = $this->payUseCase->execute($clientId, $orderId);

        return new Response($this->json([
            'status' => 'success',
            'order' => $order,
        ]));
    }
}