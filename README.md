# Simple Support
## Установка
`composer require nikservik/simple-support`
`vendor:publish --provider="Nikservik\SimpleSupport\SimpleSupportServiceProvider" --tag=migrations`
`php artisan migrate`

## Локализация и шаблоны страниц для админки
`vendor:publish --provider="Nikservik\SimpleSupport\SimpleSupportServiceProvider" --tag=views`
`vendor:publish --provider="Nikservik\SimpleSupport\SimpleSupportServiceProvider" --tag=translations`