<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\BbkkpSis;

use Illuminate\Database\Eloquent\Model;

/**
 * Class PublicSocialMedia
 * 
 * @property int $socmed_id
 * @property string|null $socmed_name
 * @property string|null $socmed_icon_cls
 * @property string|null $socmed_link
 * @property bool|null $socmed_status
 *
 * @package App\Models\BbkkpSis
 */
class PublicSocialMedia extends Model
{
	protected $table = 'public_social_media';
	protected $primaryKey = 'socmed_id';
	public $timestamps = false;

	protected $casts = [
		'socmed_status' => 'bool'
	];

	protected $fillable = [
		'socmed_name',
		'socmed_icon_cls',
		'socmed_link',
		'socmed_status'
	];
}
