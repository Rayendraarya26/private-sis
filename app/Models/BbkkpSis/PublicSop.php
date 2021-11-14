<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\BbkkpSis;

use Illuminate\Database\Eloquent\Model;

/**
 * Class PublicSop
 * 
 * @property int $sop_id
 * @property string|null $sop_name
 * @property string|null $sop_desc
 * @property string|null $sop_image
 * @property bool|null $sop_status
 *
 * @package App\Models\BbkkpSis
 */
class PublicSop extends Model
{
	protected $table = 'public_sop';
	protected $primaryKey = 'sop_id';
	public $timestamps = false;

	protected $casts = [
		'sop_status' => 'bool'
	];

	protected $fillable = [
		'sop_name',
		'sop_desc',
		'sop_image',
		'sop_status'
	];
}
