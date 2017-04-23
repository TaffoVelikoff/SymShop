<?php
	namespace AppBundle\Controller\Admin;

	use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
	use Symfony\Bundle\FrameworkBundle\Controller\Controller;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\HttpFoundation\Request;
	use AppBundle\Form\ProfileForm;
	use AppBundle\Entity\User;
	use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

	class UsersController extends Controller {

		/**
		*@Route("admin/users", name="users")
		*/
		public function listUsers(Request $request) {

			// Doctrine
			$em = $this->getDoctrine()->getManager();

			// Check for editor
			$roles = $this->get('security.token_storage')->getToken()->getUser()->getRoles();
			if(!in_array('ROLE_ADMIN', $roles)) {
				return $this->redirectToRoute('dashboard');
			}

			// Get all users
			$users = $em->getRepository('AppBundle:User')->findAll();

			// Display template
			return $this->render('admin/users.htm', ['users' => $users]);
		}

		/**
		*@Route("admin/user/delete/{id}")
		*/
		public function deleteUser($id) {

			// Doctrine
			$em = $this->getDoctrine()->getManager();

			// Check for editor
			$roles = $this->get('security.token_storage')->getToken()->getUser()->getRoles();
			if(!in_array('ROLE_ADMIN', $roles)) {
				return $this->redirectToRoute('dashboard');
			}

			// Get the user
			$user = $em->getRepository('AppBundle:User')->findOneBy(['id' => $id]);

			$roles = $user->getRoles();

			if(in_array('ROLE_ADMIN', $roles)) {
				// Success message
				$this->addFlash('error', 'Can not delete an admin!');
			} else {
				// Delete the user
				$em->remove($user);
	    		$em->flush();
			}

			// Redirect users
			return $this->redirect('/admin/users');
		}

		/**
		*@Route("admin/user/add", name="adduser")
		*/
		public function addUser(Request $request) {

			// Doctrine
			$em = $this->getDoctrine()->getManager();

			// Check for editor
			$roles = $this->get('security.token_storage')->getToken()->getUser()->getRoles();
			if(!in_array('ROLE_ADMIN', $roles)) {
				return $this->redirectToRoute('dashboard');
			}

			// Create user
			$user = new User();
			
			// User form
			$form = $this->createForm(ProfileForm::class, $user);

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
				$this->addFlash('success', 'Succesfully updated!');
				return $this->redirect('/admin/users');
			}

			// Display template
			return $this->render('admin/user.htm', ['form' => $form->createView()]);
		}

		/**
		*@Route("admin/user/{id}")
		*/
		public function profile($id, Request $request) {
			// Doctrine
			$em = $this->getDoctrine()->getManager();

			// Get user
			$user = $em->getRepository('AppBundle:User')->findOneBy(['id' => $id]);

			// Check for editor
			$roles = $this->get('security.token_storage')->getToken()->getUser()->getRoles();
			$curUser = $this->get('security.token_storage')->getToken()->getUser()->getId();
			if(!in_array('ROLE_ADMIN', $roles) && $curUser != $id) {
				return $this->redirectToRoute('dashboard');
			}

			// User form
			$form = $this->createForm(ProfileForm::class, $user);

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
				$this->addFlash('success', 'Succesfully updated!');
				return $this->redirect('/admin/user/'.$id);
			}

			// Display template
			return $this->render('admin/user.htm', ['form' => $form->createView()]);
		}

	}
?>