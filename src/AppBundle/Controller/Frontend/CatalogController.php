<?php
	namespace AppBundle\Controller\Frontend;

	use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
	use Symfony\Bundle\FrameworkBundle\Controller\Controller;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\HttpFoundation\Request;

	class CatalogController extends Controller {

		/**
		*@Route("/category/{id}")
		*/
		public function listProducts($id){
			
			// Doctrine
			$em = $this->getDoctrine()->getManager();

			// Twig
			$twig = array();

			// Get user and cart
			if($this->getUser()) {
				$twig['cart'] 		= $em->getRepository('AppBundle:User')->getCurrentCart($this->getUser()->getId());
				$twig['cprodCount'] = $em->getRepository('AppBundle:Cart')->getProductsCount($twig['cart']->getId());
				$twig['totalPrice']	= $em->getRepository('AppBundle:Cart')->getTotalPrice($twig['cart']->getId());
			}

			// Get category
			$category = $em->getRepository('AppBundle:Category')->findOneBy(['id' => $id]);
			$twig['category'] = $category;

			// Get Products
			$prods = $category->getProducts();
			$twig['prods'] = $prods;
			$twig['pcount'] = count($prods);

			return $this->render('frontend/category.htm', $twig);
		}

		/**
		*@Route("/product/{id}")
		*/
		public function productDetails($id){
			
			// Doctrine
			$em = $this->getDoctrine()->getManager();

			// Twig
			$twig = array();

			// Get user and cart
			if($this->getUser()) {
				$twig['cart'] 		= $em->getRepository('AppBundle:User')->getCurrentCart($this->getUser()->getId());
				$twig['cprodCount'] = $em->getRepository('AppBundle:Cart')->getProductsCount($twig['cart']->getId());
				$twig['totalPrice']	= $em->getRepository('AppBundle:Cart')->getTotalPrice($twig['cart']->getId());
			}

			// Get product
			$product = $em->getRepository('AppBundle:Product')->findOneBy(['id' => $id]);
			$twig['prod'] = $product;

			// Get related products
			$relatedProducts = $em->getRepository('AppBundle:Product')->getRelated(5, $product->getCategory());
			$twig['related'] = $relatedProducts;

			// Category
			$twig['category'] = $product->getCategory();

			return $this->render('frontend/product.htm', $twig);
		}

		/**
		*@Route("/remove_from_cart/{id}")
		*/
		public function removeProduct($id, Request $request){
			
			// Doctrine
			$em = $this->getDoctrine()->getManager();

			// Get product
			$cartProd = $em->getRepository('AppBundle:CartProduct')->findOneBy(['id' => $id]);
			$product = $cartProd->getProduct();
			
			// Get current quantity
			$curQty = $product->getQuantity(); 

			// Set quantity back
			$product->setQuantity($curQty + $cartProd->getQuantity());
			$em->persist($product);
			$em->flush();

			// Remove cart product
			$em->remove($cartProd);
			$em->flush();

			// Redirect back
			return $this->redirect($request->headers->get('referer'));
		}

	}
?>