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

			return $this->render('frontend/index.htm');
		}

	}
?>