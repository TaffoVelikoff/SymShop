<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CartRepository")
 * @ORM\Table(name="carts")
 */
class Cart 
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="carts")
     */
    private $user = null;

    /**
     * @ORM\OneToMany(targetEntity="CartProduct", mappedBy="cart")
     * @ORM\OrderBy({"id"="DESC"})
     */
    private $products;

    public function __construct() {
        $this->products = new ArrayCollection();
    }

    /**
     * @ORM\Column(type="string")
     */
    private $deliveryPerson = '';

    /**
     * @ORM\Column(type="string")
     */
    private $deliveryAddress = '';

    /**
     * @ORM\Column(type="string")
     */
    private $deliveryEmail = '';

    /**
     * @ORM\Column(type="string")
     */
    private $deliveryPhone = '';

    /**
     * @ORM\Column(type="integer")
     */
    private $confirmed = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private $payment = 1;

    // ID
    public function getId() {
        return $this->id;
    }

    // User
    public function setUser(User $user) {
      return $this->user = $user;
    }

    public function getUser() {
        return $this->user;
    }

    // Confirmed
    public function setConfirmed($timestamp) {
        return $this->confirmed = $timestamp;
    }

    public function getConfirmed($format = 'null') {
        if($format == null) {
           return $this->confirmed; 
       } else {
            return date($format, $this->confirmed);
       }
        
    }

    // Products
    public function getProducts() {
        return $this->products;
    }

    // Delivery
    public function setDeliveryPerson($deliveryPerson) {
        return $this->deliveryPerson = $deliveryPerson;
    }

    public function getDeliveryPerson() {
        return $this->deliveryPerson;
    }

    public function setDeliveryAddress($deliveryAddress) {
        return $this->deliveryAddress = $deliveryAddress;
    }

    public function getDeliveryAddress() {
        return $this->deliveryAddress;
    }

    public function setDeliveryEmail($deliveryEmail) {
        return $this->deliveryEmail = $deliveryEmail;
    }

    public function getDeliveryEmail() {
        return $this->deliveryEmail;
    }

    public function setDeliveryPhone($deliveryPhone) {
        return $this->deliveryPhone = $deliveryPhone;
    }

    public function getDeliveryPhone() {
        return $this->deliveryPhone;
    }

    // Payment
    public function setPayment($payment) {
        return $this->payment = $payment;
    }

    public function getPayment() {
        if($this->payment == 1) {
            return 'wallet';
        } else {
            return 'admin';
        }
    }

}
