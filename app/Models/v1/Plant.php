<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\v1;

use App\Models\Model;
use App\Traits\Cachable;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Plant
 * 
 * @property int $id
 * @property string $name
 * @property string $species
 * @property string $image
 * @property string $instructions
 * @property string|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models
 */
class Plant extends Model
{
	use SoftDeletes;
	use Cachable;

	protected $table = 'plants';
	protected $perPage = 20;

	protected $fillable = [
		'name',
		'species',
		'image',
		'instructions'
	];
}
