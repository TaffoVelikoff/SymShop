<?php 

namespace AppBundle\Twig;


use Doctrine\ORM\EntityManager;

class AppSettings extends \Twig_Extension implements \Twig_Extension_GlobalsInterface
{
    protected $em;

    public function __construct(EntityManager $em)
    {
        // Load entity manager
        $this->em = $em;
    }

    public function getGlobals()
    {
        // Getting some custom settings from DB and sending them to TWIG
        return array(
            'shopName'      => $this->em->getRepository('AppBundle:Setting')->findOneBy(['id' => 1])->getShopName(),
            'shopAddress'   => $this->em->getRepository('AppBundle:Setting')->findOneBy(['id' => 1])->getAddress(),

            // Also get the categories, we show them in frontend - footer in all pages
            'shopCats'      => $this->em->getRepository('AppBundle:Category')->findBy([], ['ord' => 'DESC']),
        );
    }
}

?>