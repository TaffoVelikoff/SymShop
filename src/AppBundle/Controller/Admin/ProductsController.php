<?php
	namespace AppBundle\Controller\Admin;

	use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
	use Symfony\Bundle\FrameworkBundle\Controller\Controller;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\HttpFoundation\Request;
	use AppBundle\Form\ProductForm;
	use AppBundle\Entity\Product;

	class ProductsController extends Controller {

		/**
		*@Route("admin/products/{catid}")
		*/
		public function listProducts($catid, Request $request) {
			// Doctrine
			$em = $this->getDoctrine()->getManager();

			// Get category products
			$cat = $em->getRepository('AppBundle:Category')->findOneBy(['id' => $catid]);
			$products = $cat->getProducts();

			// Display template
			return $this->render('admin/products.htm', ['cat' => $cat, 'products' => $products]);
		}

		/**
		*@Route("admin/addproduct/{catid}", name="products")
		*/
		public function addproduct($catid, Request $request) {
			// Doctrine
			$em = $this->getDoctrine()->getManager();

			// Get the default category
			$category = $em->getRepository('AppBundle:Category')->findOneBy(['id' => $catid]);

			// Get all categories
			$categories = $em->getRepository('AppBundle:Category')->findBy([], ['ord' => 'DESC']);

			// New product
			$product = new Product();

			// User form
			$form = $this->createForm(ProductForm::class, $product);

			$form->handleRequest($request);

			if($form->isSubmitted() && $form->isValid()) {

				if($form['promo']->getData() == null) {
					$product->setPromo(0);
				}

				$cat = $em->getRepository('AppBundle:Category')->findOneBy(['id' => $request->get('category')]);
				$product->setCategory($cat);
				$nextOrd = $product->nextFreeOrd();
				$product->setOrd($nextOrd);
				$em->persist($product);
				$em->flush();

				// Success message
				$this->addFlash('success', 'Succesfully created product!');
				return $this->redirect('/admin/categories');
			}

			// Display template
			return $this->render('admin/product.htm', [
				'category' 		=> $category,
				'categories' 	=> $categories,
				'form' 			=> $form->createView(),
			]);
		}

		/**
		*@Route("admin/product/moveup/{id}")
		*/
		public function moveUp($id, Request $request) {

			// Doctrine
			$em = $this->getDoctrine()->getManager();

			// Get product
			$prod = $em->getRepository('AppBundle:Product')->findOneBy(['id' => $id]);
			$currentOrd = $prod->getOrd();

			// Get next product
			$nextProd = $em->getRepository('AppBundle:Product')->findOneBy(['id' => $prod->nextOrd()]);
			$nextOrd = $nextProd->getOrd();
			
			// Swap places
			$nextProd->setOrd($currentOrd);
			$em->persist($nextProd);
			$em->flush();

			$prod->setOrd($nextOrd);
			$em->persist($prod);
			$em->flush();
			
			return $this->redirect($request->headers->get('referer'));
		}

		/**
		*@Route("admin/product/movedown/{id}")
		*/
		public function moveDown($id, Request $request) {
			// Doctrine
			$em = $this->getDoctrine()->getManager();

			// Get product
			$prod = $em->getRepository('AppBundle:Product')->findOneBy(['id' => $id]);
			$currentOrd = $prod->getOrd();

			// Get next product
			$prevProd = $em->getRepository('AppBundle:Product')->findOneBy(['id' => $prod->prevOrd()]);
			$prevOrd = $prevProd->getOrd();
			
			// Swap places
			$prevProd->setOrd($currentOrd);
			$em->persist($prevProd);
			$em->flush();

			$prod->setOrd($prevOrd);
			$em->persist($prod);
			$em->flush();
			
			return $this->redirect($request->headers->get('referer'));
		}

		/**
		*@Route("admin/product/delete/{id}")
		*/
		public function deleteProduct($id, Request $request) {

			// Doctrine
			$em = $this->getDoctrine()->getManager();

			// Get the product
			$product = $em->getRepository('AppBundle:Product')->findOneBy(['id' => $id]);

			$em->remove($product);
	    	$em->flush();

			// Redirect users
			return $this->redirect($request->headers->get('referer'));
		}

		/**
		*@Route("admin/product/{id}")
		*/
		public function editProduct($id, Request $request) {
			// Doctrine
			$em = $this->getDoctrine()->getManager();

			// Get all categories
			$categories = $em->getRepository('AppBundle:Category')->findBy([], ['ord' => 'DESC']);

			// New product
			$product = $em->getRepository('AppBundle:Product')->findOneBy(['id' => $id]);

			$category = $product->getCategory();

			// User form
			$form = $this->createForm(ProductForm::class, $product);

			$form->handleRequest($request);

			if($form->isSubmitted() && $form->isValid()) {

				if($form['promo']->getData() == null) {
					$product->setPromo(0);
				}

				$cat = $em->getRepository('AppBundle:Category')->findOneBy(['id' => $request->get('category')]);
				$product->setCategory($cat);
				$nextOrd = $product->nextFreeOrd();
				$product->setOrd($nextOrd);
				$em->persist($product);
				$em->flush();

				// Success message
				$this->addFlash('success', 'Succesfully created product!');
				return $this->redirect('/admin/categories');
			}

			// Display template
			return $this->render('admin/product.htm', [
				'category'		=> $category,
				'categories' 	=> $categories,
				'form' 			=> $form->createView(),
			]);
		}

		
	}
?>