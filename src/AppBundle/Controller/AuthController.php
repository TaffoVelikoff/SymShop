<?php
	namespace AppBundle\Controller;

	use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
	use Symfony\Bundle\FrameworkBundle\Controller\Controller;
	use Symfony\Component\HttpFoundation\Response;
	use AppBundle\Form\LoginForm;
	use AppBundle\Entity\User;

	class AuthController extends Controller {

		/**
		* @Route("/login", name="auth_login")
		*/
		public function loginAction() {

			$authenticationUtils = $this->get('security.authentication_utils');

		    // get the login error if there is one
		    $error = $authenticationUtils->getLastAuthenticationError();

		    // last username entered by the user
		    $lastUsername = $authenticationUtils->getLastUsername();

		    // Generate the form
		    $form = $this->CreateForm(LoginForm::class, [
		    	'_username'	=> $lastUsername
		    ]);

		    // Display template
		    return $this->render('login.htm', array(
		        'form' 			=> $form->createView(),
		        'error'         => $error,
		    ));
		}

		/**
		* @Route("/logout", name="auth_logout")
		*/
		public function logoutAction() {
			throw new \Exception('');
		}
	}
?>