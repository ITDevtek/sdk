# Devtek PHP SDK
Данное SDK упрощает работу с API Devtek. С помощью него вы можете отправлять лиды более легко, чем если бы вы делали это обычным образом. Вам не нужно писать код для отправки запросов самостоятельно, а также самостоятельно разбирать полученный ответ.

## Установка

1. Добавьте репозиторий с SDK в файл `composer.json` (свойство `repositories`) вашего проекта;

   ```json
   {
        "repositories": [
            {
                "type": "git",
                "url": "https://github.com/devtek-io/php-sdk.git"
            }
        ]
   }
   ```

2. После этого установите SDK;

   ```sh
   composer require devtek-io/php-sdk
   ```

3. Затем создайте экземпляр класса SDK укажите ID вебмастера и токен, который вы получили от менеджера, с помощью метода `setCredential()`.

   ```php
    use devtek\sdk\Devtek;

    $devtek = new Devtek;
    $devtek->setCredential(Devtek::CREDENTIAL_WEBMASTER_ID, 1); // ID вебмастера
    $devtek->setCredential(Devtek::CREDENTIAL_WEBMASTER_TOKEN, 'test'); // Токен
    ```

## ГЕО
### Получение регионов
Для получения регионов необходимо использовать метод `regions()`. Данный метод возвращает массив моделей [Region](/src/models/Region.php).

```php
$regions = $devtek->regions();
```

### Получение городов
Для получения городов необходимо использовать метод `cities()`. Данный метод возвращает массив моделей [City](/src/models/City.php).

```php
$allCities = $devtek->cities();
```

Если необходимо получить список городов определённого региона, то в метод `cities()` можно передать числовой ID региона или модель [Region](/src/models/Region.php). В случае, когда передан ID или модель региона, SDK вернёт список городов указанного региона. И это также будет массив моделей [City](/src/models/City.php).

```php
// ID 40 - Московская область
$moscowCities = $devtek->cities(40); // Передаём ID
```

### Получение региона или города по ID
Если нужно получить определённый регион или город по его ID, то можно использовать следующие методы.

- `getRegion(int $id): ?Region` - возвращает модель [Region](/src/models/Region.php), если регион найден. Иначе возвращает `null`;
- `getCity(int $id): ?City` - аналогично методу `getRegion()`, возвращает модель [City](/src/models/City.php) если город найден и `null`, если не найден.

## Лиды
### Отправка
Для отправки лида необходимо создать и заполнить модель [Lead](/src/models/Lead.php), а затем отправить её с помощью метода `send()`. Посмотреть список полей лида вы можете в [документации](https://devtek.io/docs/guide/) или в самой модели [Lead](/src/models/Lead.php). Обратите внимание, что поля модели могут немного отличаться названием.

```php
$lead = new Lead([
    'phone' => +79112223344,
    'first_name' => 'Иван',
    'last_name' => 'Иванов',
    'region' => 'Москва',
    'city' => 'Москва'
]);

$result = $devtek->send($lead);
```

При отправке лида через SDK вы можете указать регион и город как в виде строк _(например, "Москва")_, так и в виде их числовых ID. Если вы укажете регион и город в виде строки, то перед отправкой SDK самостоятельно найдёт и проставит их ID в запрос.

> __Обратите внимание!__
>
> Если вы собираетесь отправлять лиды, указывая регион и город в виде строки, то используйте для этого поля `region` и `city` модели [Lead](/src/models/Lead.php) для указания названия региона и города соответственно.
>
> Если же вы собираетесь отправлять лиды, указывая регион и город в виде ID, то используйте поля `region_id` и `city_id`.
>

Ниже написан пример кода отправки лида с указанием ID региона и города вместо их названий.

```php
$lead = new Lead([
    // ...
    'region_id' => 40,
    'city_id' => 536
]);
```

### Результат отправки
Метод `send()` возвращает ID лида в случае успешной отправкой или `null`, если отправка была неуспешной. Также при вызове метода может быть выброшено исключение [ApiErrorException](/src/exceptions/ApiErrorException.php) в случае ошибки `4XX` - например, если какие-то данные лида не прошли валидацию, а также [ApiServerErrorException](/src/exceptions/ApiServerErrorException.php) в случае ошибки `5XX` - такая ошибка может вернуться из-за неполадок на сервере, - в этом случае отправку лида необходимо повторить через некоторое время.
