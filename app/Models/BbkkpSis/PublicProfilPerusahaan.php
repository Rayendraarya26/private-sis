<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\BbkkpSis;

use Illuminate\Database\Eloquent\Model;

/**
 * Class PublicProfilPerusahaan
 * 
 * @property int $profil_id
 * @property string|null $profil_fullname_perusahaan
 * @property string|null $profil_shortname_perusahaan
 * @property string|null $profil_desc_perusahaan
 * @property string|null $profil_alamat_perusahaan
 * @property string|null $profil_email_perusahaan
 * @property string|null $profil_fax_perusahaan
 * @property string|null $profil_telp_perusahaan
 * @property string|null $profil_whatsapp_perusahaan
 * @property string|null $profil_fullname_app
 * @property string|null $profil_shortname_app
 * @property string|null $profil_app_icon
 * @property string|null $profil_app_desc
 * @property string|null $profil_background_image
 * @property string|null $profil_ketidakperpihakan_file
 *
 * @package App\Models\BbkkpSis
 */
class PublicProfilPerusahaan extends Model
{
	protected $table = 'public_profil_perusahaan';
	protected $primaryKey = 'profil_id';
	public $timestamps = false;

	protected $fillable = [
		'profil_fullname_perusahaan',
		'profil_shortname_perusahaan',
		'profil_desc_perusahaan',
		'profil_alamat_perusahaan',
		'profil_email_perusahaan',
		'profil_fax_perusahaan',
		'profil_telp_perusahaan',
		'profil_whatsapp_perusahaan',
		'profil_fullname_app',
		'profil_shortname_app',
		'profil_app_icon',
		'profil_app_desc',
		'profil_background_image',
		'profil_ketidakperpihakan_file'
	];
}
