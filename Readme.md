# Docker 

PHP-FPM 8.0

MySQL + Adminer

Nginx 1.13

XDebug

Memcache:latest

Mailhog

---

 + [Get started](#getstarted);
 + [Подсказки](#helpers);
---
### Адреса

Сайт:
```
http://localhost
```

Админер
```
http://localhost:8080
login: root
password: example
```

Данные для подключения к Memcached
```
host: memcached
port: 11211
```

Веб интерфейс к mailhog
```
http://localhost:8025
```
---
### <a name="getstarted"></a> Get started

Клонируем репозиторий
```console
git@github.com:Simtel/docker-dev.git
```
Переходим в папку
```console
cd docker-dev
``` 

Билдим контейнеры
```console
make build
```
---
### <a name="helpers"></a> Подсказки
Консолька для запуска php скриптов

```sh
make php-console
```

Консолька Mysql
```sh
make mysql-console
```

Восстановление бд из файла дампа
```sh
cat backup.sql | docker exec -i dev-db /usr/bin/mysql -u root --password=example DATABASE
```

Если нужно какие то настройки вносить в php.ini, то задавать их надо в 

```console
./docker/php/php.ini
```

и затем перезапустить контейнеры.

