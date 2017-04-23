<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SettingRepository")
 * @ORM\Table(name="settings")
 */
class Setting 
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $shopname;

    /**
     * @ORM\Column(type="string")
     */
    private $address;

    /**
     * @ORM\Column(type="integer")
     */
    private $globalpromo;

    // Setters
    public function setShopname($shopname) {
        $this->shopname = $shopname;
    }

    public function setAddress($address) {
        $this->address = $address;
    }

    public function setGlobalPromo($promo) {
        $this->globalpromo = $promo;
    }

    // Getter
    public function getShopname() {
        return $this->shopname;
    }

    public function getAddress() {
       return $this->address;
    }

    public function getGlobalPromo() {
       return $this->globalpromo;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }
}
