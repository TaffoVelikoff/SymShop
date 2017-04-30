<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="cart_products")
 */
class CartProduct
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Cart", inversedBy="products")
     */
    private $cart = null;

    /**
     * @ORM\ManyToOne(targetEntity="Product")
     */
    private $product = null;

    /**
     * @ORM\Column(type="float")
     */
    private $price = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private $quantity = 0;

    //private $totalPrice = 0;

    // ID
    public function getId() {
        return $this->id;
    }

    // Cart
    public function setCart(Cart $cart) {
      return $this->cart = $cart;
    }

    public function getCart() {
        return $this->cart;
    }

    // Cart
    public function setProduct(Product $product) {
      return $this->product = $product;
    }

    public function getProduct() {
        return $this->product;
    }

    // Price
    public function setPrice($price) {
        return $this->price = $price;
    }

    public function getPrice() {
        return $this->price;
    }

    // Quanity
    public function setQuantity($quantity) {
        return $this->quantity = $quantity;
    }

    public function getQuantity() {
        return $this->quantity;
    }

    // Total price of cart product
    public function getTotalPrice() {
        return $this->price * $this->quantity;
    }

}
