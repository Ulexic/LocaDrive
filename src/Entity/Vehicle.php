<?php

namespace App\Entity;

use App\Repository\VehicleRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VehicleRepository::class)]
class Vehicle
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?float $dailyPrice = null;

    #[ORM\Column(length: 255)]
    private ?string $model = null;

    #[ORM\Column(length: 255)]
    private ?string $constructor = null;

    // many-to-

    public function getId(): ?int
    {
        return $this->id;
    }

    public function __construct(string $model, string $constructor, float $dailyPrice)
    {
        $this->checkDailyPrice($dailyPrice);
        $this->model = $model;
        $this->constructor = $constructor;
        $this->dailyPrice = $dailyPrice;
    }

    public function serialize(): array
    {
        return [
            'id' => $this->id,
            'model' => $this->model,
            'constructor' => $this->constructor,
            'dailyPrice' => $this->dailyPrice,
        ];
    }
    private function checkDailyPrice(float $dailyPrice): void
    {
        if ($dailyPrice <= 0) {
            throw new \InvalidArgumentException('Daily price must be greater than zero.');
        }
    }

    public function update(string $model, string $constructor, float $dailyPrice): void
    {
        $this->checkDailyPrice($dailyPrice);
        $this->model = $model;
        $this->constructor = $constructor;
        $this->dailyPrice = $dailyPrice;
    }

    public function getDailyPrice(): ?float
    {
        return $this->dailyPrice;
    }

    public function setDailyPrice(float $dailyPrice): static
    {
        $this->dailyPrice = $dailyPrice;

        return $this;
    }

    public function getModel(): ?string
    {
        return $this->model;
    }

    public function setModel(string $model): static
    {
        $this->model = $model;

        return $this;
    }

    public function getConstructor(): ?string
    {
        return $this->constructor;
    }

    public function setConstructor(string $constructor): static
    {
        $this->constructor = $constructor;

        return $this;
    }
}
