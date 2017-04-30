<?php
	namespace AppBundle\Controller\Frontend;

	use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
	use Symfony\Bundle\FrameworkBundle\Controller\Controller;
	use Symfony\Component\HttpFoundation\Response;

	class HomeController extends Controller {

		/**
		*@Route("/", name="home")
		*/
		public function index(){
			
			// Doctrine
			$em = $this->getDoctrine()->getManager();

			// Twig
			$twig = array();

			// Get user and cart
			if($this->getUser()) {
				$twig['cart'] 		= $em->getRepository('AppBundle:User')->getCurrentCart($this->getUser()->getId());
				$twig['cprodCount'] = $em->getRepository('AppBundle:Cart')->getProductsCount($twig['cart']->getId());
				$twig['totalPrice']	= $em->getRepository('AppBundle:Cart')->getTotalPrice($twig['cart']->getId());
			}

			// Get latest products
			$latestProds = $em->getRepository('AppBundle:Product')->getLatest(10);
			$twig['latestProds'] = $latestProds;

			return $this->render('frontend/index.htm', $twig);
		}

	}
?>