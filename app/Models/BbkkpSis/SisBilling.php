<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\BbkkpSis;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SisBilling
 * 
 * @property int $bill_id
 * @property int $cust_id
 * @property string|null $bill_nomor_billing
 * @property string|null $bill_file_spk
 * @property Carbon|null $bill_billing_date
 * @property Carbon|null $bill_due_date
 * @property string|null $bill_harus_lunas
 * @property string|null $bill_invoice_file
 * @property string|null $bill_invoice_desc
 * @property string|null $bill_payment_tipe
 * @property string|null $bill_payment_status
 * @property Carbon|null $bill_payment_date
 * @property string|null $bill_payment_file
 * @property string|null $bill_payment_note
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * 
 * @property SisPelanggan $sis_pelanggan
 * @property Collection|SisAuditTahap1[] $sis_audit_tahap1s
 * @property Collection|SisBillingItems[] $sis_billing_items
 * @property Collection|SisJadwal[] $sis_jadwals
 *
 * @package App\Models\BbkkpSis
 */
class SisBilling extends Model
{
	protected $table = 'sis_billing';
	protected $primaryKey = 'bill_id';

	protected $casts = [
		'cust_id' => 'int'
	];

	protected $dates = [
		'bill_billing_date',
		'bill_due_date',
		'bill_payment_date'
	];

	protected $fillable = [
		'cust_id',
		'bill_nomor_billing',
		'bill_file_spk',
		'bill_billing_date',
		'bill_due_date',
		'bill_harus_lunas',
		'bill_invoice_file',
		'bill_invoice_desc',
		'bill_payment_tipe',
		'bill_payment_status',
		'bill_payment_date',
		'bill_payment_file',
		'bill_payment_note'
	];

	public function sis_pelanggan()
	{
		return $this->belongsTo(SisPelanggan::class, 'cust_id');
	}

	public function sis_audit_tahap1s()
	{
		return $this->hasMany(SisAuditTahap1::class, 'bill_id');
	}

	public function sis_billing_items()
	{
		return $this->hasMany(SisBillingItems::class, 'bill_id');
	}

	public function sis_jadwals()
	{
		return $this->hasMany(SisJadwal::class, 'bill_id');
	}
}
