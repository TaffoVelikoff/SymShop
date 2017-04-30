<?php
	namespace AppBundle\Controller\Frontend;

	use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
	use Symfony\Bundle\FrameworkBundle\Controller\Controller;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\HttpFoundation\Request;
	use AppBundle\Form\RegisterForm;
	use AppBundle\Entity\User;
	use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

	class RegisterController extends Controller {

		/**
		*@Route("/register", name="register")
		*/
		public function register(Request $request){

			// Redirect if user is logged
			if($this->getUser()) {
				return $this->redirectToRoute('home');
			}

			// Doctrine
			$em = $this->getDoctrine()->getManager();

			// Create user
			$user = new User();

			// User form
			$form = $this->createForm(RegisterForm::class, $user);

			$form->handleRequest($request);
			if($form->isSubmitted() && $form->isValid()) {

				// Encode user password
				$encoder = $this->container->get('security.password_encoder');
				$encoded = $encoder->encodePassword($user, $form['password']->getData());
				$user->setPassword($encoded);
				
				$em->persist($user);
				$em->flush();

				// Success message
				$this->addFlash('success', 'You can now login!');
				return $this->redirectToRoute('home');
			}
			
			return $this->render('frontend/register.htm', ['form' => $form->createView()]);
		}

	}
?>