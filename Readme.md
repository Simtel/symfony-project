## Symfony Demo Project

### Features
- Docker
- Makefile
- Symfony 7
- PHPStan max level
- Laravel Pint for code style
- Deptrac for analyse file paths
- PHPUnit
- PHPBench
- GitHub Actions
- GitVerse CI\CD

### Instruction
Clone repository

```bash
https://github.com/Simtel/symfony-project
```

Build containers 

```bash
make build
```

Start containers
```bash
make up
```

Create db and db_test databases in adminer in http://localhost:8080

Install composer packages
```bash
make composer-install
```

Execute migrations
```bash
make migrate
```

Запустить тесты
```bash
make test
```

### API Documentation

Проект предоставляет REST API для управления пользователями, конфигурациями, логами и тестирования различных функций.

#### Базовый URL
```
http://localhost:8000/api
```

#### Аутентификация
Для некоторых endpoint'ов требуется аутентификация через API ключ.

#### Общие endpoint'ы

##### Тестирование API
```http
GET /api/test
```
Ответ:
```json
{
  "test": true,
  "time": "2025-08-28T10:30:00+00:00",
  "message": "API работает корректно"
}
```

##### Тестирование маппинга запроса
```http
POST /api/test-map-request
Content-Type: application/json

{
  "name": "test",
  "value": "example"
}
```

##### Тестирование отправки email
```http
GET /api/test-email
```

##### Тестирование уведомлений
```http
GET /api/test-notify
```

#### Управление пользователями

##### Получение информации о пользователе
```http
GET /api/user/{id}
```
Ответ:
```json
{
  "id": 1,
  "name": "John Doe",
  "email": "john@example.com",
  "locations": [...],
  "contacts": [...]
}
```

##### Поиск пользователя по имени
```http
GET /api/user/find/{userName}
```

##### Добавление локации к пользователю
```http
PUT /api/user/{userId}/location/{locationId}
```
Ответ:
```json
{
  "message": "Локация успешно добавлена к пользователю"
}
```

##### Расчет доступов пользователя
```http
POST /api/user/{userId}/calculate-access
```
Ответ:
```json
{
  "message": "Доступы пользователя успешно рассчитаны"
}
```

##### Получение пользователей по локации
```http
GET /api/users/{locationId}
```
Ответ:
```json
{
  "users": [...],
  "message": "Пользователи найдены"
}
```

#### Управление конфигурациями

##### Получение списка конфигураций
```http
GET /api/config/list
```
Ответ:
```json
[
  {
    "id": 1,
    "name": "app_name",
    "value": "Symfony Demo",
    "author": {...}
  }
]
```

##### Создание новой конфигурации
```http
POST /api/config
Content-Type: application/json

{
  "name": "setting_name",
  "value": "setting_value"
}
```
Ответ:
```json
{
  "message": "Конфигурация успешно создана",
  "config": {
    "id": 2,
    "name": "setting_name",
    "value": "setting_value"
  }
}
```

#### Управление логами

##### Получение списка логов
```http
GET /api/log/list
```
Ответ:
```json
{
  "logs": [
    {
      "id": 1,
      "user": "John Doe",
      "action": "user_created",
      "createdAt": "2025-08-28 10:30:00",
      "url": "/api/log/1"
    }
  ]
}
```

##### Получение детальной информации о логе
```http
GET /api/log/{id}
```
Ответ:
```json
{
  "log": {
    "id": 1,
    "action": "user_created",
    "data": {...},
    "author": {...},
    "createdAt": "2025-08-28T10:30:00+00:00"
  }
}
```

#### Коды ответов

- `200 OK` - Успешный запрос
- `201 Created` - Ресурс успешно создан
- `400 Bad Request` - Ошибка валидации данных
- `401 Unauthorized` - Требуется аутентификация
- `404 Not Found` - Ресурс не найден
- `500 Internal Server Error` - Внутренняя ошибка сервера

#### Формат ошибок

Все ошибки возвращаются в следующем формате:
```json
{
  "error": "Описание ошибки",
  "code": 400,
  "details": {
    "field": "Детали ошибки поля"
  }
}
```

#### Примеры использования

##### Создание конфигурации с помощью curl
```bash
curl -X POST http://localhost:8000/api/config \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_API_KEY" \
  -d '{"name": "debug_mode", "value": "true"}'
```

##### Получение информации о пользователе
```bash
curl -X GET http://localhost:8000/api/user/1 \
  -H "Authorization: Bearer YOUR_API_KEY"
```
