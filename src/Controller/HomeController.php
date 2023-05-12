<?php

namespace App\Controller;
use App\Entity\Doctor;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Psr\Log\LoggerInterface;

class HomeController extends AbstractController
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
    #[Route('/qui', name: 'app_qui')]
    public function goqui(): Response
    {
        $this->logger->info("test");
        return $this->render("shkounek.html.twig");
    }
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('base.html.twig');
    }
    
    #[Route('/Doctors', name: 'app_Doctor')]
    public function Doctors(): Response
    {
            return $this->render('home/doctor.html.twig');
    }
    #[Route('/u', name: 'app_uhome')]
    public function uhome(): Response
        {
            return $this->render('base.html.twig');
        }
     #[Route('/d', name: 'app_dhome')]
    public function dhome(): Response
        {
            return $this->render('doctor.html.twig');
        }
            

        
    
}

