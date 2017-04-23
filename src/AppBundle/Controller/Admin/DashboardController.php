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

			// Display template
			return $this->render('admin/dashboard.htm');
		}

	}
?>