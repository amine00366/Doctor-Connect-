<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Medicament;
use App\Entity\Categorie;
use App\Form\MedicamentType;
use App\Repository\CategorieRepository;
use App\Repository\MedicamentRepository;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;  
use Doctrine\ORM\EntityManagerInterface;;
use Symfony\Component\HttpFoundation\Request;


class JsonController extends AbstractController
{
        #[Route("/allmeds", name: "listmeds")]
        public function getTypeRec(MedicamentRepository $repo, SerializerInterface $serializer)
        {
            $ca = $repo->findAll();
            $json = $serializer->serialize($ca, 'json', ['groups' => ["meds","cats"]]);
            return new Response($json);
        }
        #[Route("/medJSON/{id}", name: "medJSON")]
        public function getMedicamentById($id, MedicamentRepository $repo, SerializerInterface $serializer): Response
        {
            $reclamation = $repo->find($id);
            $jsonContent = $serializer->serialize($reclamation, 'json', ['groups' => ['meds', 'cats']]);
            return new Response($jsonContent);
        }
}
