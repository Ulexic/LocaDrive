<?php

namespace App\Entity;

use App\Repository\ReservationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReservationRepository::class)]
class Booking
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $startDate = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $endDate = null;

    // one-to-one
    #[ORM\ManyToOne(targetEntity: Vehicle::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Vehicle $vehicle = null;

    #[ORM\ManyToOne(targetEntity: Insurance::class)]
    private ?Insurance $insurance = null;

    #[ORM\ManyToOne(targetEntity: Order::class, inversedBy: 'reservations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Order $order = null;

    public function __construct(Vehicle $vehicle, string $startDate, string $endDate, ?Insurance $insurance, Order $order){
        $dates = $this->checkDates($startDate, $endDate);

        $this->startDate = $dates[0];
        $this->endDate = $dates[1];
        $this->vehicle = $vehicle;
        $this->order = $order;
        $this->insurance = $insurance;
    }

    public function serialize(): array
    {
        return [
            'id' => $this->id,
            'startDate' => $this->startDate->format('Y-m-d H:i:s'),
            'endDate' => $this->endDate->format('Y-m-d H:i:s'),
            'vehicle' => $this->vehicle?->serialize(),
            'insurance' => $this->insurance?->serialize(),
        ];
    }
    public function addInsurance(Insurance $insurance): void
    {
        if ($this->insurance !== null) {
            throw new \InvalidArgumentException('Booking already has an insurance');
        }

        $this->insurance = $insurance;
    }

    public function removeInsurance(): void
    {
        if ($this->insurance === null) {
            throw new \InvalidArgumentException('Booking does not have an insurance');
        }

        $this->order->getPayment()->removePrice($this->insurance->getPrice());
        $this->insurance= null;

    }

    private function checkDates(string $startDate, string $endDate): array
    {
        try {
            $start = new \DateTime($startDate);
            $end = new \DateTime($endDate);
        } catch (\Exception) {
            throw new \InvalidArgumentException('Invalid date format');
        }
        if ($start < new \DateTime()) {
            throw new \InvalidArgumentException('Start date must be in the future');
        }

        if ($start > $end) {
            throw new \InvalidArgumentException('Start date must be before end date');
        }

        return [$start, $end];
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): static
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeInterface $endDate): static
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getVehicle(): ?Vehicle
    {
        return $this->vehicle;
    }

    public function setVehicle(Vehicle $vehicle): static
    {
        $this->vehicle = $vehicle;

        return $this;
    }

    public function getInsurance(): ?Insurance
    {
        return $this->insurance;
    }

    public function setInsurance(Insurance $insurance): static
    {
        $this->insurance = $insurance;

        return $this;
    }

    public function getOrder(): ?Order
    {
        return $this->order;
    }

    public function setOrder(Order $order): static
    {
        $this->order = $order;

        return $this;
    }

    public function calculatePrice(): float
    {
        $delta = $this->startDate->diff($this->endDate)->days;


        return $delta * $this->vehicle->getDailyPrice();
    }

    public function adjustPrice(Order $order): void
    {
        $order->getPayment()->removePrice($this->calculatePrice());

        $insurance = $this->insurance;
        if ($insurance) {
            $order->getPayment()->removePrice($insurance->getPrice());
        }
    }
}
