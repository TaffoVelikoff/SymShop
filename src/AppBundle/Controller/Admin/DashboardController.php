<?php
	namespace AppBundle\Controller\Admin;

	use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
	use Symfony\Bundle\FrameworkBundle\Controller\Controller;
	use Symfony\Component\HttpFoundation\Response;

	class DashboardController extends Controller {

		/**
		*@Route("/admin", name="dashboard")
		*/
		public function index() {

			// Doctrine
			$em = $this->getDoctrine()->getManager();

			// Get all confirmed carts
			$carts = $em->getRepository('AppBundle:Cart')->getConfirmedCarts();

			// Display template
			return $this->render('admin/dashboard.htm', [
				'carts'		=> $carts,
				'cartRepo'	=> $carts = $em->getRepository('AppBundle:Cart')
			]);
		}

	}
?>