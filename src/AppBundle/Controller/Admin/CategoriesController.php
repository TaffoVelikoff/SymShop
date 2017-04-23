<?php
	namespace AppBundle\Controller\Admin;

	use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
	use Symfony\Bundle\FrameworkBundle\Controller\Controller;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\HttpFoundation\Request;
	use AppBundle\Form\CategoryForm;
	use AppBundle\Entity\Category;

	class CategoriesController extends Controller {

		/**
		*@Route("admin/categories", name="categories")
		*/
		public function listCategories(Request $request) {
			// Doctrine
			$em = $this->getDoctrine()->getManager();

			// Get all categories
			$categories = $em->getRepository('AppBundle:Category')->findBy([], ['ord' => 'DESC']);

			// Display template
			return $this->render('admin/categories.htm', ['categories' => $categories]);
		}

		/**
		*@Route("admin/category/add", name="addcategory")
		*/
		public function addCategory(Request $request) {
			// Doctrine
			$em = $this->getDoctrine()->getManager();

			// Create category
			$category = new Category();
			
			// User form
			$form = $this->createForm(CategoryForm::class, $category);

			$form->handleRequest($request);
			if($form->isSubmitted() && $form->isValid()) {
				
				if($form['promo']->getData() == null) {
					$category->setPromo(0);
				}

				$nextOrd = $category->nextFreeOrd();
				$category->setOrd($nextOrd);
				$em->persist($category);
				$em->flush();

				// Success message
				$this->addFlash('success', 'Succesfully created!');
				return $this->redirect('/admin/categories');
			}

			// Display template
			return $this->render('admin/category.htm', ['form' => $form->createView()]);
		}

		/**
		*@Route("admin/category/moveup/{id}")
		*/
		public function moveUp($id) {
			// Doctrine
			$em = $this->getDoctrine()->getManager();

			// Get category
			$cat = $em->getRepository('AppBundle:Category')->findOneBy(['id' => $id]);
			$currentOrd = $cat->getOrd();

			// Get next category
			$nextCat = $em->getRepository('AppBundle:Category')->findOneBy(['id' => $cat->nextOrd()]);
			$nextOrd = $nextCat->getOrd();
			
			// Swap places
			$nextCat->setOrd($currentOrd);
			$em->persist($nextCat);
			$em->flush();

			$cat->setOrd($nextOrd);
			$em->persist($cat);
			$em->flush();
			
			return $this->redirectToRoute('categories');
		}

		/**
		*@Route("admin/category/movedown/{id}")
		*/
		public function moveDown($id) {
			// Doctrine
			$em = $this->getDoctrine()->getManager();

			// Get category
			$cat = $em->getRepository('AppBundle:Category')->findOneBy(['id' => $id]);
			$currentOrd = $cat->getOrd();

			// Get next category
			$prevCat = $em->getRepository('AppBundle:Category')->findOneBy(['id' => $cat->prevOrd()]);
			$prevOrd = $prevCat->getOrd();
			
			// Swap places
			$prevCat->setOrd($currentOrd);
			$em->persist($prevCat);
			$em->flush();

			$cat->setOrd($prevOrd);
			$em->persist($cat);
			$em->flush();
			
			return $this->redirectToRoute('categories');
		}

		/**
		*@Route("admin/category/delete/{id}")
		*/
		public function deleteCategory($id) {

			// Doctrine
			$em = $this->getDoctrine()->getManager();

			// Get the category
			$category = $em->getRepository('AppBundle:Category')->findOneBy(['id' => $id]);

			$em->remove($category);
	    	$em->flush();

			// Redirect users
			return $this->redirect('/admin/categories');
		}

		/**
		*@Route("admin/category/{id}")
		*/
		public function editCategory($id, Request $request) {
			// Doctrine
			$em = $this->getDoctrine()->getManager();

			// Create category
			$category = $em->getRepository('AppBundle:Category')->findOneBy(['id' => $id]);
			
			// User form
			$form = $this->createForm(CategoryForm::class, $category);

			$form->handleRequest($request);
			if($form->isSubmitted() && $form->isValid()) {
				
				if($form['promo']->getData() == null) {
					$category->setPromo(0);
				}

				$nextOrd = $category->nextFreeOrd();
				$category->setOrd($nextOrd);
				$em->persist($category);
				$em->flush();

				// Success message
				$this->addFlash('success', 'Succesfully updated!');
				return $this->redirect('/admin/category/'.$id);
			}

			// Display template
			return $this->render('admin/category.htm', ['form' => $form->createView()]);
		}

	}
?>