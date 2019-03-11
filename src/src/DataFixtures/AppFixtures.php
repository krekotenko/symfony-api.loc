<?php

namespace App\DataFixtures;

use App\Entity\BlogPost;
use App\Entity\User;
use App\Entity\Comment;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * @var \Faker\Factory
     */
    private $faker;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->faker = \Faker\Factory::create();
    }

    public function load(ObjectManager $manager)
    {
        $this->loadUsers($manager);
        $this->loadBlogPosts($manager);
        $this->loadComments($manager);
    }

    public function loadBlogPosts(ObjectManager $manager) {

        $user = $this->getReference('admin');

        for ($i = 0; $i<=10; $i++)
        {
            $blogPost = new BlogPost();
            $blogPost->setTitle($this->faker->realText(30));
            $blogPost->setSlug("post-$i");
            $blogPost->setAuthor($user);
            $blogPost->setContent($this->faker->realText());
            $blogPost->setPublished( new \DateTime($this->faker->date()));
            $this->addReference("post_$i", $blogPost);
            $manager->persist($blogPost);
        }

        $manager->flush();
    }

    public function loadComments(ObjectManager $manager) {

        $user = $this->getReference('admin');

        for ($i = 0; $i <=rand(1, 10); $i++){
            $blogPost = $this->getReference("post_$i");
            $comment = new Comment();
            $comment->setContent($this->faker->realText());
            $comment->setPublished(new \DateTime($this->faker->date()));
            $comment->setBlogPost($blogPost);
            $comment->setAuthor($user);
            $manager->persist($comment);
        }

        $manager->flush();
    }


    public function loadUsers(ObjectManager $manager) {
        $user = new User();
        $user->setUsername('admin');
        $user->setEmail('krekotenko@gmail.com');
        $user->setFullname('Krekotenko Anatoliy');
        $user->setPassword($this->passwordEncoder->encodePassword(
            $user,
            '1111'
        ));

        $this->addReference('admin', $user);

        $manager->persist($user);

        $manager->flush();

    }
}
