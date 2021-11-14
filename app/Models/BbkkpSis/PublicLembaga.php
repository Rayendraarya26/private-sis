<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\BbkkpSis;

use Illuminate\Database\Eloquent\Model;

/**
 * Class PublicLembaga
 * 
 * @property int $lem_id
 * @property string|null $lem_name
 * @property string|null $lem_desc
 * @property string|null $lem_content
 * @property string|null $lem_external_link
 * @property bool|null $lem_status
 *
 * @package App\Models\BbkkpSis
 */
class PublicLembaga extends Model
{
	protected $table = 'public_lembaga';
	protected $primaryKey = 'lem_id';
	public $timestamps = false;

	protected $casts = [
		'lem_status' => 'bool'
	];

	protected $fillable = [
		'lem_name',
		'lem_desc',
		'lem_content',
		'lem_external_link',
		'lem_status'
	];
}
