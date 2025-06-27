<?php

namespace App\Controller;

use App\Document\Rencontre;
use App\Document\Utilisateur;
use Doctrine\ODM\MongoDB\DocumentManager;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

#[Route('/api/rencontres')]
class RencontreController extends AbstractController
{
    #[Route('', name: 'list_rencontres', methods: ['GET'])]
    public function index(DocumentManager $dm): JsonResponse
    {
        $rencontres = $dm->getRepository(Rencontre::class)->findAll();

        $data = array_map(fn(Rencontre $r) => [
            'id' => $r->getId(),
            'coach' => $r->getCoach()->getId(),
            'date_match' => $r->getDateMatch()->format('Y-m-d'),
            'point_gagnant' => $r->getPointGagnant(),
        ], $rencontres);

        return new JsonResponse($data);
    }

    #[Route('/coach/{id}', name: 'list_rencontres_by_coach', methods: ['GET'])]
    public function byCoach(string $id, DocumentManager $dm): JsonResponse
    {
        $coach = $dm->getRepository(Utilisateur::class)->find($id);
        if (!$coach) return new JsonResponse(['error' => 'Coach introuvable'], 404);

        $rencontres = $dm->getRepository(Rencontre::class)->findBy(['coach' => $coach]);

        $data = array_map(fn(Rencontre $r) => [
            'id' => $r->getId(),
            'date_match' => $r->getDateMatch()->format('Y-m-d'),
            'point_gagnant' => $r->getPointGagnant(),
        ], $rencontres);

        return new JsonResponse($data);
    }

    #[Route('/{id}', name: 'get_rencontre', methods: ['GET'])]
    public function show(string $id, DocumentManager $dm): JsonResponse
    {
        $r = $dm->getRepository(Rencontre::class)->find($id);
        if (!$r) return new JsonResponse(['error' => 'Rencontre introuvable'], 404);

        return new JsonResponse([
            'id' => $r->getId(),
            'coach_id' => $r->getCoach()->getId(),
            'date_match' => $r->getDateMatch()->format('Y-m-d'),
            'point_gagnant' => $r->getPointGagnant(),
            'qrCode' => $r->getQrCode()
        ]);
    }

    #[Route('', name: 'api_rencontre_add', methods: ['POST'])]
    public function add(Request $request, DocumentManager $dm): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (empty($data['coach_id']) || empty($data['date_match']) || !isset($data['point_gagnant'])) {
            return new JsonResponse(['error' => 'Champs requis manquants'], 400);
        }

        $coach = $dm->getRepository(Utilisateur::class)->find($data['coach_id']);
        if (!$coach) {
            return new JsonResponse(['error' => 'Coach introuvable'], 404);
        }

        $dateMatch = \DateTime::createFromFormat('Y-m-d', $data['date_match']);
        if (!$dateMatch) {
            return new JsonResponse(['error' => 'Format de date invalide (attendu: Y-m-d)'], 400);
        }

        if (!is_numeric($data['point_gagnant']) || $data['point_gagnant'] < 0) {
            return new JsonResponse(['error' => 'Score invalide (doit être >= 0)'], 400);
        }

        $qrContent = sprintf("Coach: %s\nDate: %s\nPoints gagnants: %d",
            $coach->getNom(),
            $dateMatch->format('Y-m-d'),
            $data['point_gagnant']
        );

        $qrImage = Builder::create()
            ->writer(new PngWriter())
            ->data($qrContent)
            ->size(300)
            ->margin(10)
            ->build()
            ->getString();

        $qrCodeBase64 = base64_encode($qrImage);

        $rencontre = new Rencontre();
        $rencontre->setCoach($coach);
        $rencontre->setDateMatch($dateMatch);
        $rencontre->setPointGagnant($data['point_gagnant']);
        $rencontre->setQrCode($qrCodeBase64);

        $dm->persist($rencontre);
        $dm->flush();

        return new JsonResponse([
            'id' => $rencontre->getId(),
            'qrCode' => $qrCodeBase64,
        ], 201);
    }

    #[Route('/{id}', name: 'update_rencontre', methods: ['PUT'])]
    public function update(string $id, Request $request, DocumentManager $dm): JsonResponse
    {
        $r = $dm->getRepository(Rencontre::class)->find($id);
        if (!$r) return new JsonResponse(['error' => 'Rencontre introuvable'], 404);

        $data = json_decode($request->getContent(), true);

        if (
            empty($data['coach_id']) ||
            empty($data['date_match']) ||
            !isset($data['point_gagnant'])
        ) {
            return new JsonResponse(['error' => 'Champs manquants'], 400);
        }

        $coach = $dm->getRepository(Utilisateur::class)->find($data['coach_id']);
        if (!$coach) return new JsonResponse(['error' => 'Coach introuvable'], 404);

        $dateMatch = \DateTime::createFromFormat('Y-m-d', $data['date_match']);
        if (!$dateMatch) return new JsonResponse(['error' => 'Date invalide'], 400);

        if (!is_numeric($data['point_gagnant']) || $data['point_gagnant'] < 0) {
            return new JsonResponse(['error' => 'Score invalide (doit être >= 0)'], 400);
        }

        $r->setCoach($coach);
        $r->setDateMatch($dateMatch);
        $r->setPointGagnant($data['point_gagnant']);

        $qrContent = sprintf("Coach: %s\nDate: %s\nPoints: %d",
            method_exists($coach, 'getNom') ? $coach->getNom() : 'Coach',
            $dateMatch->format('Y-m-d'),
            $data['point_gagnant']
        );
        $qrBase64 = base64_encode(Builder::create()
            ->writer(new PngWriter())
            ->data($qrContent)
            ->size(300)
            ->margin(10)
            ->build()->getString()
        );
        $r->setQrCode($qrBase64);

        $dm->flush();

        return new JsonResponse(['message' => 'Rencontre mise à jour']);
    }

    #[Route('/{id}', name: 'delete_rencontre', methods: ['DELETE'])]
    public function delete(string $id, DocumentManager $dm): JsonResponse
    {
        $r = $dm->getRepository(Rencontre::class)->find($id);
        if (!$r) return new JsonResponse(['error' => 'Rencontre introuvable'], 404);

        $dm->remove($r);
        $dm->flush();

        return new JsonResponse(['message' => 'Rencontre supprimée']);
    }

    #[Route('/{id}/qr-code', name: 'rencontre_qrcode', methods: ['GET'])]
    public function qrCode(string $id, DocumentManager $dm): Response
    {
        $r = $dm->getRepository(Rencontre::class)->find($id);
        if (!$r) return new JsonResponse(['error' => 'Rencontre introuvable'], 404);

        $qrBinary = base64_decode($r->getQrCode());

        return new Response($qrBinary, 200, [
            'Content-Type' => 'image/png'
        ]);
    }

}

