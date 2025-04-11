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
    private Collection $bookings;


    #[ORM\ManyToOne(targetEntity: Client::class, inversedBy: 'orders')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Client $client = null;

    public const STATUS_IN_CART = 'in_cart';
    public const STATUS_PAID = 'paid';
    public const STATUS_CANCELLED = 'cancelled';

    public function __construct(Payment $payment, string $status, Collection $bookings, Client $client)
    {
        $this->payment = $payment;
        $this->status = $status;
        $this->bookings = $bookings;
        $this->client = $client;
    }

    public function serialize(): array
    {
        return [
            'id' => $this->id,
            'payment' => $this->payment?->serialize(),
            'status' => $this->status,
            'bookings' => $this->bookings->map(fn($booking) => $booking->serialize())->toArray(),
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

    public function getBookings(): Collection
    {
        return $this->bookings;
    }

    public function setBookings(Collection $bookings): static
    {
        $this->bookings = $bookings;

        return $this;
    }
    public function addBooking(Booking $booking): void
    {
        $this->bookings->add($booking);
    }

    public function checkIsInCart(): void
    {
        if ($this->status !== self::STATUS_IN_CART) {
            throw new \InvalidArgumentException('Order is not in cart');
        }
    }
}
