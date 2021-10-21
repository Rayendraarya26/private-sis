<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="description" content="Sistem Informasi Sertifikasi Balai Besar Kulit dan Karet">
    <meta name="keywords" content="{{env('APP_NAME')}}">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title') | {{env('APP_NAME')}}</title>

    <!-- Site favicon -->
    <link rel="shortcut icon" href="{{ asset('images/icon/favicon-16x16-manifest-31222.png') }}">
    <!-- /site favicon -->

    <link href="{{ asset('assets/landing/css/bootstrap.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/landing/css/landing-page.css') }}" rel="stylesheet"/>

    <!--     Fonts and icons     -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" integrity="sha512-SfTiTlX6kk+qitfevl/7LibUOeJWlt9rbyDn92a1DqWOw9vWG2MFoays0sgObmWazO5BQPiFucnnEAjpAB+/Sw==" crossorigin="anonymous" />
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:300italic,400,300' rel='stylesheet' type='text/css'>
    <link href="{{ asset('assets/landing/css/pe-icon-7-stroke.css') }}" rel="stylesheet" />

    <style>
        /*.table-responsive {*/
        /*    overflow-x: hidden !important;*/
        /*}*/

        .dt-side-nav__header {
            padding-top: 0 !important;
        }

        /*.dt-side-nav__item.open>a{*/
        /*    color: #fa8c16;*/
        /*}*/

        @media screen and (max-width: 991px) {
            .dt-side-nav__header {
                padding-top: 20px !important;
            }
        }

        .custom-cooltipz {
            --cooltipz-font-size: 10px;
        }
    </style>

    @stack('css')
</head>

<body class="landing-page landing-page1">
    <nav class="navbar navbar-transparent navbar-top" role="navigation">
        <div class="container">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <button id="menu-toggle" type="button" class="navbar-toggle" data-toggle="collapse" data-target="#example">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar bar1"></span>
                <span class="icon-bar bar2"></span>
                <span class="icon-bar bar3"></span>
                </button>
                <a href="#">
                    <div class="logo-container">
                        <div class="">

                            <img height="80" width="auto" style="margin-bottom: 10px; margin-left: 10px;" src="{{asset('images/logos/sis_logo.png')}}" alt="Logo {{env('app_name')}}">
                        </div>
                    </div>
                </a>
            </div>
            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="example" >
                <ul class="nav navbar-nav navbar-right">
                    <li>
                        <a href="#sop">
                            <i class="fa fa-book"></i>&nbsp;&nbsp;SOP Sertifikasi
                        </a>
                    </li>
                    @if(auth()->check())
                        <li>
                            <a href="{{ url('dashboard') }}">
                                <i class="fa fa-sign-in"></i>&nbsp;&nbsp;Dashboard
                            </a>
                        </li>
                    @else
                        <li>
                            <a href="{{ url('auth/register') }}">
                                <i class="fa fa-user-plus"></i>&nbsp;&nbsp;Daftar
                            </a>
                        </li>
                        <li>
                            <a href="{{ url('auth/login') }}">
                                <i class="fa fa-sign-in"></i>&nbsp;&nbsp;Login
                            </a>
                        </li>
                    @endif
                </ul>
            </div>
            <!-- /.navbar-collapse -->
        </div>
    </nav>
    <div class="wrapper" id="app">
        <div class="parallax filter-gradient orange" data-color="orange" style="height: 100vh;">
            <!-- <div class="parallax-background">
                <img class="parallax-background-image" src="">
            </div> -->
            <div class= "container">
                <div class="row">
                    <div class="col-md-4 col-xs-12">
                        <div class="parallax-image">
                            <!-- <img src="assets/img/semarang_474.png" style="width: 20vw;"/> -->
                            <img src="{{asset('images/logos/sis_logo.png')}}" draggable="false" style="width: 20vw; background: url('assets/img/semarang_474_white.png') center bottom no-repeat; background-size: cover; background-position-x: 1px;"/>
                        </div>
                    </div>
                    <div class="col-md-7 col-md-offset-1">
                        <div class="description" style="margin-top: 150px;">
                            <h2>Sistem Infromasi Sertifikat</h2>
                            <h5>Balai Bersar Kulit, Karet dan Plastik</h5>
                            <br>
                            <h5>BBKKP memiliki 5 (lima) lembaga sertifikasi, yaitu:</h5>
                            <h5>
                                <ol>
                                    <li>
                                        <a href="#lembaga_1" style="color: white;">LSSM BBKKP YOQA</a>
                                    </li>
                                    <li>
                                        <a href="#lembaga_2" style="color: white;">LSPr BBKKP JPA</a>
                                    </li>
                                    <li>
                                        <a href="#lembaga_3" style="color: white;">LSSML BBKKP JECA</a>
                                    </li>
                                    <li>
                                        <a href="#lembaga_4" style="color: white;">LSIH BBKKP</a>
                                    </li>
                                    <li>
                                        <a href="#lembaga_5" style="color: white;">LSSMK3 BBKKP</a>
                                    </li>
                                </ol>
                            </h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <section id="lembaga_1" class="section section-gray section-clients">
            <div class="container">
                <h4 class="header-text" style="font-weight: 500;">LSSM BBKKP YOQA</h4>
                <p>Lembaga Sertifikasi Sistem Mutu BBKKP YOQA (LSSM BBKKP YOQA) telah mendapatkan akreditasi oleh Komite Akreditasi Nasional (KAN) sejak 12 Januari 1996. Ruang lingkup sertifikasi sistem manajemen mutu :</p>
                <ul>
                    <li>Industri kulit dan produk kulit</li>
                    <li>Produk karet dan plastik</li>
                    <li>Makanan, minuman dan tembakau</li>
                    <li>Kimia, produk kimia dan serat</li>
                    <li>Tekstil dan produk tekstil</li>
                </ul>
                <div style="margin-top: 16px;" class="w-100 text-right">
                    <a href="http://bbkkp.kemenperin.go.id/page/lssm-bbkkp-yoqa" rel="noopener noreferrer" target="_blank">
                        <h6>Lihat selengkapnya&nbsp;<i class="fa fa-arrow-right"></i></h6>
                    </a>
                </div>
            </div>
        </section>
        <section id="lembaga_2" class="section section-clients">
            <div class="container">
                <h4 class="header-text" style="font-weight: 500;">LSPr BBKKP JPA</h4>
                <p>Lembaga Sertifikasi Produk BBKKP JPA (LSPr BBKKP JPA) memberikan layanan sertifikasi produk kulit, karet dan plastik dengan tujuan memberikan kepastian mutu produk dengan mengacu pada Standar Nasional Indonesia (SNI). Ruang lingkup LSPr BBKKP JPA:</p>
                <ol type="A" class="text-sm" style="padding-inline-start: 20px;">
                    <li class="text-bold">
                        <h6>KELOMPOK PRODUK: PERALATAN PERLINDUNGAN</h6>
                        <table cellpadding="4" cellspacing="4" border="0" style="margin-bottom: 12px; width: 100%; font-size: 12px;">
                            <tbody>
                                <tr class="section-gray">
                                    <td style="padding: 3px; vertical-align: top;" colspan="3">
                                        <b>Sub Kelompok Produk: Pelindung kepala dan bagiannya (termasuk mata dan sistem pernafasan)</b>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 3px; width: 3%; vertical-align: top; text-align: center;">1.</td>
                                    <td style="padding: 3px; width: 27%; vertical-align: top;">SNI 1811-2007/amd1:2010</td>
                                    <td style="padding: 3px; width: 70%; vertical-align: top;">Helm pengendara kendaraan bermotor roda dua, Amandemen 1 Sub Kelompok Produk: Pelindung tangan dan kaki</td>
                                </tr>
                            </tbody>
                        </table>
                        <table cellpadding="4" cellspacing="4" border="0" style="margin-bottom: 12px; width: 100%; font-size: 12px;">
                            <tbody>
                                <tr class="section-gray">
                                    <td style="padding: 3px; vertical-align: top;" colspan="3">
                                        <b>Sub Kelompok Produk: Pelindung tangan dan kaki</b>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 3px; width: 3%; vertical-align: top; text-align: center;">2.</td>
                                    <td style="padding: 3px; width: 27%; vertical-align: top;">SNI 7037:2009</td>
                                    <td style="padding: 3px; width: 70%; vertical-align: top;">Sepatu pengaman dari kulit dengan sistem Goodyear welt</td>
                                </tr>
                                <tr>
                                    <td style="padding: 3px; width: 3%; vertical-align: top; text-align: center;">3.</td>
                                    <td style="padding: 3px; width: 27%; vertical-align: top;">SNI 7079:2009</td>
                                    <td style="padding: 3px; width: 70%; vertical-align: top;">Sepatu pengaman dari kulit dengan sol poliuretan dan termoplastik poliuretan sistem cetak injeksi</td>
                                </tr>
                                <tr>
                                    <td style="padding: 3px; width: 3%; vertical-align: top; text-align: center;">4.</td>
                                    <td style="padding: 3px; width: 27%; vertical-align: top;">SNI 0111:2009</td>
                                    <td style="padding: 3px; width: 70%; vertical-align: top;">Sepatu pengaman dari kulit dengan sol karet cetak vulkanisasi</td>
                                </tr>
                                <tr>
                                    <td style="padding: 3px; width: 3%; vertical-align: top; text-align: center;">5.</td>
                                    <td style="padding: 3px; width: 27%; vertical-align: top;">SNI 12-1848-2006</td>
                                    <td style="padding: 3px; width: 70%; vertical-align: top;">Sepatu bot PVC</td>
                                </tr>
                                <tr>
                                    <td style="padding: 3px; width: 3%; vertical-align: top; text-align: center;">6.</td>
                                    <td style="padding: 3px; width: 27%; vertical-align: top;">SNI 12-1548-1989</td>
                                    <td style="padding: 3px; width: 70%; vertical-align: top;">Sepatu bot PVC cetak tahan minyak dan lemak</td>
                                </tr>
                                <tr>
                                    <td style="padding: 3px; width: 3%; vertical-align: top; text-align: center;">7.</td>
                                    <td style="padding: 3px; width: 27%; vertical-align: top;">SNI 12-1547-2005</td>
                                    <td style="padding: 3px; width: 70%; vertical-align: top;">Sepatu bot PVC tahan kimia</td>
                                </tr>
                                <tr>
                                    <td style="padding: 3px; width: 3%; vertical-align: top; text-align: center;">8.</td>
                                    <td style="padding: 3px; width: 27%; vertical-align: top;">SNI 2942.1:2009</td>
                                    <td style="padding: 3px; width: 70%; vertical-align: top;">Sepatu - Kulit sistem lem - Bagian 1: Wanita</td>
                                </tr>
                                <tr>
                                    <td style="padding: 3px; width: 3%; vertical-align: top; text-align: center;">9.</td>
                                    <td style="padding: 3px; width: 27%; vertical-align: top;">SNI 2942.2:2009</td>
                                    <td style="padding: 3px; width: 70%; vertical-align: top;">Sepatu - Kulit sistem lem - Bagian 2: Pria</td>
                                </tr>
                            </tbody>
                        </table>
                        <hr/>
                    </li>
                    <li class="text-bold">
                        <h6>KELOMPOK PRODUK: PRODUK DAN KOMPONEN MEKANIK, DAN PERALATAN MESIN</h6>
                        <table cellpadding="4" cellspacing="4" border="0" style="margin-bottom: 12px; width: 100%; font-size: 12px;">
                            <tbody>
                                <tr class="section-gray">
                                    <td style="padding: 3px; vertical-align: top;" colspan="3">
                                        <b>Sub Kelompok Produk: Perangkat penyimpanan cairan</b>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 3px; width: 3%; vertical-align: top; text-align: center;">10.</td>
                                    <td style="padding: 3px; width: 27%; vertical-align: top;">SNI 7276:2014</td>
                                    <td style="padding: 3px; width: 70%; vertical-align: top;">Plastik - Tangki air silinder vertikal - Polietilena (PE)</td>
                                </tr>
                            </tbody>
                        </table>
                        <table cellpadding="4" cellspacing="4" border="0" style="margin-bottom: 12px; width: 100%; font-size: 12px;">
                            <tbody>
                                <tr class="section-gray">
                                    <td style="padding: 3px; vertical-align: top;" colspan="3">
                                        <b>Sub Kelompok Produk: Komponen pipa dan pipa fluida</b>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 3px; width: 3%; vertical-align: top; text-align: center;">11.</td>
                                    <td style="padding: 3px; width: 27%; vertical-align: top;">SNI 06-0084-2002</td>
                                    <td style="padding: 3px; width: 70%; vertical-align: top;">Pipa PVC untuk saluran air minum</td>
                                </tr>
                            </tbody>
                        </table>
                        <hr/>
                    </li>
                    <li class="text-bold">
                        <h6>KELOMPOK PRODUK: MAKANAN DAN MINUMAN</h6>
                        <table cellpadding="4" cellspacing="4" border="0" style="margin-bottom: 12px; width: 100%; font-size: 12px;">
                            <tbody>
                                <tr class="section-gray">
                                    <td style="padding: 3px; vertical-align: top;" colspan="3">
                                        <b>Sub Kelompok Produk: Kopi, teh, kakao</b>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 3px; width: 3%; vertical-align: top; text-align: center;">12.</td>
                                    <td style="padding: 3px; width: 27%; vertical-align: top;">SNI 3747:2013</td>
                                    <td style="padding: 3px; width: 70%; vertical-align: top;">Kakao bubuk</td>
                                </tr>
                                <tr>
                                    <td style="padding: 3px; width: 3%; vertical-align: top; text-align: center;">13.</td>
                                    <td style="padding: 3px; width: 27%; vertical-align: top;">SNI 2907:2008</td>
                                    <td style="padding: 3px; width: 70%; vertical-align: top;">Biji kopi</td>
                                </tr>
                            </tbody>
                        </table>
                        <table cellpadding="4" cellspacing="4" border="0" style="margin-bottom: 12px; width: 100%; font-size: 12px;">
                            <tbody>
                                <tr class="section-gray">
                                    <td style="padding: 3px; vertical-align: top;" colspan="3">
                                        <b>Sub Kelompok Produk: Minuman</b>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 3px; width: 3%; vertical-align: top; text-align: center;">14.</td>
                                    <td style="padding: 3px; width: 27%; vertical-align: top;">SNI 3053:2015</td>
                                    <td style="padding: 3px; width: 70%; vertical-align: top;">Air 70num dalam kemasan: Air mineral</td>
                                </tr>
                                <tr>
                                    <td style="padding: 3px; width: 3%; vertical-align: top; text-align: center;">15.</td>
                                    <td style="padding: 3px; width: 27%; vertical-align: top;">SNI 6241:2015</td>
                                    <td style="padding: 3px; width: 70%; vertical-align: top;">Air minum dalam kemasan: Air demineral</td>
                                </tr>
                            </tbody>
                        </table>
                        <table cellpadding="4" cellspacing="4" border="0" style="margin-bottom: 12px; width: 100%; font-size: 12px;">
                            <tbody>
                                <tr class="section-gray">
                                    <td style="padding: 3px; vertical-align: top;" colspan="3">
                                        <b>Sub Kelompok Produk: Gula, produk gula, pati</b>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 3px; width: 3%; vertical-align: top; text-align: center;">16.</td>
                                    <td style="padding: 3px; width: 27%; vertical-align: top;">SNI 3140.3:2010/amd1:2011</td>
                                    <td style="padding: 3px; width: 70%; vertical-align: top;">Gula kristal - Bagian 3: Putih</td>
                                </tr>
                            </tbody>
                        </table>
                        <hr/>
                    </li>
                    <li class="text-bold">
                        <h6>KELOMPOK PRODUK: PRODUK KACA DAN KERAMIK</h6>
                        <table cellpadding="4" cellspacing="4" border="0" style="margin-bottom: 12px; width: 100%; font-size: 12px;">
                            <tbody>
                                <tr class="section-gray">
                                    <td style="padding: 3px; vertical-align: top;" colspan="3">
                                        <b>Sub Kelompok Produk: Produk keramik dan produk sanitari berbahan dasar keramik</b>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 3px; width: 3%; vertical-align: top; text-align: center;">17.</td>
                                    <td style="padding: 3px; width: 27%; vertical-align: top;">SNI 7322:2008</td>
                                    <td style="padding: 3px; width: 70%; vertical-align: top;">Produk melamin - Perlengkapan makan dan minum</td>
                                </tr>
                            </tbody>
                        </table>
                        <hr/>
                    </li>
                    <li class="text-bold">
                        <h6>KELOMPOK PRODUK: PRODUK KARET DAN PLASTIK</h6>

                        <table cellpadding="4" cellspacing="4" border="0" style="margin-bottom: 12px; width: 100%; font-size: 12px;">
                            <tbody>
                                <tr class="section-gray">
                                    <td style="padding: 3px; vertical-align: top;" colspan="3">
                                        <b>Sub Kelompok Produk: Karet / SIR</b>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 3px; width: 3%; vertical-align: top; text-align: center;">18.</td>
                                    <td style="padding: 3px; width: 27%; vertical-align: top;">SNI 1903:2017</td>
                                    <td style="padding: 3px; width: 70%; vertical-align: top;">Karet alam – Spesifikasi teknis Standard Indonesian Rubber (SIR)</td>
                                </tr>
                            </tbody>
                        </table>

                        <table cellpadding="4" cellspacing="4" border="0" style="margin-bottom: 12px; width: 100%; font-size: 12px;">
                            <tbody>
                                <tr class="section-gray">
                                    <td style="padding: 3px; vertical-align: top;" colspan="3">
                                        <b>Sub Kelompok Produk: Produk berbahan dasar karet</b>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 3px; width: 3%; vertical-align: top; text-align: center;">19.</td>
                                    <td style="padding: 3px; width: 27%; vertical-align: top;">SNI 7213:2014</td>
                                    <td style="padding: 3px; width: 70%; vertical-align: top;">Selang karet untuk kompor gas LPG</td>
                                </tr>
                                <tr>
                                    <td style="padding: 3px; width: 3%; vertical-align: top; text-align: center;">20.</td>
                                    <td style="padding: 3px; width: 27%; vertical-align: top;">SNI 1843:2008/amd1:2011</td>
                                    <td style="padding: 3px; width: 70%; vertical-align: top;">Rol karet pengupas gabah</td>
                                </tr>
                                <tr>
                                    <td style="padding: 3px; width: 3%; vertical-align: top; text-align: center;">21.</td>
                                    <td style="padding: 3px; width: 27%; vertical-align: top;">SNI 06-0001-1987</td>
                                    <td style="padding: 3px; width: 70%; vertical-align: top;">Karet konvensional</td>
                                </tr>
                                <tr>
                                    <td style="padding: 3px; width: 3%; vertical-align: top; text-align: center;">22.</td>
                                    <td style="padding: 3px; width: 27%; vertical-align: top;">SNI 7655:2010</td>
                                    <td style="padding: 3px; width: 70%; vertical-align: top;">Karet perapat (rubber seal) pada katup tabung LPG</td>
                                </tr>
                                <tr>
                                    <td style="padding: 3px; width: 3%; vertical-align: top; text-align: center;">23.</td>
                                    <td style="padding: 3px; width: 27%; vertical-align: top;">SNI 0778:2009</td>
                                    <td style="padding: 3px; width: 70%; vertical-align: top;">Sol karet cetak</td>
                                </tr>
                                <tr>
                                    <td style="padding: 3px; width: 3%; vertical-align: top; text-align: center;">24.</td>
                                    <td style="padding: 3px; width: 27%; vertical-align: top;">SNI 12-1000-1989</td>
                                    <td style="padding: 3px; width: 70%; vertical-align: top;">Karpet karet</td>
                                </tr>
                                <tr>
                                    <td style="padding: 3px; width: 3%; vertical-align: top; text-align: center;">25.</td>
                                    <td style="padding: 3px; width: 27%; vertical-align: top;">SNI 0098:2012</td>
                                    <td style="padding: 3px; width: 70%; vertical-align: top;">Ban mobil penumpang</td>
                                </tr>
                                <tr>
                                    <td style="padding: 3px; width: 3%; vertical-align: top; text-align: center;">26.</td>
                                    <td style="padding: 3px; width: 27%; vertical-align: top;">SNI 0099:2012</td>
                                    <td style="padding: 3px; width: 70%; vertical-align: top;">Ban truk dan bus</td>
                                </tr>
                                <tr>
                                    <td style="padding: 3px; width: 3%; vertical-align: top; text-align: center;">27.</td>
                                    <td style="padding: 3px; width: 27%; vertical-align: top;">SNI 0100:2012</td>
                                    <td style="padding: 3px; width: 70%; vertical-align: top;">Ban truk ringan</td>
                                </tr>
                                <tr>
                                    <td style="padding: 3px; width: 3%; vertical-align: top; text-align: center;">28.</td>
                                    <td style="padding: 3px; width: 27%; vertical-align: top;">SNI 0101:2012</td>
                                    <td style="padding: 3px; width: 70%; vertical-align: top;">Ban sepeda motor</td>
                                </tr>
                                <tr>
                                    <td style="padding: 3px; width: 3%; vertical-align: top; text-align: center;">29.</td>
                                    <td style="padding: 3px; width: 27%; vertical-align: top;">SNI 6700:2012</td>
                                    <td style="padding: 3px; width: 70%; vertical-align: top;">Ban dalam kendaraan bermotor</td>
                                </tr>
                                <tr>
                                    <td style="padding: 3px; width: 3%; vertical-align: top; text-align: center;">30.</td>
                                    <td style="padding: 3px; width: 27%; vertical-align: top;">SNI 3768:2013</td>
                                    <td style="padding: 3px; width: 70%; vertical-align: top;">Ban vulkanisir</td>
                                </tr>
                                <tr>
                                    <td style="padding: 3px; width: 3%; vertical-align: top; text-align: center;">31.</td>
                                    <td style="padding: 3px; width: 27%; vertical-align: top;">SNI 06-3068-2006</td>
                                    <td style="padding: 3px; width: 70%; vertical-align: top;">Vulkani70t karet bantalan dermaga</td>
                                </tr>
                                <tr>
                                    <td style="padding: 3px; width: 3%; vertical-align: top; text-align: center;">32.</td>
                                    <td style="padding: 3px; width: 27%; vertical-align: top;">SNI 3967:2013</td>
                                    <td style="padding: 3px; width: 70%; vertical-align: top;">Spesifikasi dan metode uji bantalan karet (elastomer) untuk perletakan jembatan</td>
                                </tr>
                            </tbody>
                        </table>

                        <table cellpadding="4" cellspacing="4" border="0" style="margin-bottom: 12px; width: 100%; font-size: 12px;">
                            <tbody>
                                <tr class="section-gray">
                                    <td style="padding: 3px; vertical-align: top;" colspan="3">
                                        <b>Sub Kelompok Produk: Produk berbahan dasar plastik</b>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 3px; width: 3%; vertical-align: top; text-align: center;">33.</td>
                                    <td style="padding: 3px; width: 27%; vertical-align: top;">SNI 7582:2010</td>
                                    <td style="padding: 3px; width: 70%; vertical-align: top;">Terpal plastik untuk biji- bijian produk pertanian</td>
                                </tr>
                                <tr>
                                    <td style="padding: 3px; width: 3%; vertical-align: top; text-align: center;">34.</td>
                                    <td style="padding: 3px; width: 27%; vertical-align: top;">SNI 19-0057-1998</td>
                                    <td style="padding: 3px; width: 70%; vertical-align: top;">Karung tenun plastik poliolefin</td>
                                </tr>
                            </tbody>
                        </table>
                    </li>
                </ol>
                <div style="margin-top: 16px;" class="w-100 text-right">
                    <a href="http://bbkkp.kemenperin.go.id/page/lspr-bbkkp-jpa" rel="noopener noreferrer" target="_blank">
                        <h6>Lihat selengkapnya&nbsp;<i class="fa fa-arrow-right"></i></h6>
                    </a>
                </div>
            </div>
        </section>
        <section id="lembaga_3" class="section section-gray section-clients">
            <div class="container">
                <h4 class="header-text" style="font-weight: 500;">LSSML BBKKP JECA</h4>
                <p>Lembaga Sertifikasi Sistem Manajemen Lingkungan BBKKP JECA (LSSML BBKKP JECA) melaksanakan kegiatan sertifikasi ISO 14001:2015 dan telah terakreditasi oleh KAN. Ruang lingkup sertifikasi:</p>
                <ul>
                    <li>Industri kulit dan produk kulit</li>
                    <li>Produk karet dan plastik</li>
                    <li>Makanan, minuman dan tembakau</li>
                    <li>Kimia, produk kimia dan serat</li>
                </ul>
                <div style="margin-top: 16px;" class="w-100 text-right">
                    <a href="http://bbkkp.kemenperin.go.id/page/lssml-bbkkp-jeca" rel="noopener noreferrer" target="_blank">
                        <h6>Lihat selengkapnya&nbsp;<i class="fa fa-arrow-right"></i></h6>
                    </a>
                </div>
            </div>
        </section>
        <section id="lembaga_4" class="section section-clients">
            <div class="container">
                <h4 class="header-text" style="font-weight: 500;">LSIH BBKKP</h4>
                <p>Lembaga Sertifikasi Industri Hijau BBKKP (LSIH BBKKP) ditunjuk Kementerian Perindustrian Republik Indonesia berdasarkan Permenperin RI Nomor 41/M-IND/PER/12/2017 tentang Lembaga Sertifikasi Industri Hijau. Dalam menyelenggarakan kegiatan Sertifikasi Industri Hijau dan menerbitkan Sertifikat Industri Hijau, LSIH BBKKP mengacu pada Standar Industri Hijau (SIH). Ruang lingkup sertifikasi industri hijau:</p>
                <ul>
                    <li>Karet Remah / Crumb Rubber</li>
                    <li>Pengasapan Karet (RSS)</li>
                    <li>Penyamakan Kulit</li>
                </ul>
                <div style="margin-top: 16px;" class="w-100 text-right">
                    <a href="http://bbkkp.kemenperin.go.id/page/lsih-bbkkp" rel="noopener noreferrer" target="_blank">
                        <h6>Lihat selengkapnya&nbsp;<i class="fa fa-arrow-right"></i></h6>
                    </a>
                </div>
            </div>
        </section>
        <section id="lembaga_5" class="section section-gray section-clients">
            <div class="container">
                <h4 class="header-text" style="font-weight: 500;">LSSMK3 BBKKP</h4>
                <p>Lembaga Sertifikasi Sistem Manajemen Kesehatan dan Keselamatan Kerja BBKKP (LSSMK3 BBKKP) telah mendapatkan akreditasi oleh Komite Akreditasi Nasional (KAN) sejak 25 Agustus 2021 dengan nomor akreditasi LSSMK3-009-IDN. Ruang lingkup sertifikasi sistem manajemen K3:</p>
                <ul>
                    <li>Karet</li>
                    <li>Produk Plastik</li>
                </ul>
                <div style="margin-top: 16px;" class="w-100 text-right">
                    <a href="http://bbkkp.kemenperin.go.id/page/lssmk3-bbkkp" rel="noopener noreferrer" target="_blank">
                        <h6>Lihat selengkapnya&nbsp;<i class="fa fa-arrow-right"></i></h6>
                    </a>
                </div>
            </div>
        </section>
        <div class="section section-clients">
            <div class="container text-center">
                <h5>Pernyataan Ketidakberpihakan Lembaga Sertifikasi BBKKP</h5>
                <a href="http://bbkkp.kemenperin.go.id/storage/files/2017_Pernyataan_Ketidakberpihakan_%20Ka_BBKKP.pdf" target="_blank" rel="noopener noreferrer" class="btn btn-primary">
                    <i class="fa fa-download"></i>&nbsp;Download
                </a>
            </div>
        </div>
        <section id="sop" class="section section-gray section-presentation">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="description text-left">
                            <h4 class="header-text" style="font-weight: 500;">
                                <i class="fa fa-book"></i>&nbsp;&nbsp;SOP Sertifikasi
                            </h4>
                            <hr>
                            <h6>Jasa Sertifikasi SMM ISO 9001, SML ISO 14001, SMK3 ISO 45001, dan Sertifikasi Industri Hijau</h6>
                            <p style="margin-top: 0;">
                                Waktu Penyelesaian : 41 Hari Kerja. Waktu penyelesaian, di luar waktu tindakan koreksi LKS Tinjauan Manual (Audit Tahap I) dan Audit Lapangan (Audit Tahap II)
                            </p>
                            <a href="#gambar_11">Lihat gambar 1.1</a>
                            <hr>
                            <h6>Jasa Sertifikasi Produk</h6>
                            <p style="margin-top: 0;">Waktu Penyelesaian : 41 Hari Kerja. Waktu penyelesaian, di luar waktu tindakan koreksi LKS, waktu pengiriman contoh uji dan proses pengujian</p>
                            <a href="#gambar_12">Lihat gambar 1.2</a>
                            <hr>
                            <h6>Audit dan Evaluasi</h6>
                            <a href="#gambar_13">Lihat gambar 1.3</a>
                            <hr>
                            <div class="row">
                                <div class="col-md-4 text-center">
                                    <section id="gambar_11">
                                        <span class="text-primary">Gambar 1.1</span>
                                        <br>
                                        <br>
                                        <div class="w-100 text-center">
                                            <a target="_blank" rel="noopener noreferrer" href="{{asset('assets/images/JECA.png')}}">
                                                <img alt="" src="{{asset('assets/images/JECA.png')}}" draggable="false" style="width: 20vh;"/>
                                            </a>
                                        </div>
                                    </section>
                                    <hr>
                                </div>
                                <div class="col-md-4 text-center">
                                    <section id="gambar_12">
                                        <span class="text-primary">Gambar 1.2</span>
                                        <br>
                                        <br>
                                        <div class="w-100 text-center">
                                            <a target="_blank" rel="noopener noreferrer" href="{{asset('assets/images/JPA.png')}}">
                                                <img alt="" src="{{asset('assets/images/JPA.png')}}" draggable="false" style="width: 30vh;"/>
                                            </a>
                                        </div>
                                    </section>
                                    <hr>
                                </div>
                                <div class="col-md-4 text-center">
                                    <section id="gambar_13">
                                        <span class="text-primary">Gambar 1.3</span>
                                        <br>
                                        <br>
                                        <div class="w-100 text-center">
                                            <a target="_blank" rel="noopener noreferrer" href="{{asset('assets/images/SOP.png')}}"">
                                                <img alt="" src="{{asset('assets/images/SOP.png')}}" draggable="false" style="width: 35vh;"/>
                                            </a>
                                        </div>
                                    </section>
                                    <hr>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <div class="section section-clients" style="padding-top: 20px; padding-bottom: 20px;">
            <div class="container">
                © Hak Cipta Balai Besar Kulit dan Karet {{ date('Y') }}
            </div>
        </div>
    </div>

</body>

<script src="{{ asset('assets/landing/js/jquery-1.10.2.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/landing/js/jquery-ui-1.10.4.custom.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/landing/js/bootstrap.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/landing/js/awesome-landing-page.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/landing/js/jquery-3.3.1.min.js') }}" type="text/javascript"></script>
@stack('javascript')

</html>
