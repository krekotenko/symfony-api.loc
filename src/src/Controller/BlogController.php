<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 05.03.2019
 * Time: 14:38
 */

namespace App\Controller;


use App\Entity\BlogPost;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class BlogController
 * @package App\Controller
 * @Route("/blog")
 */
class BlogController extends AbstractController
{
    /**
     * @Route("/{page}", name="blog_list", defaults={"page": 1}, requirements={"page"="\d+"})
     * @param $request Request
     * @param $page integer
     * @return object
     */
    public function list($page = 1, Request $request) {
        $limit = $request->get('limit', 10);
        $repository = $this->getDoctrine()->getRepository(BlogPost::class);
        $items = $repository->findAll();

        return $this->json([
            'page' => $page,
            'limit' => $limit,
            'data' => array_map(function (BlogPost $item) {
                return $this->generateUrl('blog_by_slug', ['slug' => $item->getSlug()]);
            }, $items)
        ]);
    }

    /**
     * @Route("/post/{id}", name="blog_by_id", requirements={"id"="\d+"}, methods={"GET"})
    */
    public function post($id) {

        return $this->json(
            $this->getDoctrine()->getRepository(BlogPost::class)->find($id)
        );


    }
    /**
     * @param $post BlogPost
     * @Route("/post/{slug}", name="blog_by_slug", methods="GET")
     * @return object
    */
    public function postBySlug(BlogPost $post) {

        //available this
        /*return $this->json(
            $this->getDoctrine()->getRepository(BlogPost::class)->findBy(['slug' => $slug])
        );*/
        //or this
        return $this->json($post);

    }

    /**
     * @param Request $request
     * @Route("/add", name="add_blog_item", methods="POST")
     * @return object
     */
    public function add(Request $request) {

        /**
         * @var Serializer $serializer
         */
        $serializer = $this->get('serializer');
      

        $blogPost = $serializer->deserialize($request->getContent(), BlogPost::class, 'json');
        $em = $this->getDoctrine()->getManager();
        $em->persist($blogPost);
        $em->flush();

        return$this->json($blogPost);
    }

    /**
     * @param $post BlogPost
     * @Route("/post/{id}", name="delete_blog_item", requirements={"id"="\d+"},  methods={"DELETE"})
     * @return JsonResponse
     */
    public function delete(BlogPost $post) {

        $em = $this->getDoctrine()->getManager();
        $em->remove($post);
        $em->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }


}
