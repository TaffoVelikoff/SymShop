<?php
	namespace AppBundle\Controller\Frontend;

	use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
	use Symfony\Bundle\FrameworkBundle\Controller\Controller;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\HttpFoundation\Request;
	use AppBundle\Form\RegisterForm;
	use AppBundle\Entity\User;
	use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

	class ProfileController extends Controller {

		/**
		*@Route("/profile", name="profile")
		*/
		public function profile(Request $request){

			// Doctrine
			$em = $this->getDoctrine()->getManager();

			// Twig vars
			$twig = array();

			// Create user
			$user = $this->getuser();

			$twig['cart'] 		= $em->getRepository('AppBundle:User')->getCurrentCart($user->getId());
			$twig['cprodCount'] = $em->getRepository('AppBundle:Cart')->getProductsCount($twig['cart']->getId());
			$twig['totalPrice']	= $em->getRepository('AppBundle:Cart')->getTotalPrice($twig['cart']->getId());
			$twig['orders']		= $em->getRepository('AppBundle:Cart')->getUserConfirmedCarts($user);
			$twig['cartRepo']	= $em->getRepository('AppBundle:Cart');

			// User form
			$form = $this->createForm(RegisterForm::class, $user);
			$twig['form'] = $form->createView();

			$form->handleRequest($request);
			if($form->isSubmitted() && $form->isValid()) {

				// Encode user password
				if($form['password'] != null) {
					$encoder = $this->container->get('security.password_encoder');
					$encoded = $encoder->encodePassword($user, $form['password']->getData());
					$user->setPassword($encoded);
				}
				
				$em->persist($user);
				$em->flush();

				// Success message
				$this->addFlash('success', 'Updated!');
			}
			
			return $this->render('frontend/profile.htm', $twig);
		}

	}
?>