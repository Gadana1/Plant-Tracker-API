<?php

namespace App\Repository\Eloquent;

use App\Models\v1\Plant;
use App\Repository\PlantRepositoryInterface;
use Illuminate\Contracts\Pagination\Paginator;

class PlantRepository extends BaseRepository implements PlantRepositoryInterface
{

    /**
     * BaseRepository constructor.
     *
     * @param BaseModel $model
     */
    public function __construct(Plant $model)
    {
        $this->model = $model;
    }

    /**
     * @param int $limit
     * @param string $query
     * @param array $columns
     * @param array $relations
     * @return Collection
     */
    public function paginateQuery($limit = null, $query = null, array $columns = ['*'], array $relations = []): Paginator
    {
        if (!empty($query) && $list = explode(' ', $query)) {
            $builder = $this->model->with($relations);
            foreach ($list as $item) {
                $item = trim($item);
                $builder = $builder->orWhere('name', 'LIKE', "%$item%")
                    ->orWhere('species', 'LIKE', "%$item%");
            }
            return $builder->oldest()->simplePaginate($limit ?: $this->model->perPage, $columns);
        }
        return $this->model->with($relations)->latest()->simplePaginate($limit ?: $this->model->perPage, $columns);

    }
}
