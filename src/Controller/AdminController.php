<?php

namespace App\Controller;

use App\Entity\Email;
use App\Entity\Formation;
use App\Entity\Projet;
use App\Entity\Tag;
use App\Form\FormationType;
use App\Form\ProjectType;
use App\Form\TagType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * @Route("/admin")
 */
class AdminController extends Controller
{
    /**
     * @Route("/dashboard", name="dashboard")
     */
    public function indexAction(Request $request, EntityManagerInterface $em)
    {
        $emailNoReadList = $em
            ->getRepository(Email::class)
            ->findEmailByView(0);
        $countNoReadEmail = count($emailNoReadList);

        $formation_list = $em
            ->getRepository(Formation::class)
            ->findAll();

        $project_list = $em
            ->getRepository(Projet::class)
            ->findAll();

        $tag_list = $em
            ->getRepository(Tag::class)
            ->findAll();

        return $this->render('Admin/dashboard.html.twig', array(
            'FormationList'    => $formation_list,
            'ProjectsList'     => $project_list,
            'TagsList'         => $tag_list,
            'NoReadEmailCount' => $countNoReadEmail
        ));
    }

    /*****************************
     * TAG ACTIONS
     *****************************/

    /**
     * @Route("/tag/add", name="tag_add")
     */
    public function AddTagAction(Request $request, Session $session, EntityManagerInterface $em) {
        $tag = new Tag();

        $tagForm = $this->createForm(TagType::class, $tag);
        $tagForm->add('Ajouter', SubmitType::class);
        $tagForm->handleRequest($request);

        if($tagForm->isSubmitted() && $tagForm->isValid()) {
            $em->persist($tag);
            $em->flush();

            $session->getFlashBag()->add(
                'success',
                'Le tag a bien été ajouté !'
            );
        }

        $tagList = $this->getDoctrine()
            ->getRepository(Tag::class)
            ->findAll();

        return $this->render('Admin/tag/add.html.twig', array(
            'TagsList' => $tagList,
            'TagsAdd'  => $tagForm->createView()
        ));

    }

    /**
     * @Route("/tag/delete/{id}", name="tag_delete")
     */
    public function DeleteTagAction(Request $request, Session $session, EntityManagerInterface $em, $id) {

        $tag = $em->getRepository(Tag::class)->find($id);
        $em->remove($tag);
        $em->flush();

        $session->getFlashBag()->add(
            'success',
            'Le tag a bien été supprimé !'
        );

        return $this->redirectToRoute('tag_add');
    }

    /**
     * @Route("/tag/edit/{id}", name="tag_edit")
     */
    public function EditTagAction(Request $request, $id, Session $session) {

        $em = $this->getDoctrine()->getEntityManager();
        $tag = $this->getDoctrine()->getRepository(Tag::class)->find($id);

        $tagEditForm = $this->createForm(TagType::class, $tag);
        $tagEditForm->add('Editer', SubmitType::class);
        $tagEditForm->handleRequest($request);

        if($tagEditForm->isSubmitted() && $tagEditForm->isValid()) {

            $em->persist($tag);
            $em->flush();

            $session->getFlashBag()->add(
                'success',
                'Le tag a bien été édité !'
            );

            return $this->redirectToRoute('tag_add');
        }
        return $this->render('Admin/tag/edit.html.twig', array(
            'Tag' => $tag,
            'TagEdit'  => $tagEditForm->createView()
        ));
    }

    /*****************************
     * PROJET ACTIONS
     *****************************/

    /**
     * @Route("/projet/add", name="projet_add")
     */
    public function AddProjetAction(Request $request, Session $session) {

        $em = $this->getDoctrine()->getEntityManager();
        $project = new Projet();

        $projectForm = $this->createForm(ProjectType::class, $project);
        $projectForm->add('Ajouter', SubmitType::class);
        $projectForm->handleRequest($request);

        if($projectForm->isSubmitted() && $projectForm->isValid()) {
            // Upload...
            $file = $project->getImage();
            $fileName = md5(uniqid()).'.'.$file->guessExtension();
            $file->move(
                $this->getParameter('upload_project_image'),
                $fileName
            );
            $project->setImage($fileName);

            $em->persist($project);
            $em->flush();

            $session->getFlashBag()->add(
                'success',
                'Le projet a bien été ajouté !'
            );
        }

        $projectList = $this->getDoctrine()
            ->getRepository(Projet::class)
            ->findAll();

        return $this->render('Admin/projet/add.html.twig', array(
            'ProjectsList' => $projectList,
            'ProjectsAdd'  => $projectForm->createView()
        ));
    }

    /**
     * @Route("/projet/delete/{id}", name="project_delete")
     */
    public function DeleteProjectAction(Request $request, $id, Session $session) {

        $em = $this->getDoctrine()->getEntityManager();
        $project = $this->getDoctrine()->getRepository(Projet::class)->find($id);

        $projectImage = $project->getImage();
        $imagePath = $this->get('kernel')->getRootDir().'/../public/uploads/projet/'.$projectImage;
        unlink($imagePath);

        $em->remove($project);
        $em->flush();

        $session->getFlashBag()->add(
            'success',
            'Le projet a bien été supprimé !'
        );

        return $this->redirectToRoute('projet_add');
    }

    /**
     * @Route("/projet/edit/{id}", name="project_edit")
     */
    public function EditProjectAction(Request $request, Session $session, $id) {

        $em = $this->getDoctrine()->getEntityManager();
        $project = $this->getDoctrine()->getRepository(Projet::class)->find($id);

        $previousProjectImage = $project->getImage();
        $previousImagePath = $this->get('kernel')->getRootDir().'/../public/uploads/projet/'.$previousProjectImage;

        $projectEditForm = $this->createForm(ProjectType::class, $project);
        $projectEditForm->add('Editer', SubmitType::class);
        $projectEditForm->handleRequest($request);

        if($projectEditForm->isSubmitted() && $projectEditForm->isValid()) {

            $file = $project->getImage();
            $fileName = md5(uniqid()).'.'.$file->guessExtension();
            $file->move(
                $this->getParameter('upload_project_image'),
                $fileName
            );
            $project->setImage($fileName);

            $em->persist($project);
            $em->flush();

            unlink($previousImagePath);

            $session->getFlashBag()->add(
                'success',
                'Le projet a bien été édité !'
            );

            return $this->redirectToRoute('projet_add');
        }
        return $this->render('Admin/projet/edit.html.twig', array(
            'Project' => $project,
            'ProjectEdit'  => $projectEditForm->createView()
        ));
    }

    /*****************************
     * FORMATION ACTIONS
     *****************************/

    /**
     * @Route("/formation/add", name="formation_add")
     */
    public function AddFormationAction(Request $request, Session $session) {

        $em = $this->getDoctrine()->getEntityManager();
        $formation = new Formation();

        $formationAddForm = $this->createForm(FormationType::class, $formation);
        $formationAddForm->add('Ajouter', SubmitType::class);
        $formationAddForm->handleRequest($request);

        if($formationAddForm->isSubmitted() && $formationAddForm->isValid()) {
            // Upload...
            $file = $formation->getImage();
            $fileName = md5(uniqid()).'.'.$file->guessExtension();
            $file->move(
                $this->getParameter('upload_formation_image'),
                $fileName
            );
            $formation->setImage($fileName);

            $em->persist($formation);
            $em->flush();

            $session->getFlashBag()->add(
                'success',
                'La formation a bien été ajoutée !'
            );
        }

        $formationList = $this->getDoctrine()
            ->getRepository(Formation::class)
            ->findAll();

        return $this->render('Admin/formation/add.html.twig', array(
            'FormationList' => $formationList,
            'FormationAdd'  => $formationAddForm->createView()
        ));
    }

    /**
     * @Route("/formation/delete/{id}", name="formation_delete")
     */
    public function DeleteFormationAction(Request $request, Session $session, $id) {

        $em = $this->getDoctrine()->getEntityManager();
        $formation = $this->getDoctrine()->getRepository(Formation::class)->find($id);

        $formationImage = $formation->getImage();
        $imagePath = $this->get('kernel')->getRootDir().'/../public/uploads/formation/'.$formationImage;
        unlink($imagePath);

        $em->remove($formation);
        $em->flush();

        $session->getFlashBag()->add(
            'success',
            'La formation a bien été supprimée !'
        );

        return $this->redirectToRoute('formation_add');
    }

    /**
     * @Route("/formation/edit/{id}", name="formation_edit")
     */
    public function EditFormationAction(Request $request, Session $session, $id)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $formation = $this->getDoctrine()->getRepository(Formation::class)->find($id);

        $previousFormationImage = $formation->getImage();
        $previousImagePath = $this->get('kernel')->getRootDir().'/../public/uploads/formation/'.$previousFormationImage;

        $formationEditForm = $this->createForm(FormationType::class, $formation);
        $formationEditForm->add('Editer', SubmitType::class);
        $formationEditForm->handleRequest($request);

        if($formationEditForm->isSubmitted() && $formationEditForm->isValid()) {

            $file = $formation->getImage();
            $fileName = md5(uniqid()).'.'.$file->guessExtension();
            $file->move(
                $this->getParameter('upload_formation_image'),
                $fileName
            );
            $formation->setImage($fileName);

            $em->persist($formation);
            $em->flush();

            unlink($previousImagePath);

            $session->getFlashBag()->add(
                'success',
                'La formation a bien été édité !'
            );

            return $this->redirectToRoute('formation_add');
        }
        return $this->render('Admin/formation/edit.html.twig', array(
            'Formation' => $formation,
            'FormationEdit'  => $formationEditForm->createView()
        ));
    }


    /**
     * @Route("/email", name="email_list")
     */
    public function EmailList(EntityManagerInterface $em) {
        $emailList = $em
            ->getRepository(Email::class)
            ->findAll();
        $countAllEmail = count($emailList);

        $emailNoReadList = $em
            ->getRepository(Email::class)
            ->findEmailByView(0);
        $countNoReadEmail = count($emailNoReadList);

        return $this->render('Admin/email/list.html.twig', array(
            'EmailList' => $emailList,
            'AllEmailCount' => $countAllEmail,
            'NoReadEmailCount' => $countNoReadEmail
        ));
    }

    /**
     * @Route("/email/count", name="email_count")
     */
    public function EmailCount(EntityManagerInterface $em)
    {
        $emailNoReadList = $em
            ->getRepository(Email::class)
            ->findEmailByView(0);
        $countNoReadEmail = count($emailNoReadList);

        return $this->render('Admin/partials/header.html.twig', array(
            'NoReadEmailCount' => $countNoReadEmail
        ));
    }


    /**
     * @Route("/email/{id}", name="email_show")
     */
    public function Email(EntityManagerInterface $em, $id) {
        $email = $em
            ->getRepository(Email::class)
            ->find($id);

        // Update view to 1...
        $email->setView(1);
        $em->persist($email);
        $em->flush();

        return $this->render('Admin/email/show.html.twig', array(
            'Email' => $email
        ));
    }

    /**
     * @Route("/email/delete/{id}", name="email_delete")
     */
    public function EmailDelete(Request $request, Session $session, $id) {

        $em = $this->getDoctrine()->getEntityManager();
        $email = $this->getDoctrine()->getRepository(Email::class)->find($id);
        $em->remove($email);
        $em->flush();

        $session->getFlashBag()->add(
            'success',
            'Le message a bien été supprimé !'
        );
        return $this->redirectToRoute('email_list');
    }

}