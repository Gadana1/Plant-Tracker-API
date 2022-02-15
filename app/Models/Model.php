<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model as BaseModel;

/**
 * @mixin \Illuminate\Database\Eloquent\Builder
 *
 * @method static static make(array $attributes = [])
 * @method static static create(array $attributes = [])
 * @method static static forceCreate(array $attributes)
 * @method static updateOrCreate(array $attributes, array $values = [])
 *
 * @method static firstOrNew(array $attributes = [], array $values = [])
 * @method static firstOrFail($columns = ['*'])
 * @method static firstOrCreate(array $attributes, array $values = [])
 * @method static firstOr($columns = ['*'], \Closure $callback = null)
 * @method static firstWhere($column, $operator = null, $value = null, $boolean = 'and')
 * @method static|null first($columns = ['*'])
 *
 * @method static static findOrFail($id, $columns = ['*'])
 * @method static static findOrNew($id, $columns = ['*'])
 * @method static static|null find($id, $columns = ['*'])
 * @method static \Illuminate\Database\Eloquent\Collection findMany($ids, $columns = ['*'])
 *
 * @method static static[] all()
 * @method static static[]|\Illuminate\Database\Eloquent\Collection get()
 *
 * @method static \Illuminate\Database\Eloquent\Builder|static query()
 * @method static \Illuminate\Database\Eloquent\Builder where($column, $operator = null, $value = null, $boolean = 'and')
 */
class Model extends BaseModel
{
    use HasFactory;

    /**
     * Create a new Eloquent model instance.
     *
     * @param  array  $attributes
     * @return void
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->perPage = config('app.pagination_count');
    }
}
