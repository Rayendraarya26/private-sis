<!DOCTYPE html>
<html lang="id">
<style>
    .content {
        padding: 60px 70px;
    }

    @media only screen and (max-width: 600px) {
        .content {
            padding: 0 0 0 0;
        }
    }
</style>

<body
    style="background-color: #FFFFFF; font-size: 14px; line-height: 1.43; font-family: 'Helvetica Neue', 'Segoe UI', 'Helvetica', 'Arial', sans-serif;">
<div style="max-width: 100%; margin: 0; background-color: #fff; box-shadow: 0 20px 50px rgba(0,0,0,0.05);">
    <table style="width: 100%;">
        <tr>
            <td style="background-color: #fff;">
                <img alt="Logo" src="{{asset('images/logos/sis_logo.png')}}" style="width: 70px; padding: 20px">
            </td>
        </tr>
    </table>
    <div class="content" style="border-top: 1px solid rgba(0,0,0,0.05);">
        <h2 style="margin-top: 0;">
            Permohonan Sertifikasi
        </h2>
        <div style="color: #636363; font-size: 14px;">
            {{ $pemohonNama }} mengajukan permohonan sertifikasi {{ $pemohonSertifNama }} 
			<br>
			<div style="color: blue; font-size: 12px;">
				Mohon maaf karena beberapa alasan kami terpaksa menolak permohonan anda.
			</div>
            <br>
            <div style="color: #636363; font-size: 14px;">
                <a href="{{$link_verif}}"
                   style="padding: 8px 20px; background-color: #4B72FA; color: #fff; font-weight: bolder; font-size: 16px; display: inline-block; margin: 20px 0px; margin-right: 20px; text-decoration: none;">
                    Selengkapnya
                </a>
            </div>
        </div>

        <h4 style="margin-bottom: 10px;">
            Butuh bantuan ?
        </h4>
        <div style="color: #A5A5A5; font-size: 12px;">
            <p>
                Jika anda memiliki pertanyaan, anda dapat menghubungi kami melalui
                <a href="mailto:{{env('MAIL_FROM_ADDRESS')}}">email</a>
            </p>
        </div>
    </div>
    <div style="background-color: #F5F5F5; padding: 40px; text-align: center;">


        <div style="color: #A5A5A5; font-size: 12px; margin-bottom: 20px; padding: 0 50px;">
            Anda mendapatkan email ini secara otomatis dari
            <a href="{{env("APP_URL")}}"><b>{{env("APP_NAME")}}</b></a>
        </div>
        <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid rgba(0,0,0,0.05);">
            <div style="color: #A5A5A5; font-size: 10px; margin-bottom: 5px;">
                {{env("APP_ADDRESS")}}
            </div>
        </div>
    </div>
</div>
</body>
</html>
