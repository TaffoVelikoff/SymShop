<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\File;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ProductRepository")
 * @ORM\Table(name="products")
 * @UniqueEntity(fields={"name"}, message="This product already exists!")
 */
class Product 
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", unique=true)
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="products")
     */
    private $category = null;

    /**
     * @ORM\Column(type="text", length=1024)
     * @Assert\NotBlank()
     */
    private $description;

    /**
     * @ORM\Column(type="float")
     * @Assert\Range( min = 0, max = 100, maxMessage = "Promo should be less than 100%.", minMessage = "Promo % should not be a negative number.")
     */
    private $promo;

    /**
     * @ORM\Column(type="integer")
     * @Assert\Range( min = 0, minMessage = "Quantity should be at least 0.")
     */
    private $quantity = 0;

    /**
     * @ORM\Column(type="float")
     * @Assert\Range( min = 1, minMessage = "Price should be at least 1 BGN.")
     */
    private $price = 1;

    private $actualPrice = null;

    /**
     * @Assert\File(mimeTypes={ "image/png", "image/jpg", "image/jpeg" })
     */
    private $photoform;

    /**
     * @ORM\Column(type="string")
     *
     */
    private $photo = '';

    /**
     * @ORM\Column(type="integer")
     */
    private $ord;

    private $promoPercent = null;

    // Id
    public function getId() {
        return $this->id;
    }

    // Name
    public function getName() {
        return $this->name;
    }

    public function setName($name) {
       return $this->name = $name;
    }

    // Description
    public function setDescription($description) {
        return $this->description = $description;
    }

    public function getDescription() {
        return $this->description;
    }

    // Promo
    public function getPromo() {
        return $this->promo;
    }

    public function setPromo($promo) {
      return $this->promo = $promo;
    }

    // Quantity
    public function getQuantity() {
        return $this->quantity;
    }

    public function setQuantity($quantity) {
      return $this->quantity = $quantity;
    }

    // Price
    public function getPrice() {
        return $this->price;
    }

    public function setPrice($price) {
      return $this->price = $price;
    }

    // Ord
    public function getOrd() {
        return $this->ord;
    }

    public function setOrd($ord) {
      return $this->ord = $ord;
    }

    // Category
    public function getCategory() {
        return $this->category;
    }

    public function setCategory(Category $category) {
      return $this->category = $category;
    }

    // Category ID
    public function getCategoryId() {
        return $this->category->getId();
    }

    // In Stock
    public function getInStock() {
        $qtty = $this->quantity;

        if($qtty > 0) {
            return 'YES';
        } else {
            return 'NO';
        }
    }

    // Photo
    public function getPhoto()
    {
        if(!empty($this->photo)) {
            return $this->photo;
        } else {
            return null;
        } 
    }

    public function setPhoto($photo)
    {
        $this->photo = $photo;

        return $this;
    }

    public function getPhotoform() {
        return $this->photoform;
    }

    public function setPhotoform($photoform) {
      return $this->photoform = $photoform;
    }

    // Promo percent
    public function getPromoPercent() {

        $promos = array(
            // product promo
            $this->promo,
            // category promo
            $this->getCategory()->getPromo(),
            // get global promo
            $this->getCategory()->getSite()->getGlobalPromo()
        );

        return max($promos);
    }

    // Actual Price 
    public function getActualPrice() {
        $promo = $this->getPromoPercent();
        $price = $this->getPrice();

        $discounted = $price * 0.01 * $promo;
        $actual = $price - $discounted;

        return $actual;
    }
}
