<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400"></a></p>

## About Project

Sistem infromasi sertifikat menggunkan laravel 8 dengan php 8


### Tutorial

1. clone project ini dengan `git clone `
2. masuk pada folder project jalankan `composer install`
3. masuk ke folder public > jalankan `yarn` (pastikan sudah punya yarn)
4. jalankan `php artisan key:generate`
5. copy `.env.example` menjadi `.env`
6. setting database connection dan mail configuration (pakai mailtrap.io)
    1. Buat database bernama bbkkp_sil
    2. Buat database bernama bbkkp_sil_log
    3. Buat akun <a href="https://mailtrap.io" >mailtrap.io</a> (untuk testing email)
7. jalankan `php artisan migrate:refresh --seed` untuk membuat tabel dan data awal
8. jalankan `php artisan serve`
9. buka terminal/cmd lagi jalankan `php artisan queue:work`
10. Login dengan akun
    1. email: kemal@mailinator.com
    2. paswd: 2104
    
