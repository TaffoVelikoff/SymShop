<?php

namespace AppBundle\Repository;

/**
 * CategoryRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ProductRepository extends \Doctrine\ORM\EntityRepository
{
	// Next free order
    public function nextFreeOrd()
    {   
       	$em = $this->getEntityManager();

        // Get max ord
        $maxord = $em->createQueryBuilder()
                ->select('MAX(b.ord)')
                ->from('AppBundle:Product', 'b')
                ->getQuery()
                ->getSingleScalarResult();

        return($maxord + 1);
    }

    // Check what's on top
    public function onTop($cat) {
    	$em = $this->getEntityManager();

    	$maxord = $em->createQueryBuilder()
                ->select('MAX(b.ord)')
                ->from('AppBundle:Product', 'b')
                ->where('b.category = '.$cat->getId())
                ->getQuery()
                ->getSingleScalarResult();

        return $maxord;
    }

    // Check what's on bottom
    public function onBottom($cat) {
    	$em = $this->getEntityManager();

    	$minord = $em->createQueryBuilder()
                ->select('MIN(b.ord)')
                ->from('AppBundle:Product', 'b')
                ->where('b.category = '.$cat->getId())
                ->getQuery()
                ->getSingleScalarResult();

        return $minord;
    }

    // Select next order
    public function nextOrd($id) {
        $em = $this->getEntityManager();

        // Current product
        $current = $em->getRepository('AppBundle:Product')->findOneBy(['id' => $id]);

        // Get max ord
        $next = $em->createQueryBuilder()
                ->select('c.id')
                ->from('AppBundle:Product', 'c')
                ->where('c.ord > '.$current->getOrd())
                ->andWhere('c.category = '.$current->getCategoryId())
                ->addOrderBy('c.ord', 'ASC')
                ->setMaxResults(1)
                ->getQuery()->getResult();

        // Return next ord
        if(count($next) > 0) {
        	return $next[0]['id'];
        } else {            
            return null;
        }
    }

    // Select previous order
    public function prevOrd($id) {
        $em = $this->getEntityManager();

        // Current product order
        $current = $em->getRepository('AppBundle:Product')->findOneBy(['id' => $id]);

        // Get max ord
        $next = $em->createQueryBuilder()
                ->select('c.id')
                ->from('AppBundle:Product', 'c')
                ->where('c.ord < '.$current->getOrd())
                ->andWhere('c.category = '.$current->getCategoryId())
                ->addOrderBy('c.ord', 'DESC')
                ->setMaxResults(1)
                ->getQuery()->getResult();

        // Return previous ord
        if(count($next) > 0) {
            return $next[0]['id'];
        } else {
            return null;
        }
    }

    // Get latest products
    public function getLatest($limit = 5) {
        $em = $this->getEntityManager();

        $prods = $em->createQueryBuilder()
                ->select('c')
                ->from('AppBundle:Product', 'c')
                ->where('c.quantity > 0')
                ->addOrderBy('c.id', 'DESC')
                ->setMaxResults($limit)
                ->getQuery()->getResult();

        return $prods;
    }

    // Get related products
    public function getRelated($limit = 5, $category) {
        $em = $this->getEntityManager();

        $prods = $em->createQueryBuilder()
                ->select('c')
                ->from('AppBundle:Product', 'c')
                ->where('c.quantity > 0')
                ->andWhere('c.category = '.$category->getId())
                ->addOrderBy('c.id', 'DESC')
                ->setMaxResults($limit)
                ->getQuery()->getResult();

        return $prods;
    }
}
