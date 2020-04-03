<?php

namespace App\Controller;

use App\Entity\Activity;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index(EntityManagerInterface $em)
    {
        $activityRep = $em->getRepository(Activity::class);
        $activities = $activityRep->findAll();

        $userRep= $em->getRepository(User::class);
        $usersToValidate = $userRep->findBy(["active"=>false]);

        return $this->render('admin/index.html.twig', [
            'activities' => $activities,
            'inscriptions_attente'=>$usersToValidate
        ]);
    }

    /**
     * @Route("/admin/activate/{userEmail}", name="activer_user")
     * @param string $userEmail
     * @param EntityManagerInterface $em
     * @return void
     */
    public function activerUser(string $userEmail,EntityManagerInterface $em)
    {

        $userRep= $em->getRepository(User::class);
        $user = $userRep->findOneBy(["email"=>$userEmail]);
        $user->setActive(true);
        return new Response("active");

    }
}
