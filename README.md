# Simple Support

Пакет реализует диалоги с пользователями и отправку уведомлений сразу всем пользователям.

## Установка

Добавить в `composer.json`
```bash
    "require": {
        ...
        "nikservik/simple-support": "^2.0",
        ...
    },
    "config": {
        ...
        "github-oauth": {
            "github.com": "токен доступа (создается в настройках)"
        }
    },
    "repositories" : [
        {
            "type": "vcs",
            "url" : "git@github.com:nikservik/simple-support.git"
        }
    ]
```
После этого выполнить 
```bash
composer update
```
### Миграции

Миграции можно опубликовать 
```bash
php artisan vendor:publish --tag="simple-support-migrations"
```
Или раскомментировать фичу `autoload-migrations` в конфигурации. 

Выполнить миграции:
```bash
php artisan migrate
```

### Конфигурация

Опубликовать файл конфигурации:
```bash
php artisan vendor:publish --tag="simple-support-config"
```

Содержимое файла конфигурации по умолчанию:
```php
    // чтобы отключить любую возможность, достаточно ее закомментировать
    'features' => [
        'user-can-send-message',
        'user-can-update-message',
        'user-can-delete-message',
        'send-notifications-to-telegram',
//        'register-api-routes',
//        'autoload-migrations',
    ],

    // без / в начале и в конце
    'route' => 'support',

    // сколько сообщений загружается одним запросом
    'messages-per-page' => 20,

    // метод подсчета непрочитанных сообщений
    // fast - одним запросом с тремя вложенными
    // simple - тремя запросами
    'unread-count' => 'simple',

    // настройки для отправки уведомлений о новых сообщениях от пользователей
    'telegram' => [
        'url' => 'https://api.telegram.org/bot',
        'token' => env('SUPPORT_BOT_TOKEN'),
        'chat' => env('SUPPORT_BOT_CHAT'),
    ],
```

В `.env` нужно добавить 2 настройки: 
идентификатор бота и идентификатор чата, в который он будет слать уведомления.
```bash
SUPPORT_BOT_TOKEN=
SUPPORT_BOT_CHAT=
```

## История изменений
### 2.03
- из выборки исключены сообщения и уведомления, созданные до регистрации пользователя
- 
### 2.02
- оптимизация countUnread

### 2.01
- В Actions вынесен метод jsonResponse
- asController возвращает значение, которое можно использовать во view
- Можно наследовать Actions и добавлять свой htmlResponse

### 2.0
- Добавлены общие уведомления в чат с пользователем
- Возможность отвечать на сообщение
- Отключаемые в конфигурации фичи
- Административная часть вынесена в пакет admin-support
- Полностью описан API в стандарте OpenAPI
- Добавлены фабрики
- Все действия переписаны на основе laravel-actions
- Полное покрытие тестами и standalone-тестирование
- Описание установки

