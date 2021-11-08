<!DOCTYPE html>
<html lang="en">
<head>

    <!-- Meta tags -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Sistem Informasi Sertifikasi Balai Besar Kulit dan Karet">
    <meta name="keywords" content="{{env('APP_NAME')}}">
    <!-- /meta tags -->
    <title>@yield('title') | {{env('APP_NAME')}}</title>

    <!-- Site favicon -->
    <link rel="shortcut icon" href="{{asset('images/icon/favicon-32x32-manifest-31222.png')}}" type="image/x-icon">
    <!-- /site favicon -->

    <!-- Font Icon Styles -->
    <link rel="stylesheet" href="{{asset('node_modules/flag-icon-css/css/flag-icon.min.css')}}">
    <link rel="stylesheet" href="{{asset('vendors/gaxon-icon/style.css')}}">
    <!-- /font icon Styles -->

    <!-- Perfect Scrollbar stylesheet -->
    <link rel="stylesheet" href="{{asset('node_modules/perfect-scrollbar/css/perfect-scrollbar.css')}}">
    <!-- /perfect scrollbar stylesheet -->

    <!-- Load Styles -->

    <link rel="stylesheet" href="{{asset('assets/css/lite-style-1.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/fontawesome/css/all.min.css')}}">
    <!-- /load styles -->

    <script>
        window.APP_URL = `{{env('APP_URL')}}`
    </script>
    @stack("css")
</head>
<body class="dt-sidebar--fixed dt-header--fixed">

<!-- Loader -->
<div class="dt-loader-container">
    <div class="dt-loader">
        <svg class="circular" viewBox="25 25 50 50">
            <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"></circle>
        </svg>
    </div>
</div>
<!-- /loader -->

<!-- Root -->
<div class="dt-root">
    <!-- Login Container -->
    <div class="dt-login--container dt-app-login--container">
        <!-- Login Content -->
    @yield('content')
    <!-- /login content -->
    </div>
    <!-- /login container -->
</div>
<!-- /root -->

<!-- Optional JavaScript -->
<script src="{{asset('node_modules/jquery/dist/jquery.min.js')}}"></script>
<script src="{{asset('node_modules/moment/moment.js')}}"></script>
<script src="{{asset('node_modules/bootstrap/dist/js/bootstrap.bundle.min.js')}}"></script>
<!-- Perfect Scrollbar jQuery -->
<script src="{{asset('node_modules/perfect-scrollbar/dist/perfect-scrollbar.min.js')}}"></script>
<!-- /perfect scrollbar jQuery -->

<!-- masonry script -->
<script src="{{asset('node_modules/masonry-layout/dist/masonry.pkgd.min.js')}}"></script>
<script src="{{asset('node_modules/sweetalert2/dist/sweetalert2.js')}}"></script>

<!-- Custom JavaScript -->
<script src="{{asset('assets/js/script.js')}}"></script>
<script src="{{asset('assets/fontawesome/js/all.min.js')}}"></script>

<script src="{{ asset('assets/js/dexie.min.js') }}"></script>

<script>
    window.idb = new Dexie("bbkkp_sis");
    window.idb.version(16).stores({
        pelanggan_permohonan: "++id, &name, value",
        pelanggan_permohonan_komoditas: "++id, komoditi_id, komoditi_nama, sni, merk, tipe, ukuran, produksi_tahunan, satuan_produksi",
        bill_data: "++id, &name, value",
		bill_data_itms: "++id, bil_tipe, mohon_id, bil_desc, bil_total, bil_lunas",
		jadwal_data: "++id, &name, tanggal_mulai, tanggal_selesai, jenis, cust_id, bill_id",
		jadwal_data_itms: "++id, jenis, mohon_id, sert_id, sert_nama, komodt_id, komodt_nama, cust_sert_id, nomor_sertifikat, nomor_referensi, kode_nace, kode_ea, standart_acuan, ruang_lingkup, kegiatan, tujuan_audit, sni, merk, tipe, ukuran, kapasitas_produksi, satuan, mohon_komoditi_id",
		tahap1_data: "++id, &name, tanggal_mulai, tanggal_selesai, bill_id, cust_id, mohon_id, tujuan",
		tahap1_data_tim: "++id, peg_id, peg_nama, kode, posisi",
    });

    idb.pelanggan_permohonan.clear();
    idb.pelanggan_permohonan_komoditas.clear();
    idb.bill_data.clear();
    idb.bill_data_itms.clear();
    idb.jadwal_data.clear();
    idb.jadwal_data_itms.clear();
    idb.tahap1_data.clear();
    idb.tahap1_data_tim.clear();

</script>

{{--<script src="https://kit.fontawesome.com/68c3e4b5b2.js"></script>--}}
@stack('javascript')
</body>
</html>
