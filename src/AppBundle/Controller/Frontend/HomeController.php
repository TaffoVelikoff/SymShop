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
			
			echo 'home'; exit;
			// Get twig

			return $this->render('admin/base.html.twig', array(
			    'variable_name' => 'variable_value',
			));
		}

	}
?>