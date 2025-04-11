<?php

namespace App\Controller;

use App\Application\Payment\ChoosePaymentMethodUseCase;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PaymentController extends AbstractController
{
    private ChoosePaymentMethodUseCase $choosePaymentMethodUseCase;

    public function __construct(ChoosePaymentMethodUseCase $choosePaymentMethodUseCase)
    {
        $this->choosePaymentMethodUseCase = $choosePaymentMethodUseCase;
    }

    #[Route(path: '/client/{clientId}/payment/{paymentId}/method', name: 'choose_payment_method', methods: ['PUT'])]
    public function addInsurance(Request $request): Response
    {
        $paymentId = $request->get('paymentId');
        $clientId = $request->get('clientId');

        $data = json_decode($request->getContent(), true);
        $method = $data['method'] ?? null;

        try {
            $payment = $this->choosePaymentMethodUseCase->execute($paymentId, $method, $clientId);
        } catch (\Exception $e) {
            return new Response($this->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ]), Response::HTTP_BAD_REQUEST);
        }

        return new Response($this->json([
            'status' => 'success',
            'payment' => $payment
        ]));
    }
}