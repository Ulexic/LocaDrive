<?php

namespace App\Entity;

use App\Repository\ClientRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: ClientRepository::class)]
class Client implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    private ?string $firstname = null;

    #[ORM\Column(length: 255)]
    private ?string $lastname = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $licenseIssueDate = null;

    #[ORM\OneToMany(targetEntity: Order::class, mappedBy: 'client', cascade: ['persist', 'remove'])]
    private Collection $orders;


    private function checkPassword($password): void
    {
        if (strlen($password) < 8) {
            throw new \InvalidArgumentException('Password must be at least 8 characters long.');
        }

        // Check for at least 4 digit
        if (!preg_match('/\d.*\d.*\d.*\d/', $password)) {
            throw new \InvalidArgumentException('Password must contain at least 4 digits.');
        }

        // Check for at least 4 letter
        if (!preg_match('/[a-zA-Z].*[a-zA-Z].*[a-zA-Z].*[a-zA-Z]/', $password)) {
            throw new \InvalidArgumentException('Password must contain at least 4 letters.');
        }
    }

    private function createOrder(): Order
    {
        $payment = new Payment(null, null);
        $order = new Order($payment, Order::STATUS_IN_CART, new ArrayCollection(), $this);
        $this->orders->add($order);

        return $order;
    }

    public function __construct($firstname, $lastname, $email, $password, $licenceIssueDate)
    {
        $this->checkPassword($password);

        $this->firstname = $firstname;
        $this->lastname = $lastname;
        $this->email = $email;
        $this->password = $password;
        $this->orders = new ArrayCollection();
        $order = $this->createOrder();
        $this->orders->add($order);
        try {
            $this->licenseIssueDate = new \DateTime($licenceIssueDate);
        } catch (\DateMalformedStringException) {
            throw new \InvalidArgumentException('Licence issue date must be a valid date.');
        }
    }

    public function serialize(): array
    {
        return [
            'id' => $this->id,
            'firstname' => $this->firstname,
            'lastname' => $this->lastname,
            'email' => $this->email,
            'licenseIssueDate' => $this->licenseIssueDate,
            'orders' => $this->orders->map(function (Order $order) {
                return $order->serialize();
            })->toArray(),
        ];
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): static
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): static
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getLicenseIssueDate(): ?\DateTimeInterface
    {
        return $this->licenseIssueDate;
    }

    public function setLicenseIssueDate(\DateTimeInterface $licenseIssueDate): static
    {
        $this->licenseIssueDate = $licenseIssueDate;

        return $this;
    }

    public function getOrders(): Collection
    {
        return $this->orders;
    }

    public function getCurrentOrder(): Order
    {
        if (!$this->orders->isInitialized()) {
            $this->orders->getValues(); // Triggers initialization
        }

        foreach ($this->orders as $order) {
            if ($order->getStatus() === Order::STATUS_IN_CART) {
                return $order;
            }
        }

        return $this->createOrder();
    }

    public function getRoles(): array
    {
        return ['ROLE_CLIENT'];
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function checkHasOrder($order): void
    {
        if (!$this->orders->contains($order)) {
            throw new \InvalidArgumentException('Client does not have this order.');
        }
    }

    public function checkHasPayment($payment): void
    {
        if (!$this->getCurrentOrder()->getPayment() === $payment) {
            throw new \InvalidArgumentException('Client does not have this payment.');
        }
    }

    public function checkHasReservation($reservation): void
    {
        if (!$this->getCurrentOrder()->getReservations()->contains($reservation)) {
            throw new \InvalidArgumentException('Client does not have this reservation.');
        }
    }
}
