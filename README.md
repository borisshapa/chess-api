## Chess API

API для проведения шахматных партий позволяет:
* начать новую партию
* сделать ход
* узнать статус партии
* закончить партию

#### Начать новую партию
Создаётся новая строка в таблице базе данных, хранящая информацию о новой партии: 
имя игрока, играющего белыми фигурами, имя игрока, играющего чёрными фигурами, текущее состояние шахматной доски 
(в созданной игре фигуры на доске будут расставлены [классическим образом](https://en.wikipedia.org/wiki/Chess#Setup)) и цвет игрока, который должен сделать ход.
В параметрах `POST` запроса можно указать имена игроков.
* Endpoint
    ```
    POST /chess/start
    ```
* Параметры `POST` запроса

    | Название | Тип | Описание |
    |:--------:|:---:|:--------:|
    | `player1` | `string` | Имя игрока, который ходит __белыми__ фигурами. Опциональный параметр. При его отсутствии строка "player1" будет использоваться в качестве имени. |
    | `player2` | `string` | Имя игрока, который ходит __белыми__ фигурами. Опциональный параметр. При его отсутствии строка "player1" будет использоваться в качестве имени. |

* Ответ

    Ответ содержит `id` новой строки в таблице базы данных, которая отвечает за созданную партию.
    
    __Пример ответа__
    ```
    {
        "id": 508,
        "status": true
    }
    ```
  
#### Сделать ход
В параметрах запроса указываются `id` строки в таблице базы данных, отвечающую за партию в которой делается ход.
Также в параметрах указываются позиции клеток, откуда сделать ход, и куда сделать ход.

Опциональным параметром можно передать название фигуры, в которую должна превратиться пешка, если она дошла до последней горизонтали,
в противном случае этот параметр игнорируется. 

Ходы проверяются на соответствие [правилам классических шахмат](https://en.wikipedia.org/wiki/Chess_piece#Moves_of_the_pieces).  

При этом игрока, который делает ход в параметрах указывать не нужно. Информация об игроке, который делает ход берётся из базы данных (после каждого хода эта информация меняется).  

Для того, чтобы сделать [рокировку](https://en.wikipedia.org/wiki/Castling) в параметре `from` укажите позицию короля, а в параметре `to` позицию короля, на которой он окажется после рокировки (смещение на 2 клетки).
Ладья переместится автоматически.

Для того, чтобы сделать [взятие на проходе](https://en.wikipedia.org/wiki/En_passant) в параметре `from` укажите позицию пешки, а в параметре `to` __свободную__ позицию, в которой окажется эта пешка после хода (смещение по диагонали).
Пешка соперника, съеденная при помощи взятия на проходе, пропадёт с доски.

В случае, если игрок своим ходом ставит [мат](https://en.wikipedia.org/wiki/Checkmate) сопернику, информация о данной игре удаляется из базы данных, пользователю возвращается сообщение о победе того или иного цвета.

* Endpoint
    ```
    PUT /chess/move?id={id}&from={from}&to={to}&piece={piece}
    ```

* Параметры

    | Название | Тип | Описание |
    |:--------:|:---:|:--------:|
    | `id` | `number` | `id` строки в таблице базы данных, в которой хранится информация о партии, в которой нужно сделать ход. |
    | `from` | `string` | Позиция на шахматной доске, на которой находится фигура, делающая ход. Позиция указывается в шахматной нотации, например "e2". (не чувствителен к регистру) |
    | `to` | `string` | Позиция в которой должна оказаться фигура после хода. Позиция указывается в шахматной нотации, например "e4". (не чувствителен к регистру)|
    | `piece` | `string` | Опциональный параметр. Используется в случае, если ход совершается пешкой и она оказывается на последеней горизонтали. Тогда пешка превращается в фигуру указанную в этом параметре. Возможные варианты: `queen`, `rook`, `bishop`, `knight`, `pawn`. Если был совершён ход пешкой на последнюю горизонталь, а этот параметр не был передан, то пешка остаётся пешкой. (не чувствителен к регистру) |

* Ответ

    Пользователь получит ответ с информацией об __ошибке__ в случае:
    * Отсутствия одного из обязательных параметров (`id`, `from`, `to`)
        
        * Пример:
        ```
        {
            "status": false,
            "message": "'id', 'from', 'to' parameters are expected"
        }
        ```
    * В таблице базы данных не существует строки с указанным `id`
    
        * Пример:
        ```
        {
            "status": false,
            "message": "ChessApiException : DatabaseAccessException : There is not model with 510 id in the database"
        }
        ```
    * Ход не соответствует [правилам классических шахмат](https://en.wikipedia.org/wiki/Chess_piece#Moves_of_the_pieces)
    
        * Пример:
        ```
        {
            "status": false,
            "message": "ChessException : InvalidMoveException : Unable to castle. Not all positions on the path are free."
        }
        ```
      
    Пользователь получит ответ об __успешном__ выполнении хода в формате:
    
    ```
    {
        "id": 508,
        "message": "A move e2-e4 has been made.",
        "status": true
    }
    ```
  
    или 
    
    ```
    {
        "id": 508,
        "message": "White player won. The 508 game has been removed from the database.",
        "status": true
    }
    ```
    если игрок поставил мат своим ходом. 
    
    Здесь `id` — идентификатор строки в таблице базы данных, отвечающей за партию, в которой был сделан ход.
    
#### Узнать статус партии
API возвращает информацию о партии по переданному параметру `id`, который обозначает `id` строки в таблице базы данных, в которой хранится состояние интересующей игры.

Пользователю возвращаются имена игроков, положение фигур на доске в данный момент и цвет игрока, от которого ожидается ход.  
(Формат возвращаемых данных см. в примере)

* Endpoint
    ```
    GET /chess/status?id={id}
    ```

* Параметры

    | Название | Тип | Описание |
    |:--------:|:---:|:--------:|
    | `id` | `number` | `id` строки в таблице базы данных, в которой хранится информация об интересующей партии |
    
* Ответ

    Пользователь получит ответ с информацией об __ошибке__ в случае:
    * Отсутствия параметра `id`
        
        * Пример:
        ```
        {
            "status": false,
            "message": "'id' parameter is expected"
        }
        ```
    * В таблице базы данных не существует строки с указанным `id`.
    
        * Пример:
        ```
        {
            "status": false,
            "message": "ChessApiException : DatabaseAccessException : There is not model with 510 id in the database"
        }
        ```
    
    В случае __успешной__ обработки запроса, пользователь получит информацию о партии в следующим формате:
    
    ```
    {
        "id": 508,
        "players": 
        {
            "white": "Bob",
            "black": "Alice"
        },
        "current": "black",
        "board": 
        [
            ["BR","BN","BB","BQ","BK","BB","BN","BR"],
            ["BP","BP","BP","BP","BP","BP","BP","BP"],
            ["__","__","__","__","__","__","__","__"],
            ["__","__","__","__","__","__","__","__"],
            ["__","__","__","__","WP","__","__","__"],
            ["__","__","__","__","__","__","__","__"],
            ["WP","WP","WP","WP","__","WP","WP","WP"],
            ["WR","WN","WB","WQ","WK","WB","WN","WR"]
        ],
        "status":true
    }
    ```
    Здесь "__" — пустая клетка. Две буквы обозначают фигуру: первая буква — первая буква названия цвета фигуры, вторая — буква обозначает тип фигуры в шахматной нотации (например, "WR" — White Rook).
    
#### Закончить партию

Заканчивает партию, удаляя строку с `id`, переданным в качестве параметра, из таблицы базы данных.

* Endpoint
    ```
    DELETE /chess/finish?id={id}
    ```
* Параметры

    | Название | Тип | Описание |
    |:--------:|:---:|:--------:|
    | `id` | `number` | `id` строки в таблице базы данных, которую требуется удалить |
    
* Ответ

    Пользователь получит ответ с информацией об __ошибке__ в случае:
    * Отсутствия параметра `id`
        
        * Пример:
        ```
        {
            "status": false,
            "message": "'id' parameter is expected"
        }
        ```
    * В таблице базы данных не существует строки с указанным `id`.
    
        * Пример:
        ```
        {
            "status": false,
            "message": "ChessApiException : DatabaseAccessException : There is not model with 510 id in the database"
        }
        ```
    
    В случае __успешной__ обработки запроса, пользователь получит информацию о партии в следующим формате:
    ```
    {
        "id": 508,
        "message": "Game over. The 508 game has been removed from the database.",
        "status": true
    }
    ```

---
#### Запуск
* Для того, чтобы все зависимости в проекте корректно разрешились необходимо установить и настроить [Composer](https://getcomposer.org/).
* После настройки `composer` установите composer-пакеты, от которых указана зависимость в [composer.json](composer.json)
* Для работы с API необходимо запустить [Apache](https://httpd.apache.org/) http сервер c ip адресом `localhost` и портом `8080`
* Также необходимо создать таблицу с именем `game` в базе данных MySql c именем `chess` со следующими полями:

![](https://sun9-55.userapi.com/0TrMkVdyzhUaEDCTXUHKS01GxZJISBfFvkA6ZQ/3sirN5h7Bj4.jpg)

Для удобства работы с серверами и базами данных рекомендуется установить и настроить [xampp](https://www.apachefriends.org/ru/index.html).

Тогда запрос к API может выглядеть следующим образом:
```
localhost:8080/chess/move?id=508&from=g7&to=g8&piece=queen
```

Следующие параметры можно изменить в файле [Config.php](src/app/Config.php):
* IP-адрес(`HOST`);
* порт(`PORT`);
* имя используемой базы данных (`DB_NAME`);
* имя пользователя для доступа к базе данных (`USERNAME`);
* пароль для доступа к базе данных (`PASSWORD`).

#### Тестирование
Должны быть установлены composer-пакеты `phpunit`.

Есть два набора тестов:
* [ChessEngineTest](tests/ChessEngineTest.php) тестирует функционал, связанный непосредственно с проведением шахматной партии: инициализация шахматной доски, ходы фигур, проверка условий победы того или иного цвета.  
Для тестирования __не нужны__ соединение с `Apache` сервером и соединение с базой данных.

* [ChessApiTest](tests/ChessApiTest.php) тестирует работу API: проверяет корректность ответов на соответствующие запросы.  
Для тестирования __должны быть__ включены `Apache` сервер и MySql сервер.