# Simple Support
## Установка
`composer require nikservik/simple-support`
`php artisan vendor:publish --provider="Nikservik\SimpleSupport\SimpleSupportServiceProvider" --tag=migrations`
`php artisan migrate`

## Локализация и шаблоны страниц для админки
`php artisan vendor:publish --provider="Nikservik\SimpleSupport\SimpleSupportServiceProvider" --tag=views`
`php artisan vendor:publish --provider="Nikservik\SimpleSupport\SimpleSupportServiceProvider" --tag=translations`