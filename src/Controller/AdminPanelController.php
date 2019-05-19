<?php

namespace App\Controller;

use App\Aws\S3Manager;
use App\Entity\Category;
use App\Entity\Product;
use App\Entity\Tag;
use App\Entity\User;
use App\Form\AssignCategoryType;
use App\Form\CategoryType;
use App\Form\ProductType;
use App\Form\TagType;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin")
 */
class AdminPanelController extends AbstractController
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("")
     */
    public function index()
    {
        return $this->render('admin_panel/dashboard.html.twig', [
            'usersCount' => count($this->getDoctrine()->getRepository(User::class)->findAll()),
            'productsCount' => count($this->getDoctrine()->getRepository(Product::class)->findAll()),
            'categoriesCount' => count($this->getDoctrine()->getRepository(Category::class)->findAll()),
            'tagsCount' => count($this->getDoctrine()->getRepository(Tag::class)->findAll()),
            'lastUsers' => $this->getDoctrine()->getRepository(User::class)->findLast($this->getParameter('last_admin')),
            'lastCategories' => $this->getDoctrine()->getRepository(Category::class)->findLast($this->getParameter('last_admin')),
            'lastTags' => $this->getDoctrine()->getRepository(Tag::class)->findLast($this->getParameter('last_admin')),
            'lastProducts' => $this->getDoctrine()->getRepository(Product::class)->findLast($this->getParameter('last_admin')),
        ]);
    }

    /**
     * @param Request $request
     * @param PaginatorInterface $paginator
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/users")
     */
    public function usersList(Request $request, PaginatorInterface $paginator)
    {
        $users = $this->getDoctrine()->getRepository(User::class)->getAll();

        return $this->render('admin_panel/usersList.html.twig', [
            'users' => $paginator->paginate(
                $users,
                $request->query->getInt('page', 1),
                $this->getParameter('page_range')
            ),
        ]);
    }

    /**
     * @param User $user
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @Route("/users/{id}/make-manager")
     */
    public function makeManager(User $user)
    {
        $user->setRoles([User::ROLE_ADMIN_MANAGER]);
        $this->getDoctrine()->getManager()->flush();

        $this->addFlash('notice', 'Made by manager!');

        return $this->redirectToRoute('app_adminpanel_userslist');
    }

    /**
     * @param User $user
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @Route("/users/{id}/make-user")
     */
    public function makeUser(User $user)
    {
        $user->setRoles([User::ROLE_USER]);
        $user->getCategory()->clear();
        $this->getDoctrine()->getManager()->flush();

        $this->addFlash('notice', 'Made by user!');

        return $this->redirectToRoute('app_adminpanel_userslist');
    }

    /**
     * @param Request $request
     * @param User $user
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/users/{id}/assign-categories")
     */
    public function assignCategories(Request $request, User $user)
    {
        $form = $this->createForm(AssignCategoryType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('notice', 'Сategories are assigned!');

            return $this->redirectToRoute('app_adminpanel_userslist');
        }

        return $this->render('admin_panel/assignCategories.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     * @param PaginatorInterface $paginator
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/products")
     */
    public function productsList(Request $request, PaginatorInterface $paginator)
    {
        $products = $this->getDoctrine()->getRepository(Product::class)->getAll();

        return $this->render('admin_panel/productsList.html.twig', [
            'products' => $paginator->paginate(
                $products,
                $request->query->getInt('page', 1),
                $this->getParameter('page_range')
            ),
        ]);
    }

    /**
     * @param Request $request
     *
     * @param S3Manager $s3Manager
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @Route("/products/new")
     */
    public function newProduct(Request $request, S3Manager $s3Manager)
    {
        $product = new Product();
        $product->setManager($this->getUser());

        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($image = $product->getImage()) {
                $imageFile = $this->file($image->getFile())->getFile();
                $result = $s3Manager->uploadPicture(fopen($imageFile, 'rb'), $imageFile->guessExtension());
                $product->getImage()->setUrl($result['url']);
                $product->getImage()->setS3Key($result['key']);
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($product);
            $em->flush();

            $this->addFlash('notice', 'Product created!');

            return $this->redirectToRoute('app_adminpanel_productslist');
        }

        return $this->render('product/newEdit.html.twig', [
            'form' => $form->createView(),
            'title' => 'Create',
            'image' => $product->getImage(),
        ]);
    }

    /**
     * @param Request $request
     * @param Product $product
     * @param S3Manager $s3Manager
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @Route("/products/{id}/edit")
     */
    public function editProduct(Request $request, Product $product, S3Manager $s3Manager)
    {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($image = $product->getImage()) {
                if ($image->getS3Key()) {
                    $s3Manager->deletePicture($image->getS3Key());
                }
                $imageFile = $this->file($image->getFile())->getFile();
                $result = $s3Manager->uploadPicture(fopen($imageFile, 'rb'), $imageFile->guessExtension());
                $product->getImage()->setUrl($result['url']);
                $product->getImage()->setS3Key($result['key']);
            }
            $em = $this->getDoctrine()->getManager();
            $em->persist($product);
            $em->flush();

            $this->addFlash('notice', 'Product updated!');

            return $this->redirectToRoute('app_default_index');
        }

        return $this->render('product/newEdit.html.twig', [
            'form' => $form->createView(),
            'title' => 'Edit',
            'id' => $product->getId(),
            'image' => $product->getImage(),
        ]);
    }

    /**
     * @param Product $product
     * @param S3Manager $s3Manager
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @throws \Exception
     *
     * @Route("/products/{id}/delete")
     */
    public function deleteProduct(Product $product, S3Manager $s3Manager)
    {
        try {
            if ($product->getImage()) {
                $s3Manager->deletePicture($product->getImage()->getS3Key());
            }

            $em = $this->getDoctrine()->getManager();
            $em->remove($product);
            $em->flush();

            $this->addFlash('notice', 'Product deleted!');

            return $this->redirectToRoute('app_adminpanel_productslist');
        } catch (Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @param PaginatorInterface $paginator
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/categories")
     */
    public function categoriesList(Request $request, PaginatorInterface $paginator)
    {
        $categories = $this->getDoctrine()->getRepository(Category::class)->getAll();

        return $this->render('admin_panel/categoriesList.html.twig', [
            'categories' => $paginator->paginate(
                $categories,
                $request->query->getInt('page', 1),
                $this->getParameter('page_range')
            ),
        ]);
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @Route("/categories/new")
     */
    public function newCategory(Request $request)
    {
        $category = new Category();

        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($category);
            $em->flush();

            $this->addFlash('notice', 'Category created!');

            return $this->redirectToRoute('app_adminpanel_categorieslist');
        }

        return $this->render('category/newEdit.html.twig', [
            'form' => $form->createView(),
            'title' => 'Create'
        ]);
    }

    /**
     * @param Request $request
     * @param Category $category
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @Route("/categories/{id}/edit")
     */
    public function editCategory(Request $request, Category $category)
    {
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($category);
            $em->flush();

            $this->addFlash('notice', 'Category updated!');

            return $this->redirectToRoute('app_adminpanel_categorieslist');
        }

        return $this->render('category/newEdit.html.twig', [
            'form' => $form->createView(),
            'title' => 'Edit',
        ]);
    }

    /**
     * @param Category $category
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @Route("/categories/{id}/delete")
     */
    public function deleteCategory(Category $category)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($category);
        $em->flush();

        $this->addFlash('notice', 'Category deleted!');

        return $this->redirectToRoute('app_adminpanel_categorieslist');
    }

    /**
     * @param Request $request
     * @param PaginatorInterface $paginator
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/tags")
     */
    public function tagsList(Request $request, PaginatorInterface $paginator)
    {
        $tags = $this->getDoctrine()->getRepository(Tag::class)->getAll();

        return $this->render('admin_panel/tagsList.html.twig', [
            'tags' => $paginator->paginate(
                $tags,
                $request->query->getInt('page', 1),
                $this->getParameter('page_range')
            ),
        ]);
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @Route("/tags/new")
     */
    public function newTag(Request $request)
    {
        $tag = new Tag();

        $form = $this->createForm(TagType::class, $tag);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($tag);
            $em->flush();

            $this->addFlash('notice', 'Tag created!');

            return $this->redirectToRoute('app_adminpanel_tagslist');
        }

        return $this->render('tag/newEdit.html.twig', [
            'form' => $form->createView(),
            'title' => 'Create'
        ]);
    }

    /**
     * @param Request $request
     * @param Tag $tag
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @Route("/tags/{id}/edit")
     */
    public function editTag(Request $request, Tag $tag)
    {
        $form = $this->createForm(TagType::class, $tag);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($tag);
            $em->flush();

            $this->addFlash('notice', 'Tag updated!');

            return $this->redirectToRoute('app_adminpanel_tagslist');
        }

        return $this->render('tag/newEdit.html.twig', [
            'form' => $form->createView(),
            'title' => 'Edit',
        ]);
    }

    /**
     * @param Tag $tag
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @Route("/tags/{id}/delete")
     */
    public function deleteTag(Tag $tag)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($tag);
        $em->flush();

        $this->addFlash('notice', 'Tag deleted!');

        return $this->redirectToRoute('app_adminpanel_tagslist');
    }
}
