<?php

namespace App\Application\Traits;

use App\Application\Doctrine\Repository\DataTablesRepository;
use App\Infrastructure\ORM\Registry\Repository\Treatment;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

trait ServersideDatatablesTrait
{
    /**
     * @var DataTablesRepository
     */
    protected $repository;

    /**
     * Get the label of the column corresponding to the specified key.
     *
     * @param string $key The column number of the column
     */
    protected function getCorrespondingLabelFromKey(string $key)
    {
        return \array_key_exists($key, $this->getLabelAndKeysArray()) ? $this->getLabelAndKeysArray()[$key] : null;
    }

    /**
     * The function called with AJAX by the datatables to retrieve data
     * Manage order, filters and so on.
     */
    abstract public function listDataTables(Request $request): JsonResponse;

    protected function getBaseDataTablesResponse(Request $request, $results, array $criteria = [])
    {
        $draw = $request->request->get('draw');

        $reponse = [
            'draw'            => $draw,
            'recordsTotal'    => $this->repository->count($criteria),
            'recordsFiltered' => count($results),
            'data'            => [],
        ];

        return $reponse;
    }

    protected function getResults(Request $request, array $criteria = []): ?Paginator
    {
        $first      = $request->request->get('start');
        $maxResults = $request->request->get('length');
        $orders     = $request->request->get('order');
        $columns    = $request->request->get('columns');

        $orderColumn = $this->getCorrespondingLabelFromkey($orders[0]['column']);
        $orderDir    = $orders[0]['dir'];

        $searches = [];
        foreach ($columns as $column) {
            if ('' !== $column['search']['value']) {
                $searches[$column['data']] = $column['search']['value'];
                if ($column['data'] === 'baseLegal' && $this->repository instanceof Treatment) {
                    $searches[$column['data']] = json_encode($column['search']['value']);
                }
            }
        }

        return $this->repository->findPaginated($first, $maxResults, $orderColumn, $orderDir, $searches, $criteria);
    }

    /**
     * Return an array containing the correspondance between key (column number)
     * and column label.
     */
    abstract protected function getLabelAndKeysArray(): array;
}
