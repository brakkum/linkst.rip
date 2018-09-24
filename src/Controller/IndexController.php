<?php

namespace App\Controller;

use App\Entity\Link;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    /**
     * @Route("/", name="index")
     * @param Request $request
     * @return string
     */
    public function index(Request $request)
    {

        $link = new Link();

        $form = $this->createFormBuilder($link)
            ->setAction($this->generateUrl('newLink'))
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
            ->add("save", SubmitType::class, array(
                "label" => "New Link",
                "attr" => array(
                    "class" => "",
                )
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
        if (!$request->request->get("form")) {
            return $this->redirect("/");
        }

        $full_url = $request->request->get("form")["full_url"];
        $custom_slug = $request->request->get("form")["slug"];

        if (strlen($custom_slug) < 5) {
            return $this->redirect("/err=1");
        }

        $new_link = new Link();
        // set full_url
        $new_link->setFullUrl($full_url);

        // set slug
        if ($custom_slug) {
            // is custom slug unique?
            if ($this->slugAlreadyExists($custom_slug)) {
                return $this->redirect("/err=0");
            } else {
                $new_link->setSlug($custom_slug);
                $slug = $custom_slug;
            }
        } else {
            // generate random slug
            $random_slug = $this->getRandomSlug();
            while ($this->slugAlreadyExists($random_slug)) {
                $random_slug = $this->getRandomSlug();
            }
            $new_link->setSlug($random_slug);
            $slug = $random_slug;
        }
        $link = $request->getHttpHost() . "/" . $slug;

        // set domain and path
        list($domain, $path) = $this->getDomainAndPath($full_url);
        $new_link->setDomain($domain);
        $new_link->setPath($path);

        // save it to database
        $em = $this->getDoctrine()->getManager();
        $em->persist($new_link);
        $em->flush();

        // output link
        return $this->render('index/test.html.twig', [
            'slug' => $link,
        ]);
    }

    /**
     * @Route("/err={error_id}", name="errorDisplay", requirements={"error_id"="[0-1]"})
     * @param int $error_id
     * @return string
     */
    public function errorDisplay($error_id)
    {
        $errors = array(
            0 => "Hmm.. Looks like that slug is already taken.",
            1 => "That slug is too short.",
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

    /**
     * @param string $slug
     * @return boolean
     */
    public function slugAlreadyExists($slug)
    {
        /** @var \App\Repository\LinkRepository $link_repo */
        $link_repo = $this->getDoctrine()->getRepository(Link::class);
        /** @var \App\Entity\Link[] $link */
        $link = $link_repo->findBy(array("slug" => $slug));

        return count($link) != 0;
    }

    /**
     * @param int $length
     * @return string
     */
    public function getRandomSlug($length = 5)
    {
        $seed = str_split('abcdefghijklmnopqrstuvwxyz'
            .'ABCDEFGHIJKLMNOPQRSTUVWXYZ'
            .'0123456789_-~.');
        shuffle($seed);
        $rand = '';
        foreach (array_rand($seed, $length) as $k) {
            $rand .= $seed[$k];
        }
        return $rand;
    }

    /**
     * @param string $full_url
     * @return array
     */
    public function getDomainAndPath($full_url)
    {
        $regex = "/([-a-zA-Z0-9@:%._\+~#=]{2,256}\.[a-z]{2,6}\b)([-a-zA-Z0-9@:%_\+.~#?&\/=]*)/";
        preg_match($regex, $full_url,$matches);
        $domain = $matches[1];
        $domain = str_replace("www.", "", $domain);
        $path = $matches[2];
        if ($path == "") {
            $path = null;
        }
        return array($domain, $path);
    }

    /**
     * @Route("/api/check/{slug}", name="checkSlug")
     * @param string $slug
     * @return JsonResponse
     */
    public function isSlugAvailable($slug)
    {
        $success = true;
        $errors = [];

        if (strlen($slug) < 5) {
            $success = false;
            $errors[] = "Slug must be at least 5 characters.";
        }

        if (!preg_match("/^[a-zA-Z0-9-._~]{0,100}$/", $slug)) {
            $success = false;
            $errors[] = "Invalid slug.";
        }

        if ($success) {
            /** @var \App\Repository\LinkRepository $link_repo */
            $link_repo = $this->getDoctrine()->getRepository(Link::class);
            /** @var \App\Entity\Link[] $link */
            $link = $link_repo->findBy(array("slug" => $slug));
        } else {
            $link = 1;
        }

        return new JsonResponse([
            "success" => $success,
            "data" => [
                "slug" => $slug,
                "errors" => $errors,
                "slugAvailable" => (count($link) == 0)
            ]
        ]);
    }
}
