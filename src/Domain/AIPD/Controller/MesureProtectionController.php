<?php

declare(strict_types=1);

namespace App\Domain\AIPD\Controller;

use App\Application\Controller\CRUDController;
use App\Application\Symfony\Security\UserProvider;
use App\Application\Traits\ServersideDatatablesTrait;
use App\Domain\AIPD\Form\Type\MesureProtectionAIPDType;
use App\Domain\AIPD\Model\MesureProtection;
use App\Domain\AIPD\Repository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Snappy\Pdf;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @property Repository\MesureProtection $repository
 */
class MesureProtectionController extends CRUDController
{
    use ServersideDatatablesTrait;

    private RequestStack $requestStack;

    /**
     * @var RouterInterface
     */
    private $router;

    public function __construct(
        EntityManagerInterface $entityManager,
        TranslatorInterface $translator,
        Repository\MesureProtection $repository,
        Pdf $pdf,
        UserProvider $userProvider,
        AuthorizationCheckerInterface $authorizationChecker,
        RequestStack $requestStack,
        RouterInterface $router
    ) {
        parent::__construct($entityManager, $translator, $repository, $pdf, $userProvider, $authorizationChecker);
        $this->requestStack = $requestStack;
        $this->router       = $router;
    }

    protected function getDomain(): string
    {
        return 'aipd';
    }

    protected function getModel(): string
    {
        return 'mesure_protection';
    }

    protected function getModelClass(): string
    {
        return MesureProtection::class;
    }

    protected function getFormType(): string
    {
        return MesureProtectionAIPDType::class;
    }

    public function listAction(): Response
    {
        return $this->render($this->getTemplatingBasePath('list'), [
            'totalItem' => $this->repository->count(),
            'route'     => $this->router->generate('aipd_mesure_protection_list_datatables'),
        ]);
    }

    public function listDataTables(Request $request): JsonResponse
    {
        $request = $this->requestStack->getMasterRequest();

        $mesures = $this->getResults($request);

        $reponse = $this->getBaseDataTablesResponse($request, $mesures);
        /** @var MesureProtection $mesure */
        foreach ($mesures as $mesure) {
            $reponse['data'][] = [
              'nom'                => $mesure->getNom(),
              'nomCourt'           => $mesure->getNomCourt(),
              'detail'             => $mesure->getDetail(),
              'poidsVraisemblance' => $mesure->getPoidsVraisemblance(),
              'poidsGravite'       => $mesure->getPoidsGravite(),
              'actions'            => '',
            ];
        }

        $jsonResponse = new JsonResponse();
        $jsonResponse->setJson(json_encode($reponse));

        return $jsonResponse;
    }

    protected function getLabelAndKeysArray(): array
    {
        return [
            '0' => 'nom',
            '1' => 'nomCourt',
            '2' => 'detail',
            '3' => 'poidsVraisemblance',
            '4' => 'poidsGravite',
            '5' => 'actions',
        ];
    }

    private function generateActionCell(MesureProtection $mesureProtection)
    {
        $editPath = $this->router->generate('aipd_mesure_protection_list', ['id' => $mesureProtection->getId()]);

        return '<a href="' .
                 $editPath . '">
            <i class="fa fa-pencil-alt"></i>' .
            $this->translator->trans('action.edit') .
            '</a>
            <a href="' .
//            $this->router->generate('aipd_mesure_protection_delete', ['id' => $mesureProtection->getId()]) .
            '"><i class="fa fa-trash"></i>' .
            $this->translator->trans('action.delete') .
            '</a>'
        ;
    }
}
