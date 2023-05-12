<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

#[Route('/user')]
class UserController extends AbstractController
{
    #[Route('/', name: 'app_user_index')]
    public function index(Request $request,UserRepository $userRepository,): Response
    {   
      
        $patient=$userRepository->findAll(); 
        $back = null;
            
        if($request->isMethod("POST")){
            if ( $request->request->get('optionsRadios')){
                $SortKey = $request->request->get('optionsRadios');
                switch ($SortKey){
                    case 'Age':
                        $patient = $userRepository->SortByAge();
                        break;
                        case 'id':
                            $patient = $userRepository->SortByid();
                            break;
                           
                        
                }
                
           
            }
            
            else
            {
                $type = $request->request->get('optionsearch');
                $value = $request->request->get('Search');
                switch ($type){
                    case 'Nom':
                        $patient = $userRepository->findByNom($value);
                        break;
                        case 'Preom':
                            $patient = $userRepository->findByPrenom($value);
                            break;
                            case 'Age':
                                $patient = $userRepository->findByAge($value);
                                break;

                    

                }
            }

            if ( $patient){
                $back = "success";
            }else{
                $back = "failure";
            }
            $patient = $userRepository->findByRoleperm($value);

        }
        return $this->render('user/index.html.twig', [
            'users' => $patient,'back'=>$back
        ]);
    }

    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, UserRepository $userRepository): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userRepository->save($user, true);

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(User $user): Response
    {    

        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }
   

    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
           
            $user = $form->getData();
            $user->setPassword(
                $passwordEncoder->encodePassword($user, $form['plainPassword']->getData())
            );
            $this->getDoctrine()->getManager()->flush();
    
            return $this->redirectToRoute('app_user_index', ['id' => $user->getId()]);
        }
    
        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, UserRepository $userRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $userRepository->remove($user, true);
        }

        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/f', name: 'app_f')]
    public function front(UserRepository $repository,Request $request)
    {
        $patients=$repository->findAll();
       
           
        return $this->render('user/index.html.twig',['patients'=>$patients]);
    }






}