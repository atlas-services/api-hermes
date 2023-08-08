<?php

namespace App\Controller;

use App\Entity\Template;
use App\Form\TemplateType;
use App\Repository\TemplateRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/template')]
class TemplateController extends AbstractController
{
    #[Route('/', name: 'app_template_index', methods: ['GET'])]
    public function index(TemplateRepository $templateRepository): Response
    {
        return $this->render('template/index.html.twig', [
            'templates' => $templateRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_template_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $template = new Template();
        $form = $this->createForm(TemplateType::class, $template);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($template);
            $entityManager->flush();

            return $this->redirectToRoute('app_template_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('template/new.html.twig', [
            'template' => $template,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_template_show', methods: ['GET'])]
    public function show(Template $template): Response
    {
        return $this->render('template/show.html.twig', [
            'template' => $template,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_template_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Template $template, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TemplateType::class, $template);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_template_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('template/edit.html.twig', [
            'template' => $template,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_template_delete', methods: ['POST'])]
    public function delete(Request $request, Template $template, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$template->getId(), $request->request->get('_token'))) {
            $entityManager->remove($template);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_template_index', [], Response::HTTP_SEE_OTHER);
    }
}
