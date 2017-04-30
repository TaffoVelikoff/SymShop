<?php
	namespace AppBundle\Controller\Admin;

	use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
	use Symfony\Bundle\FrameworkBundle\Controller\Controller;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\HttpFoundation\Request;

	class OrderController extends Controller {

		/**
		*@Route("/admin/order/{id}")
		*/
		public function viewOrder($id, Request $request) {

			// Check for editor
			$roles = $this->get('security.token_storage')->getToken()->getUser()->getRoles();
			if(!in_array('ROLE_ADMIN', $roles)) {
				return $this->redirectToRoute('dashboard');
			}
			
			// Doctrine
			$em = $this->getDoctrine()->getManager();

			// Get the cart
			$cart = $em->getRepository('AppBundle:Cart')->findOneBy(['id' => $id]);
			
			// Get user
			$user = $cart->getUser();

			// Get cart products
			$cartProds = $cart->getProducts();

			// Display template
			return $this->render('admin/order.htm', [
				'cart'		=> $cart,
				'user'		=> $user,
				'cartProds'	=> $cartProds,
				'cartRepo'	=> $em->getRepository('AppBundle:Cart')
			]);

		}

		/**
		*@Route("/admin/remove_cart/{id}")
		*/
		public function removeCart($id, Request $request) {

			// Check for editor
			$roles = $this->get('security.token_storage')->getToken()->getUser()->getRoles();
			if(!in_array('ROLE_ADMIN', $roles)) {
				return $this->redirectToRoute('dashboard');
			}

			// Doctrine
			$em = $this->getDoctrine()->getManager();

			// Get cart
			$cart = $em->getRepository('AppBundle:Cart')->findOneBy(['id' => $id]);

			// User
			$user = $cart->getUser();

			// Cart products
			$prods = $cart->getProducts();

			foreach($prods as $prod) {
				// Product 
				$product = $prod->getProduct();

				// Restore quantity
				$curQty = $product->getQuantity();
				$product->setQuantity($curQty + $prod->getQuantity());
				$em->persist($product);
				$em->flush();

				// Restore cash
				$curCash = $user->getCash();
				$user->setCash($curCash + $prod->getPrice());
				$em->persist($product);
				$em->flush();
				
				// Remove cart product
				$em->remove($prod);
				$em->flush();
			}

			$em->remove($cart);
			$em->flush();

			// Redirect bavk
			return $this->redirect($request->headers->get('referer'));
		}

		
		/**
		*@Route("/admin/remove_from_cart/{id}/{user}/{prod_id}")
		*/
		public function removeCartProd($id, $user, $prod_id, Request $request) {

			// Doctrine
			$em = $this->getDoctrine()->getManager();

			// Get cart prod
			$cprod = $em->getRepository('AppBundle:CartProduct')->findOneBy(['id' => $id]);

			// Get product
			$product = $em->getRepository('AppBundle:Product')->findOneBy(['id' => $prod_id]);

			// Get user
			$usr = $em->getRepository('AppBundle:User')->findOneBy(['id' => $user]);
			
			// Restore cash
			$curCash = $usr->getCash();
			$usr->setCash($curCash + $cprod->getPrice());
			$em->persist($usr);
			$em->flush();

			// Restore quantity
			$curQty = $product->getQuantity();
			$product->setQuantity($curQty + $cprod->getQuantity());
			$em->persist($product);
			$em->flush();

			// Remove cart product
			$em->remove($cprod);
			$em->flush();

			// Redirect bavk
			return $this->redirect($request->headers->get('referer'));
		}

		/**
		*@Route("/admin/confirm/{id}")
		*/
		public function confirm($id, Request $request) {

			// Doctrine
			$em = $this->getDoctrine()->getManager();

			// Get cart
			$cart = $em->getRepository('AppBundle:Cart')->findOneBy(['id' => $id]);

			$cart->setStatus(1);
			$em->persist($cart);
			$em->flush();

			// Redirect bavk
			return $this->redirect($request->headers->get('referer'));
		}

	}
?>