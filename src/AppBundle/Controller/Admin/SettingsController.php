<?php
	namespace AppBundle\Controller\Admin;

	use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
	use Symfony\Bundle\FrameworkBundle\Controller\Controller;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\HttpFoundation\Request;
	use AppBundle\Form\SettingsForm;

	class SettingsController extends Controller {

		/**
		*@Route("admin/settings", name="settings")
		*/
		public function index(Request $request) {

			// Doctrine
			$em = $this->getDoctrine()->getManager();

			// Check for editor
			$roles = $this->get('security.token_storage')->getToken()->getUser()->getRoles();
			if(!in_array('ROLE_ADMIN', $roles)) {
				return $this->redirectToRoute('dashboard');
			}

			// Get settings
			$settings = $em->getRepository('AppBundle:Setting')->findOneBy(['id' => 1]);

			// Settings form
			$form = $this->createForm(SettingsForm::class, $settings);

			$form->handleRequest($request);
			if($form->isSubmitted() && $form->isValid()) {
				
				// Change settings
				$settings->setShopName($form['shopname']->getData());
				
				$em->persist($settings);
				$em->flush();

				// Success message
				$this->addFlash('success', 'Succesfully updated!');
				return $this->redirectToRoute('settings');
			}

			// Display template
			return $this->render('admin/settings.htm', ['form' => $form->createView()]);
		}

	}
?>