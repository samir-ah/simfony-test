<?php

namespace App\Controller;

use App\Entity\Activity;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class IndexController extends AbstractController
{
    /**
     * @Route("/inscription",methods={"GET","POST"})
     */
    public function index(EntityManagerInterface $em,Request $request)
    {
        $message = "Vous devez vous inscrire";
        $form = $this->createFormBuilder()
            ->add("email")
            ->add("name")
            ->add("prenom")
            ->add("date_naissance",DateType::class)
            ->add("password")
            ->add('submit',SubmitType::class,['label'=>'envoyer'])
            ->getForm();
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $data = $form->getData();
            $user1 = new User();
            $user1->setEmail($data["email"]);
            $user1->setName($data["name"]);
            $user1->setPrenom($data["prenom"]);
            $user1->setDateNaissance($data["date_naissance"]);
            $user1->setPassword($data["password"],PasswordType::class);
            $user1->setRole(2);
            $user1->setActive(false);
            $em->persist($user1);
            $em->flush();
            $message="vous etes inscrit, votre compte va etre validé par l'admin";
        }
        return $this->render('index/inscription.html.twig',["form"=>$form->createView(),"message"=>$message]);
    }

    /**
     * @Route("/login",methods={"GET","POST"})
     */
    public function connection(EntityManagerInterface $em,Request $request)
    {
        $message = "Vous devez vous connecter";
        $form = $this->createFormBuilder()
            ->add("email")
            ->add("password",PasswordType::class)
            ->add('submit',SubmitType::class,['label'=>'se connecter'])
            ->getForm();
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $data = $form->getData();
            $rep = $em->getRepository(User::class);
            $user1 = $rep->findOneBy(["email"=>$data['email']]);
            if($user1 !== null){
                if($user1->getPassword() === $data["password"]) {
                    if($user1->getActive()){
                        return $this->redirectToRoute('mon_espace');
                    }
                    else{
                        $message = "compte non activé";
                    }
                }
                else{
                    $message = "email/password incorrect";
                }
            }
            else{
                $message = "email/password incorrect";
            }
        }
        return $this->render('index/inscription.html.twig',["form"=>$form->createView(),"message"=>$message]);
    }
    /**
     * @Route("/users",name="mon_espace")
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function home(EntityManagerInterface $em): Response
    {   $repo = $em->getRepository(Activity::class);
        $activities = $repo->findAll();
        return $this->render('index/mon_espace.html.twig',["activities"=>$activities]);
    }
    /**
     * @Route("/subscribe/{activityid}")
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function subscribe(string $activityid,activityidEntityManagerInterface $em,UserInterface $user): Response
    {   $repo = $em->getRepository(Activity::class);
        $activitie = $repo->findOneBy(["id"=>$activityid]);

        $activitie->addUser($user);
        return new Response("inscrit");
    }
}
