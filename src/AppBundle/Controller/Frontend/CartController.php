<?php
	namespace AppBundle\Controller\Frontend;

	use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
	use Symfony\Bundle\FrameworkBundle\Controller\Controller;
	use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\Response;
	use AppBundle\Entity\CartProduct;
	use AppBundle\Entity\Product;

	class CartController extends Controller {

		/**
		*@Route("/cart", name="cart")
		*/
		public function viewCart(){
			
			// Doctrine
			$em = $this->getDoctrine()->getManager();

			// Twig
			$twig = array();

			// Get user and cart
			$cart = $em->getRepository('AppBundle:User')->getCurrentCart($this->getUser()->getId());
			$twig['cart'] 		= $cart;
			$twig['cprodCount'] = $em->getRepository('AppBundle:Cart')->getProductsCount($twig['cart']->getId());
			$twig['totalPrice']	= $em->getRepository('AppBundle:Cart')->getTotalPrice($twig['cart']->getId());

			// Cart products
			$prods = $cart->getProducts();
			$twig['cartProds']	= $prods;

			return $this->render('frontend/cart.htm', $twig);
		}

		/**
		*@Route("/add/{id}", name="add")
		*/
		public function addSingleProduct($id, Request $request){

			if(!$this->getUser()) {
				return $this->redirectToRoute('auth_login');
			}

			// Doctrine
			$em = $this->getDoctrine()->getManager();

			// Get cart
			$cart = $em->getRepository('AppBundle:User')->getCurrentCart($this->getUser()->getId());

			// Get the product
			$prod = $em->getRepository('AppBundle:Product')->findOneBy(['id' => $id]);

			// Cart product
			$cprod = new CartProduct();

			$cprod->setCart($cart);
			$cprod->setQuantity(1);
			$cprod->setProduct($prod);
			$cprod->setPrice($prod->getActualPrice());

			$em->persist($cprod);
			$em->flush();

			// Change quantity
			$curQty = $prod->getQuantity();
			$prod->setQuantity($curQty - 1);

			$em->persist($cprod);
			$em->flush();

			// Redirect back
			return $this->redirect($request->headers->get('referer'));
		}

		/**
		*@Route("/add_to_cart", name="add-to-cart")
		*@Method({"POST"})
		*/
		public function addProduct(Request $request) {

			if(!$this->getUser()) {
				return $this->redirectToRoute('auth_login');
			}

			// Doctrine
			$em = $this->getDoctrine()->getManager();

			// Get the product
			$prod = $em->getRepository('AppBundle:Product')->findOneBy(['id' => $request->get('product')]);

			// Get cart
			$cart = $em->getRepository('AppBundle:User')->getCurrentCart($this->getUser()->getId());

			// Cart product
			$cprod = new CartProduct();

			$cprod->setCart($cart);
			$cprod->setQuantity($request->get('quantity'));
			$cprod->setProduct($prod);
			$cprod->setPrice($prod->getActualPrice());

			$em->persist($cprod);
			$em->flush();

			// Change quantity
			$curQty = $prod->getQuantity();
			$prod->setQuantity($curQty - $request->get('quantity'));

			$em->persist($cprod);
			$em->flush();

			// Redirect back
			return $this->redirect($request->headers->get('referer'));
		}

		/**
		*@Route("/checkout", name="checkout"))
		*/
		public function checkout(Request $request) {

			// Doctrine
			$em = $this->getDoctrine()->getManager();

			// Twig vars
			$twig = array();

			// Get user and cart
			$cart = $em->getRepository('AppBundle:User')->getCurrentCart($this->getUser()->getId());
			$twig['cart'] 		= $cart;
			$twig['cprodCount'] = $em->getRepository('AppBundle:Cart')->getProductsCount($twig['cart']->getId());
			$twig['totalPrice']	= $em->getRepository('AppBundle:Cart')->getTotalPrice($twig['cart']->getId());

			return $this->render('frontend/checkout.htm', $twig);
		}

		/**
		*@Route("/process_order", name="process_order"))
		*@Method({"POST"})
		*/
		public function processOrder(Request $request) {

			// Doctrine
			$em = $this->getDoctrine()->getManager();

			// Get user
			$user = $this->getUser();

			// Get cart
			$cart = $em->getRepository('AppBundle:Cart')->findOneBy(['id' => $request->get('cart')]);

			// Confirm cart
			$cart->setDeliveryPerson($request->get('deliveryPerson'));
			$cart->setDeliveryAddress($request->get('deliveryAddress'));
			$cart->setDeliveryPhone($request->get('deliveryPhone'));
			$cart->setDeliveryEmail($request->get('deliveryEmail'));
			$cart->setConfirmed(time());

			$em->persist($cart);
			$em->flush();

			// Cart total price
			$totalPrice = $em->getRepository('AppBundle:Cart')->getTotalPrice($cart->getId());

			// Remove cahs from user
			$cash = $user->getCash();
			$user->setCash($cash-$totalPrice);

			$em->persist($user);
			$em->flush();

			// Create a new empty cart
			$em->getRepository('AppBundle:User')->getCurrentCart($user->getId());

			// Redirect back
			$this->addFlash('success', 'Thank you for the order!');
			return $this->redirectToRoute('ok');
		}

		/**
		*@Route("/ok", name="ok"))
		*/
		public function ok(Request $request) {
			return $this->redirectToRoute('home');
		}

	}
?>