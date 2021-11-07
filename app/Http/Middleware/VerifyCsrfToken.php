<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        'email/template/ajax',
        'marketing/verifikasi-permohonan/ajax',
        'master/sis/sertifikasi/ajax',
        'timaudit/auditor/tahap1/ajax',
        'pelanggan/audit/ajax'
    ];
}
