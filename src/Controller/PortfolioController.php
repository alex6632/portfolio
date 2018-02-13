<?php

namespace App\Controller;

use App\Entity\Email;
use App\Entity\Formation;
use App\Entity\Projet;
use App\Form\ContactType;
use Swift_Mailer;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class PortfolioController extends Controller
{
    /**
     * @Route("/", name="accueil")
     */
    public function indexAction() {
        return $this->render('Portfolio/index.html.twig');
    }

    /**
     * @Route("/contact/submit", name="contact_form")
     */
    public function contactFormAction(Request $request, Swift_Mailer $mailer) {

        $em = $this->getDoctrine()->getEntityManager();
        $email = new Email();
        $contactForm = $this->createForm(ContactType::class, $email);
        $contactForm->handleRequest($request);
        //$contactForm = $this->createForm(ContactType::class, null, array());

        if ($contactForm->isSubmitted() && $contactForm->isValid()) {
            $currentDate = new \DateTime("now");
            $email->setDate($currentDate);
            $em->persist($email);
            $em->flush();
            /*
            $data = $contactForm->getData();
            $message = (new \Swift_Message('Message portfolio'))
                ->setFrom($data['email'])
                ->setTo('asimonin.digital@gmail.com')
                ->setBody(
                    $this->renderView(
                        'Portfolio/partials/email.html.twig',
                        array(
                            'nom' => $data['lastName'],
                            'prenom' => $data['firstName'],
                            'email' => $data['email'],
                            'message' => $data['message'],
                        )
                    ),
                    'text/html'
                );
            $mailer->send($message);
            */
            $message = array(
                'type'       => 'success',
                'message'        => 'Merci ! Le message a bien été envoyé !',
            );
            return new JsonResponse($message, 200);
        }

        return $this->render('Portfolio/partials/contactForm.html.twig', array(
            'ContactForm'  => $contactForm->createView()
        ));
    }

    /**
     * @Route("/formation", name="formation")
     */
    public function FormationAction() {
        $formationList = $this->getDoctrine()
            ->getRepository(Formation::class)
            ->findAll();

        return $this->render('Portfolio/formation.html.twig', array(
            'FormationList' => $formationList
        ));
    }

    /**
     * @Route("/projets", name="projets")
     */
    public function ProjectsAction() {
        $projectList = $this->getDoctrine()
            ->getRepository(Projet::class)
            ->findAll();

        return $this->render('Portfolio/projets.html.twig', array(
            'ProjectList' => $projectList
        ));
    }


    /**
     * @Route("/contact", name="contact")
     */
    public function ContactAction() {
        return $this->render('Portfolio/contact.html.twig');
    }
}