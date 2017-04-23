<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CategoryRepository")
 * @ORM\Table(name="categories")
 * @UniqueEntity(fields={"name"}, message="This category already exists!")
 */
class Category 
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
     * @ORM\OneToMany(targetEntity="Product", mappedBy="category")
     * @ORM\OrderBy({"ord"="DESC"})
     */
    private $products;

    /**
     * @ORM\Column(type="integer")
     */
    private $ord;

    /**
     * @ORM\Column(type="float")
     * @Assert\Range( min = 0, max = 100, maxMessage = "Promo should be less than 100%.", minMessage = "Promo % should not be a negative number.")
     */
    private $promo;

    public function __construct() {
        $this->products = new ArrayCollection();
    }

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

    // Ord
    public function getOrd() {
        return $this->ord;
    }

    public function setOrd($ord) {
      return $this->ord = $ord;
    }

    // Promo
    public function getPromo() {
        return $this->promo;
    }

    public function setPromo($promo) {
      return $this->promo = $promo;
    }

    // Products
    public function getProducts() {
        return $this->products;
    }

    public function getProductsCount() {
        return count($this->products);
    }

    // Next free order
    public function nextFreeOrd()
    {   
        global $kernel;
        $em = $kernel->getContainer()->get( 'doctrine.orm.entity_manager' );

        // Get max ord
        $maxord = $em->createQueryBuilder()
                ->select('MAX(b.ord)')
                ->from('AppBundle:Category', 'b')
                ->getQuery()
                ->getSingleScalarResult();

        return($maxord + 1);
    }

    // Select next order
    public function nextOrd() {
        global $kernel;
        $em = $kernel->getContainer()->get( 'doctrine.orm.entity_manager' );

        // Get max ord
        $next = $em->createQueryBuilder()
                ->select('c.id')
                ->from('AppBundle:Category', 'c')
                ->where('c.ord > '.$this->getOrd())
                ->addOrderBy('c.ord', 'ASC')
                ->setMaxResults(1)
                ->getQuery()->getResult();

        if(count($next) > 0) {
            return $next[0]['id'];
        } else {
            return null;
        }
    }

    // Select previous order
    public function prevOrd() {
        global $kernel;
        $em = $kernel->getContainer()->get( 'doctrine.orm.entity_manager' );

        // Get max ord
        $next = $em->createQueryBuilder()
                ->select('c.id')
                ->from('AppBundle:Category', 'c')
                ->where('c.ord < '.$this->getOrd())
                ->addOrderBy('c.ord', 'DESC')
                ->setMaxResults(1)
                ->getQuery()->getResult();

        if(count($next) > 0) {
            return $next[0]['id'];
        } else {
            return null;
        }
    }
}
