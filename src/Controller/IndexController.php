<?php

namespace App\Controller;

use App\Entity\Link;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    /**
     * @Route("/", name="index")
     * @return string
     */
    public function index()
    {

        $link = new Link();

        $form = $this->createFormBuilder($link)
            ->add("full_url", TextType::class, array(
                "label" => false,
                "required" => true,
                "attr" => array(
                    "class" => "",
                    "maxlength" => "255",
                    "placeholder" => "URL",
                ),
            ))
            ->add("slug", TextType::class, array(
                "label" => false,
                "required" => false,
                "attr" => array(
                    "class" => "",
                    "maxlength" => "100",
                    "placeholder" => "Custom Slug",
                ),
            ))
            ->getForm();

        return $this->render('index/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/new", name="newLink")
     * @param Request $request
     * @return string
     */
    public function newLink(Request $request)
    {
        $link = new Link();
        $form = $this->createFormBuilder($link)
            ->getForm();

        $form->handleRequest($request);

        $task = $form->getData();

        // check that custom slug doesn't exist
        $custom_slug = $request->request->get("form")["slug"];
        if ($custom_slug) {
            /** @var \App\Repository\LinkRepository $link_repo */
            $link_repo = $this->getDoctrine()->getRepository(Link::class);
            /** @var \App\Entity\Link $link */
            $link = $link_repo->findOneBy(array("slug" => $custom_slug));

            if ($link) {
                return $this->redirect("/err?1");
            }
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($task);
            $em->flush();

            return $this->redirect("/");
        } else {
            return $this->redirect("/");
        }
    }

    /**
     * @Route("/err{error_id}", name="errorDisplay", requirements={"error_id"="[0]"})
     * @param int $error_id
     * @return string
     */
    public function errorDisplay($error_id)
    {
        $errors = array(
            0 => "zero",
        );

        return $this->render('index/error.html.twig', [
            'error' => $errors[$error_id],
        ]);
    }

    /**
     * @Route("/{slug}", name="redirectSlug", requirements={"slug"="[a-zA-Z0-9-._~]{5,100}"})
     * @param string $slug
     * @return string
     */
    public function redirectSlug($slug)
    {
        /** @var \App\Repository\LinkRepository $link_repo */
        $link_repo = $this->getDoctrine()->getRepository(Link::class);
        /** @var \App\Entity\Link $link */
        $link = $link_repo->findOneBy(array("slug" => $slug));

        return $this->render('index/test.html.twig', [
            'slug' => $link,
        ]);
    }

}
