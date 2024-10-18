<?php

namespace App\Controller;

use App\Entity\Dette;
use App\Form\DetteType;
use App\Repository\DetteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DetteController extends AbstractController
{
    #[Route('/dettes', name: 'dettes.index', methods: ['GET', 'POST'])]
    public function index(DetteRepository $detteRepository, Request $request): Response
    {
        $formSearch = $this->createFormBuilder()
            ->add('status', \Symfony\Component\Form\Extension\Core\Type\ChoiceType::class, [
                'choices' => [
                    'Montant payé' => 'paye',
                    'Montant non payé' => 'non_paye',
                ],
                'expanded' => true, // affichera sous forme de checkbox
                'multiple' => true, // permettra de sélectionner plusieurs cases
            ])
            ->getForm();
        
        $formSearch->handleRequest($request);
        $dettes = [];

        if ($formSearch->isSubmitted() && $formSearch->isValid()) {
            $status = $formSearch->get('status')->getData();
            if (in_array('paye', $status)) {
                $dettes = $detteRepository->findByMontantPayé(); // À définir dans le repository
            }
            if (in_array('non_paye', $status)) {
                $dettes = $detteRepository->findByMontantNonPayé(); // À définir dans le repository
            }
        } else {
            $dettes = $detteRepository->findAll();
        }

        return $this->render('dette/index.html.twig', [
            'dettes' => $dettes,
            'formSearch' => $formSearch->createView(),
        ]);
    }

    #[Route('/dettes/create', name: 'dettes.create', methods: ['GET', 'POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $dette = new Dette();
        $form = $this->createForm(DetteType::class, $dette);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $dette->setCreateAt(new \DateTimeImmutable());
            $dette->setUpdateAt(new \DateTimeImmutable());
            
            $entityManager->persist($dette);
            $entityManager->flush();

            return $this->redirectToRoute('dettes.index');
        }

        return $this->render('dette/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
