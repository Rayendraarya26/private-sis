<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\BbkkpSis;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class MasterIcons
 * 
 * @property int $icon_id
 * @property string|null $icon_name
 * @property Carbon|null $icon_created_at
 * @property Carbon|null $icon_updated_at
 *
 * @package App\Models\BbkkpSis
 */
class MasterIcons extends Model
{
	protected $table = 'master_icons';
	protected $primaryKey = 'icon_id';
	public $timestamps = false;

	protected $dates = [
		'icon_created_at',
		'icon_updated_at'
	];

	protected $fillable = [
		'icon_name',
		'icon_created_at',
		'icon_updated_at'
	];
}
