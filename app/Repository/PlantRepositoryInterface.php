<?php

namespace App\Repository;

use Illuminate\Contracts\Pagination\Paginator;

interface PlantRepositoryInterface extends BaseRepositoryInterface {
    
    /**
     * Get paginated list of models with query.
     *
     * @param int $limit
     * @param string $query
     * @param array $columns
     * @param array $relations
     * @return Collection
     */
    public function paginateQuery($limit = null, $query = null, array $columns = ['*'], array $relations = []): Paginator;
}