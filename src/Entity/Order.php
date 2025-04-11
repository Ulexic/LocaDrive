<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: '`order`')]
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(targetEntity: Payment::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Payment $payment = null;

    // TODO: add enum for status

    #[ORM\Column(length: 255)]
    private ?string $status = null;

    #[ORM\OneToMany(targetEntity: Booking::class, mappedBy: 'order', cascade: ['persist', 'remove'])]
    private Collection $reservations;


    #[ORM\ManyToOne(targetEntity: Client::class, inversedBy: 'orders')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Client $client = null;

    public const STATUS_IN_CART = 'in_cart';
    public const STATUS_PAID = 'paid';
    public const STATUS_CANCELLED = 'cancelled';

    public function __construct(Payment $payment, string $status, Collection $reservations, Client $client)
    {
        $this->payment = $payment;
        $this->status = $status;
        $this->reservations = $reservations;
        $this->client = $client;
    }

    public function serialize(): array
    {
        return [
            'id' => $this->id,
            'payment' => $this->payment?->serialize(),
            'status' => $this->status,
            'reservations' => $this->reservations->map(fn($reservation) => $reservation->serialize())->toArray(),
        ];
    }

    public function pay(): void
    {
        if ($this->status !== self::STATUS_IN_CART) {
            throw new \InvalidArgumentException('Order is not in cart');
        }

        if ($this->payment->getMethod() === null) {
            throw new \InvalidArgumentException('Payment method is not set');
        }

        $this->status = self::STATUS_PAID;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getPayment(): ?Payment
    {
        return $this->payment;
    }

    public function setPayment(Payment $payment): static
    {
        $this->payment = $payment;

        return $this;
    }

    public function getReservations(): Collection
    {
        return $this->reservations;
    }

    public function setReservations(Collection $reservations): static
    {
        $this->reservations = $reservations;

        return $this;
    }
    public function addReservation(Booking $reservation): void
    {
        $this->reservations->add($reservation);
    }

    public function checkIsInCart(): void
    {
        if ($this->status !== self::STATUS_IN_CART) {
            throw new \InvalidArgumentException('Order is not in cart');
        }
    }
}
