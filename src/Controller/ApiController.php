<?php

namespace App\Controller;

use App\Entity\Link;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends AbstractController
{

    /**
     * @Route("/link", name="newLink")
     * @param Request $request
     * @return string
     */
    public function newLink(Request $request)
    {
        $url = $request->get("url");
        $slug = $request->get("slug");
        $min_slug_len = getenv("MIN_SLUG_LENGTH");

        // if the slug isn't empty, but doesn't
        // meet requirement, then fail
        if (!empty($slug) && strlen($slug) < $min_slug_len) {
            return new JsonResponse([
                "success" => false,
                "error" => "Slug must be at least $min_slug_len characters long"
            ]);
        } else {
            $slug = null;
        }

        // double check that the url is 'valid'
        if (!$this->urlMatchesDomainRegex($url)) {
            return new JsonResponse([
                "success" => false,
                "error" => "Invalid Url"
            ]);
        }

        $new_link = new Link();
        $new_link->setUrl($url);

        if ($slug) {
            // if custom slug already exists, fail
            if ($this->slugAlreadyExists($slug)) {
                return new JsonResponse([
                    "success" => false,
                    "error" => "Slug already exists"
                ]);
            } else {
                $new_link->setIsCustomSlug(true);
            }
        } else {
            $new_link->setIsCustomSlug(false);
        }

        // check for reusable link
        $link_repo = $this->getDoctrine()->getRepository(Link::class);
        $old_link = $link_repo->findOneBy(array("url" => $url));

        // if there's no custom slug
        // and the link already exists
        if (!$slug && $old_link) {
            // if old link is random slug, reuse
            if (!$old_link->getIsCustomSlug()) {
                return new JsonResponse([
                    "success" => true,
                    "url" => getenv("HTTP_HOST") . "/" . $old_link->getSlug()
                ]);
            }
        }

        if ($new_link->getIsCustomSlug()) {
            $new_link->setSlug($slug);
        } else {
            $random_slug = $this->getRandomSlug();
            while ($this->slugAlreadyExists($random_slug)) {
                $random_slug = $this->getRandomSlug();
            }
            $new_link->setSlug($random_slug);
        }

        $new_url = getenv("HTTP_HOST") . "/" . $new_link->getSlug();

        // save it to database
        $em = $this->getDoctrine()->getManager();
        $em->persist($new_link);
        $em->flush();

        // output
        return new JsonResponse([
            "success" => true,
            "url" => $new_url
        ]);
    }

    /**
     * @Route("/{slug}", name="redirectSlug", requirements={"slug"="^[a-zA-Z0-9-._~]{5,100}?"})
     * @param string $slug
     * @return RedirectResponse
     */
    public function redirectSlug($slug)
    {
        $link_repo = $this->getDoctrine()->getRepository(Link::class);
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

    /**
     * @param string $slug
     * @return boolean
     */
    public function slugAlreadyExists($slug)
    {
        $link_repo = $this->getDoctrine()->getRepository(Link::class);
        $link = $link_repo->findOneBy(array("slug" => $slug));

        return !empty($link);
    }

    /**
     * @param int $length
     * @return string
     */
    public function getRandomSlug($length = null)
    {
        $length = $length ?? getenv("RANDOM_SLUG_LENGTH");

        $seed = str_split(
            'abcdefghijklmnopqrstuvwxyz'
            .'ABCDEFGHIJKLMNOPQRSTUVWXYZ'
            .'0123456789_-~'
        );

        shuffle($seed);
        $rand = '';
        foreach (array_rand($seed, $length) as $k) {
            $rand .= $seed[$k];
        }

        return $rand;
    }

    /**
     * @Route("/api/checkSlug", name="checkSlug", methods={"GET"})
     * @param Request $request
     * @return JsonResponse
     */
    public function checkSlugAvailability(Request $request)
    {
        $slug = $request->get("slug");

        if (empty($slug)) {
            return new JsonResponse([
                "success" => false,
                "error" => "Cannot be empty"
            ]);
        }

        $min_slug_len = getenv("MIN_SLUG_LENGTH");

        if (strlen($slug) < $min_slug_len) {
            return new JsonResponse([
                "success" => false,
                "error" => "Message must be at least $min_slug_len characters."
            ]);
        }


        if (!preg_match("/^[a-zA-Z0-9-_~]{{$min_slug_len},100}$/", $slug)) {
            return new JsonResponse([
                "success" => false,
                "error" => "Can only contain a-z, 0-9, -_~."
            ]);
        }

        /** @var \App\Repository\LinkRepository $link_repo */
        $link_repo = $this->getDoctrine()->getRepository(Link::class);
        $link = $link_repo->findOneBy(array("slug" => $slug));

        if (!empty($link)) {
            return new JsonResponse([
                "success" => false,
                "error" => "Slug already taken"
            ]);
        }

        return new JsonResponse([
            "success" => true
        ]);
    }

    /**
     * @Route("/api/validUrl", name="checkUrl", methods={"GET"})
     * @param Request $request
     * @return JsonResponse
     */
    public function checkIfValidUrl(Request $request)
    {
        $url = $request->get("url");

        if (!$this->urlMatchesDomainRegex($url)) {
            return new JsonResponse([
                "success" => false,
                "error" => "Not a valid URL."
            ]);
        }

        return new JsonResponse([
            "success" => true
        ]);
    }

    public function urlMatchesDomainRegex($url)
    {
        $regex = "/^([-a-zA-Z0-9@:%._\/+~#=]{2,256}\.[a-z]{2,6}\b)([-a-zA-Z0-9@:%_\+.~#?&\/=]*)$/";
        return (preg_match($regex, $url));
    }
}