<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\BbkkpSis;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class MasterEmailTemplate
 * 
 * @property int $template_id
 * @property string $template_uuid
 * @property string $template_code
 * @property string|null $template_desc
 * @property string|null $template_mail_subject
 * @property string|null $template_mail_body
 * @property Carbon|null $template_created_at
 * @property Carbon|null $template_updated_at
 *
 * @package App\Models\BbkkpSis
 */
class MasterEmailTemplate extends Model
{
	protected $table = 'master_email_template';
	protected $primaryKey = 'template_id';
	public $timestamps = false;

	protected $dates = [
		'template_created_at',
		'template_updated_at'
	];

	protected $fillable = [
		'template_uuid',
		'template_code',
		'template_desc',
		'template_mail_subject',
		'template_mail_body',
		'template_created_at',
		'template_updated_at'
	];
}
