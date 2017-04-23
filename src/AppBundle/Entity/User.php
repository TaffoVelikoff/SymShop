<?php

namespace AppBundle\Entity;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\Role\Role;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 * @ORM\Table(name="user")
 * @UniqueEntity(fields={"email"}, message="It looks like you already have an account!")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * Full name
     *
     * @Assert\NotBlank()
     * @ORM\Column(type="string")
     */
    private $fullname;

    /**
     * Delivery address
     *
     * @Assert\NotBlank()
     * @ORM\Column(type="string")
     */
    private $address;

    /**
     * User Cash
     *
     * @Assert\NotBlank()
     * @Assert\Range( min = 0, max = 500, maxMessage = "Users should not have more than 500 BGN in wallet.", minMessage = "Users can not have a negative sum in their wallets ...")
     * @ORM\Column(type="float")
     */
    private $cash;

    /**
     * @Assert\NotBlank()
     * @Assert\Email()
     * @ORM\Column(type="string", unique=true)
     */
    private $email;

    /**
     * The encoded password
     *
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * A non-persisted field that's used to create the encoded password.
     * @Assert\NotBlank(groups={"Registration"})
     *
     * @var string
     */
    private $plainPassword;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="json_array")
     */
    private $roles = [];

    // needed by the security system
    public function getUsername()
    {
        return $this->email;
    }

    public function getRoles()
    {
        $roles = $this->roles;

        return $roles;
    }

    public function setRoles(array $roles)
    {
        $this->roles = $roles;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getSalt()
    {
        //
    }

    public function eraseCredentials()
    {
        $this->plainPassword = null;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    public function getID()
    {
        return $this->id;
    }

    public function setPlainPassword($plainPassword)
    {
        $this->plainPassword = $plainPassword;

        // Doctrine *not* saving this entity, if only plainPassword changes
        $this->password = null;
    }

    // Full name
    public function getFullName() {
        return $this->fullname;
    }

    public function setFullName($fullname) {
        $this->fullname = $fullname;
    }

    // Address
    public function getAddress() {
        return $this->address;
    }

    public function setAddress($address) {
        $this->address = $address;
    }

    // Cash
    public function getCash() {
        return $this->cash;
    }

    public function setCash($cash) {
        $this->cash = $cash;
    }

    // Roles
    public function getUserRoles() {
        $userRoles = $this->roles;

        $roles = '';

        foreach($userRoles as $role) {
            $roleName = explode('_', $role);
            $roles .= $roleName[1];
            if($role != end($userRoles)) {
                $roles .= ', ';
            }
        }

        return $roles;
    }
}
