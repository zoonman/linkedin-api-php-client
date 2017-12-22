Клиент для работы с LinkedIn API с авторизацией через OAuth 2 написанный на PHP
============================================================
[![Build Status](https://travis-ci.org/zoonman/linkedin-api-php-client.svg?branch=master)](https://travis-ci.org/zoonman/linkedin-api-php-client) [![Code Climate](https://codeclimate.com/github/zoonman/linkedin-api-php-client/badges/gpa.svg)](https://codeclimate.com/github/zoonman/linkedin-api-php-client) [![Packagist](https://img.shields.io/packagist/dt/zoonman/linkedin-api-php-client.svg)](https://packagist.org/packages/zoonman/linkedin-api-php-client) [![GitHub license](https://img.shields.io/github/license/zoonman/linkedin-api-php-client.svg)](https://github.com/zoonman/linkedin-api-php-client/blob/master/LICENSE.md)



Чтобы быстрее вникнуть, смотри [пример использования](examples/) внутри [index.php](examples/index.php).


## Установка

Установка делается через composer следующей командой

```bash
composer require zoonman/linkedin-api-php-client
```

Также можно добавить `composer.json`.

Если вы никогда им не пользовались, познакомьтесь на [этой страничке](http://www.phptherightway.com/#composer_and_packagist)
и установите composer.


## Использование клиента

Чтобы начать работать с LinkedIn API, потребуется раздобыть идентификатор клиента (client id) и его секретный ключ (secret). 

Получить их можно на [Портале разработчиков](https://developer.linkedin.com/), для этого зайдите в секцию мои приложения 
(My Apps).


#### Подключение к проекту

Установите пакет, там появится каталог vendor, в котором будет autoload.php - это автозагрузчик. 

```php
// ... подлкючить автозагрузчик
include_once __DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

// сделать класс доступным
use LinkedIn\Client;

// создать новый объект
$client = new Client(
    'LINKEDIN_APP_CLIENT_ID',  
    'LINKEDIN_APP_CLIENT_SECRET'
);
```

#### Получение локального адреса для перенаправления

Чтобы начать процесс аутентификации вам необходимо установить адрес для перенаправления.
Вы можете вызвать метод `getRedirectUrl()`, 

```php
$redirectUrl = $client->getRedirectUrl();
```

Вам нужно будет сохранить этот адрес во временное хранилище для текущей сессии.
Вам потребуется этот адрес снова, когда вы будете получать токен.

```php
$_SESSION['linkedin_redirect_url'] = $redirectUrl;
```

#### Установка собственного адреса возврата 

Вы также можете использовать `setRedirectUrl()`, чтобы установить свой обратный адрес.  
Не забудьте указать этот адрес в параметрах приложения.

```php
$client->setRedirectUrl('http://your.domain.tld/path/to/script/');
```

#### Получение адреса для аутентификации 

Для того, чтобы пройти аутентификацию, вам необходимо получить адрес в LinkedIn,
на который нужно перенаправить пользователя. 
Этот тот самый адрес, на котором пользователя спрашивают о подтвердении 
запрашиваемых прав доступа для приложения.

```php
// определить области доступа
$scopes = [
    'r_basicprofile',
    'r_emailaddress',
    'rw_company_admin',
    'w_share',
];
$loginUrl = $client->getLoginUrl($scopes); // получить адрес
```

Теперь нужно перенаправить пользователя на полученный адрес.

 
#### Получение токена 

Чтобы получить токен или маркер доступа, как его иногда называют, 
нужно установить обратный адрес ($redirectUrl), который вы сохранили в сессии.

А затем вызвать получение токена 

```php
$accessToken = $client->getAccessToken($_GET['code']);
```

#### Вызов API 

All API calls can be called through simple method:
Вызовы API происходят с помощью простого метода api(), 
который принимает 3 параметра: путь вызова, параметры и метод. 

```php
$profile = $client->api(
    'ENDPOINT',
    ['parameter name' => 'its value here'],
    'HTTP method like GET for example'
);
```

Есть два упрощенных вызова:

```php
// метод get
$client->get('путь', ['имя параметра' => 'значение']);

// метод post
$client->post('ENDPOINT', ['param' => 'value']);
```
#### Примеры

Получить информацию о профиле 

```php
$profile = $client->get(
    'people/~:(id,email-address,first-name,last-name)'
);
print_r($profile);
```

Получить список компаний, в которой владелец  токена - администратор.

```php
$profile = $client->get(
    'companies',
    ['is-company-admin' => true]
);
print_r($profile);
```

Опубликовать сообщение у себя на странице профиля

```php
$share = $client->post(
    'people/~/shares',
    [
        'comment' => 'Посмотри, какая классная библиотека для работы с LinkedIn!',
        'content' => [
            'title' => 'PHP Client for LinkedIn API',
            'description' => 'OAuth 2 flow, composer Package',
            'submitted-url' => 'https://github.com/zoonman/linkedin-api-php-client',
            'submitted-image-url' => 'https://github.com/fluidicon.png',
        ],
        'visibility' => [
            'code' => 'anyone'
        ]
    ]
);
```

Поделиться контентом на тестовой странице компаний

```php
// Вы можете увидеть сообщение на этой странице
// https://www.linkedin.com/company/devtestco
$companyId = '2414183'; // идентификатор страницы

$share = $client->post(
    'companies/' . $companyId . '/shares',
    [
        'comment' => 'Checkout this amazing PHP SDK for LinkedIn!',
        'content' => [
            'title' => 'PHP Client for LinkedIn API',
            'description' => 'OAuth 2 flow, composer Package',
            'submitted-url' => 'https://github.com/zoonman/linkedin-api-php-client',
            'submitted-image-url' => 'https://github.com/fluidicon.png',
        ],
        'visibility' => [
            'code' => 'anyone'
        ]
    ]
);
```

Установить заголовки по умолчанию

```php
$client->setApiHeaders([
  'Content-Type' => 'application/json',
  'x-li-format' => 'json',
  'x-li-src' => 'msdk' // например отправить "msdk" чтобы симулировать мобильное SDK
]);
```

Изменить корневой адрес для API вызовов

```php
$client->setApiRoot('https://api.linkedin.com/v2/');
```

## Помощь проекту

Если вы нашли ошибку и исправили ее, вы всегда можете открыть Pull Request. 
У нас есть небольшое требование к качеству кода.
Пожалуйста, следуйте стандарту [PSR](http://www.php-fig.org/psr/)  и пишите тесты PHPUnit для вносимых изменений. 

## Лицензия

[MIT](LICENSE.md) - вы имеете право использовать библиотеку без каких-либо отчислений.
Пожалуйста, указывайте ссылку на данный проекта в своих приложениях.
