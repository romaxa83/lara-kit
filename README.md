## Установка

Для локальной разработки:
- Копирование файлов окружения
```shell
./we init
```

- Правим файлы
```shell
nano .env
nano .env.testing
```

- Билдим контейнера
```shell
./we build
```

- Активируем паспорта клиентов
```shell
php artisan passport:client --password --provider=admins --name='Admins'
php artisan passport:client --password --provider=users --name='Users'
```

Вносим паспортные данные в .env
```shell
nano .env

#Заполняем из вывода предыдущих команд
OAUTH_USERS_CLIENT_ID=
OAUTH_USERS_CLIENT_SECRET=
OAUTH_ADMINS_CLIENT_ID=
OAUTH_ADMINS_CLIENT_SECRET=
```
