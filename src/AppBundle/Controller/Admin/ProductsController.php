<?php
	namespace AppBundle\Controller\Admin;

	use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
	use Symfony\Bundle\FrameworkBundle\Controller\Controller;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\HttpFoundation\Request;
	use AppBundle\Form\ProductForm;
	use AppBundle\Entity\Product;
	use Symfony\Component\HttpFoundation\File\File;

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

			// On top/on bottom products
			$onTop = $em->getRepository('AppBundle:Product')->onTop($cat);
			$onBottom = $em->getRepository('AppBundle:Product')->onBottom($cat);

			// Display template
			return $this->render('admin/products.htm', [
				'cat' => $cat, 'products' => $products,
				'onTop' => $onTop, 
				'onBottom' => $onBottom
			]);
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

				if($form['photoform']->getData() != null) {

		            $file = $product->getPhotoform();

		            // Generate a unique name for the file before saving it
		            $fileName = md5(uniqid()).'.'.$file->guessExtension();

		            $file->move(
		                $this->getParameter('prods_directory'),
		                $fileName
		            );

		            $product->setPhoto($fileName);
		        }

				$cat = $em->getRepository('AppBundle:Category')->findOneBy(['id' => $request->get('category')]);
				$product->setCategory($cat);
				$nextOrd = $em->getRepository('AppBundle:Product')->nextFreeOrd();
				$product->setOrd($nextOrd);
				$em->persist($product);
				$em->flush();

				// Success message
				$this->addFlash('success', 'Succesfully created product!');
				return $this->redirect('/admin/products/'.$category->getId());
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
			$nextProdId = $em->getRepository('AppBundle:Product')->nextOrd($prod->getId());
			$nextProd = $em->getRepository('AppBundle:Product')->findOneBy(['id' => $nextProdId]);
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

			// Get previous product
			$prevProdId = $em->getRepository('AppBundle:Product')->prevOrd($prod->getId());
			$prevProd = $em->getRepository('AppBundle:Product')->findOneBy(['id' => $prevProdId]);
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

			// Get product's photo
			$photo = $product->getPhoto();

			// Delete prod photo
			if($photo != null) {
				unlink($this->getParameter('prods_directory').'/'.$photo);
			}

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

				// Attach photo
				if($form['photoform']->getData() != null) {

					if(strlen($product->getPhoto()) > 0) {
						unlink($this->getParameter('prods_directory').'/'.$product->getPhoto());
					}

		            $file = $product->getPhotoform();

		            // Generate a unique name for the file before saving it
		            $fileName = md5(uniqid()).'.'.$file->guessExtension();

		            $file->move(
		                $this->getParameter('prods_directory'),
		                $fileName
		            );

		            $product->setPhoto($fileName);
		        }

				$product->setCategory($cat);

				$em->persist($product);
				$em->flush();

				// Success message
				$this->addFlash('success', 'Succesfully edited product!');
				return $this->redirect('/admin/products/'.$cat->getId());
			}

			// Display template
			return $this->render('admin/product.htm', [
				'prod'			=> $product,
				'category'		=> $category,
				'categories' 	=> $categories,
				'form' 			=> $form->createView(),
			]);
		}

		
	}
?>