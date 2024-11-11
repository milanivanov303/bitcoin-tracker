<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Bitcoin Tracker app
Application to track bitcoin price using Bitfinex public API.

Some project details:
1. Project is Laravel 11; Using Vue and Inertia
2. To run the project - 'php artisan serve', 'npm run dev'
3. Subscription is allowed to fill different than user's email
4. Data for bitcoin prices is not stored in db 
5. Subscription is only for USD

Documentation
- [docs](http://localhost:8000/docs).

Tests
1. Run tests - 'php artisan test'
2. Logs of tests 
    - /storage/logs/laravel.log
    - /storage/logs/price_alerts.log
    - /storage/logs/percent_alerts.log
