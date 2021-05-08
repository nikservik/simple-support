<?php

return [
    // чтобы отключить любую возможность, достаточно ее закомментировать
    'features' => [
        'user-can-send-message',
        'user-can-update-message',
        'user-can-delete-message',
        'send-notifications-to-telegram',
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
];
