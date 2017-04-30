<?php
	namespace AppBundle\Controller\Admin;

	use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
	use Symfony\Bundle\FrameworkBundle\Controller\Controller;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\HttpFoundation\Request;

	class OrderController extends Controller {

		/**
		*@Route("/admin/remove_cart/{id}")
		*/
		public function removeCart($id, Request $request) {

			// Doctrine
			$em = $this->getDoctrine()->getManager();

			// Get all confirmed carts
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

	}
?>