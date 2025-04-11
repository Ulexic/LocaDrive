<?php

namespace App\Entity;

use App\Repository\PaymentRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PaymentRepository::class)]
class Payment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // TODO: add enum for method
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $method = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $price = null;

    public const METHOD_CREDIT_CARD = 'credit_card';
    public const METHOD_PAYPAL = 'paypal';

    public function __construct($method, $price)
    {
        $this->method = $method;
        $this->price = $price;
    }

    public function serialize(): array
    {
        return [
            'id' => $this->id,
            'method' => $this->method,
            'price' => $this->price,
        ];
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMethod(): ?string
    {
        return $this->method;
    }

    public function setMethod(string $method): static
    {
        $this->method = $method;

        return $this;
    }

    public function addPrice(float $amount): void {
        $this->price += $amount;
    }

    public function removePrice(float $amount): void
    {
        $this->price -= $amount;
    }
}
