<?php

declare(strict_types=1);

namespace App\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class User
 * @package App\Entity
 * @ORM\Entity
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({"producer"="App\Entity\Producer", "customer"="App\Entity\Customer"})
 * @UniqueEntity(fields="email", message="Il semblerait que vous soyez déjà inscrit.", entityClass="App\Entity\User")
 */
abstract class User implements UserInterface, \Serializable, EquatableInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="uuid")
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class=UuidGenerator::class)
     */
    protected UuidInterface $id;

    /**
     * @ORM\Column
     * @Assert\NotBlank
     */
    protected string $firstName = "";

    /**
     * @ORM\Column
     * @Assert\NotBlank
     */
    protected string $lastName = "";

    /**
     * @ORM\Column(unique=true)
     * @Assert\NotBlank
     * @Assert\Email
     */
    protected string $email = "";

    /**
     * @ORM\Column
     */
    protected string $password = "";

    /**
     * @Assert\NotBlank(groups={"password"})
     * @Assert\Length(min=8, groups={"password"})
     */
    protected ?string $plainPassword = null;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    protected DateTimeImmutable $registeredAt;

    /**
     * @ORM\Embedded(class="ForgottenPassword")
     */
    protected ?ForgottenPassword $forgottenPassword;

    /**
     * User constructor.
     */
    public function __construct()
    {
        $this->registeredAt = new DateTimeImmutable();
    }

    /**
     * @return UuidInterface
     */
    public function getId(): UuidInterface
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     */
    public function setLastName(string $lastName): void
    {
        $this->lastName = $lastName;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password): void
    {
        $this->forgottenPassword = null;
        $this->password = $password;
    }

    /**
     * @return string|null
     */
    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    /**
     * @param string|null $plainPassword
     */
    public function setPlainPassword(?string $plainPassword): void
    {
        $this->plainPassword = $plainPassword;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getRegisteredAt(): DateTimeImmutable
    {
        return $this->registeredAt;
    }

    /**
     * @param DateTimeImmutable $registeredAt
     */
    public function setRegisteredAt(DateTimeImmutable $registeredAt): void
    {
        $this->registeredAt = $registeredAt;
    }

    public function getSalt(): void
    {
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->email;
    }

    public function eraseCredentials(): void
    {
        $this->plainPassword = null;
    }

    public function hasForgotHisPassword(): void
    {
        $this->forgottenPassword = new ForgottenPassword();
    }

    /**
     * @return ForgottenPassword|null
     */
    public function getForgottenPassword(): ?ForgottenPassword
    {
        return $this->forgottenPassword;
    }

    /**
     * @return string
     */
    public function getFullName(): string
    {
        return sprintf("%s %s", $this->firstName, $this->lastName);
    }

    /**
     * @inheritDoc
     */
    public function serialize()
    {
        return serialize([
            $this->id,
            $this->email
        ]);
    }

    /**
     * @inheritDoc
     */
    public function unserialize($serialized)
    {
        list(
            $this->id,
            $this->email
            ) = unserialize($serialized);
    }

    /**
     * @inheritDoc
     */
    public function isEqualTo(UserInterface $user)
    {
        return $user->getUsername() === $this->getUsername();
    }
}
