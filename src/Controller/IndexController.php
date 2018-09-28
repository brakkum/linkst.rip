<?php

namespace App\Controller;

use App\Entity\Link;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
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
        $form = $this->getIndexForm();

        return $this->render('index.html.twig', array(
            "form" => $form->createView(),
        ));
    }

    /**
     * @Route("/link", name="newLink")
     * @param Request $request
     * @return string
     */
    public function newLink(Request $request)
    {
        if (!$request->request->get("form")) {
            return $this->redirect("/");
        }

        $url = $request->request->get("form")["url"];
        $custom_slug = $request->request->get("form")["slug"];

        $new_link = new Link();

        // double check that the url is 'valid'
        if (!$this->matchesDomainRegex($url)) {
            return $this->redirect("/err=2");
        }

        // set url
        $new_link->setUrl($url);

        // set slug
        if ($custom_slug) {
            if (strlen($custom_slug) < getenv("MIN_SLUG_LENGTH")) {
                return $this->redirect("/err=1");
            }
            // is custom slug unique?
            if ($this->slugAlreadyExists($custom_slug)) {
                return $this->redirect("/err=0");
            } else {
                $new_link->setSlug($custom_slug);
            }
        } else {
            if ($custom_slug === "") {
                $custom_slug = null;
            }

            // first, check if this exists in database
            /** @var \App\Repository\LinkRepository $link_repo */
            $link_repo = $this->getDoctrine()->getRepository(Link::class);
            /** @var \App\Entity\Link $link */
            $link = $link_repo->findOneBy(array("url" => $url));

            // output pre-existing entry if match
            if ($link) {
                return $this->render('output.html.twig', array(
                    'output_link' => getenv("HTTP_HOST") . "/" . $link->getSlug(),
                ));
            }

            // generate random slug
            $random_slug = $this->getRandomSlug();
            while ($this->slugAlreadyExists($random_slug)) {
                $random_slug = $this->getRandomSlug();
            }
            $new_link->setSlug($random_slug);
        }

        $output_link = getenv("HTTP_HOST") . "/" . $new_link->getSlug();

        // save it to database
        $em = $this->getDoctrine()->getManager();
        $em->persist($new_link);
        $em->flush();

        // output
        return $this->render('output.html.twig', array(
            'output_link' => $output_link,
        ));
    }

    /**
     * @Route("/err={error_id}", name="errorDisplay", requirements={"error_id"="[0-2]"})
     * @param int $error_id
     * @return string
     */
    public function errorDisplay($error_id)
    {
        $errors = array(
            0 => "Hmm.. Looks like that slug is already taken.",
            1 => "That slug is too short.",
            2 => "Invalid URL.",
        );

        return $this->render("error.html.twig", array(
            "error" => $errors[$error_id],
        ));
    }

    /**
     * @Route("/{slug}", name="redirectSlug", requirements={"slug"="^[a-zA-Z0-9-._~]{5,100}?"})
     * @param string $slug
     * @return string
     */
    public function redirectSlug($slug)
    {
        /** @var \App\Repository\LinkRepository $link_repo */
        $link_repo = $this->getDoctrine()->getRepository(Link::class);
        /** @var \App\Entity\Link $link */
        $link = $link_repo->findOneBy(array("slug" => $slug));

        // If not a valid slug, redirect to home
        if (!$link) {
            return $this->redirect("/");
        }

        $url = $link->getUrl();

        if (!preg_match("/https?:\/\//", $url)) {
            $url = "http://" . $url;
        }

        $link->addOneVisit();

        $em = $this->getDoctrine()->getManager();
        $em->persist($link);
        $em->flush();

        // Handle redirect. This is what it's all for.
        return new RedirectResponse($url, getenv("REDIRECT_RESPONSE"));
    }

    public function matchesDomainRegex($url)
    {
        $regex = "/^([-a-zA-Z0-9@:%._\/+~#=]{2,256}\.[a-z]{2,6}\b)([-a-zA-Z0-9@:%_\+.~#?&\/=]*)$/";
        return (preg_match($regex, $url));
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
    public function getRandomSlug($length = null)
    {
        if (!$length) {
            $length = getenv("RANDOM_SLUG_LENGTH");
        }
        $seed = str_split('abcdefghijklmnopqrstuvwxyz'
            .'ABCDEFGHIJKLMNOPQRSTUVWXYZ'
            .'0123456789_-~');
        shuffle($seed);
        $rand = '';
        foreach (array_rand($seed, $length) as $k) {
            $rand .= $seed[$k];
        }
        return $rand;
    }

    /**
     * @Route("/api/slug/{slug}", name="checkSlug")
     * @param string $slug
     * @return JsonResponse
     */
    public function isSlugAvailable($slug)
    {
        $success = true;
        $errors = [];

        // check slug length
        if (strlen($slug) < getenv("MIN_SLUG_LENGTH")) {
            $success = false;
            $min_slug = getenv("MIN_SLUG_LENGTH");
            $errors[] = "Slug must be at least $min_slug characters.";
        }

        // regex to match potential slug
        if (!preg_match("/^[a-zA-Z0-9-_~]{0,100}$/", $slug)) {
            $success = false;
            $errors[] = "Invalid slug.";
        }

        // if length and regex check successful
        if ($success) {
            /** @var \App\Repository\LinkRepository $link_repo */
            $link_repo = $this->getDoctrine()->getRepository(Link::class);
            /** @var \App\Entity\Link[] $link */
            $link = $link_repo->findBy(array("slug" => $slug));

            // if slug already exists
            if (count($link) > 0) {
                $errors[] = "Slug already in use.";
            }
        // otherwise, make it so slugAvailable fails
        } else {
            $link = ["darn."];
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

    /**
     * @Route("/api/valid_url", name="checkUrl")
     * @param Request $request
     * @return JsonResponse
     */
    public function isValidUrl(Request $request)
    {
        $success = true;
        $errors = [];

        $url = $request->query->get("url");

        $regex = "/^([-a-zA-Z0-9@:%._\/+~#=]{2,256}\.[a-z]{2,6}\b)([-a-zA-Z0-9@:%_\+.~#?&\/=]*)$/";
        if (!preg_match($regex, $url)) {
            $success = false;
            $errors[] = "Not a valid URL.";
        }

        return new JsonResponse([
            "success" => $success,
            "data" => [
                "url" => $url,
                "errors" => $errors
            ]
        ]);
    }

    public function getIndexForm()
    {
        $link = new Link();

        return $this->createFormBuilder($link, array(
            "attr" => array(
                "id" => "link_form",
            )))
            ->setAction($this->generateUrl('newLink'))
            ->add("url", TextType::class, array(
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
                    "class" => "data-valid",
                    "maxlength" => "100",
                    "placeholder" => "Custom Message ( linkst.rip/YourMessageHere )",
                ),
            ))
            ->add("save", SubmitType::class, array(
                "label" => "New Link",
                "attr" => array(
                    "class" => "",
                    "disabled" => true,
                )
            ))
            ->getForm();
    }
}
