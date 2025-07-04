# Langame News Portal

### Тестовое задание для Langame

## Реализовано

* Агрегация новостей из внешних источников по расписанию и вручную CLI-командой
* Раздел новостей с поиском и постраничной навигацией, доступный только для авторизованных пользователей, подтвердивших свою регистрацию
* В БД записыаются только уникальные новости, дублирующиеся новости игнорируются
* Регистрация пользователей с подтверждением через Telegram
* Раздел пользователей с постраничной навигацией, доступный только для авторизованных пользователей, подтвердивших свою регистрацию

## Технические особенности реализации

* Реализовано на PHP 8.2 с использованием фреймворка Laravel 12
* Фронтенд реализован с использованием Alpine.js и Tailwind CSS на Blade-шаблонах (с поддержкой светлой/тёмной темы в соответствии с системными настройками)
* Для хранения данных используется БД MySQL
* В качестве веб-сервера используется Nginx
* Приложение контейнеризировано с помощью Docker и Docker Compose
* Доступ к данным реализован с использованием паттерна Repository
* Вся бизнес-логика вынесена в сервисный слой
* Для добавления новых аггрегаторов для новых источников новостей используется паттерн Strategy с интерфейсом `NewsAggregatorInterface`
* Очереди работают через RabbitMQ (используются пакеты `vladimir-yuldashev/laravel-queue-rabbitmq` и `php-amqplib/php-amqplib`)
* Пользовательский ввод обрабатывается на уровне FormRequest и трансофрмируется в DTO
* Для генерации уведомлений используется паттерн Observer
* Для запуска фоновых задач используется паттерн Command
* Для проверки статуса учетной записи используется кастомная Middleware[](https://)
* Отправка кода подтверждения реализована через нативный функционал Notifications в Laravel, расширенный каналом Telegram (используется пакет `laravel-notification-channels/telegram`)

## Дополнительно реализовано

* COMET через SSE для мгновенных уведомлений о новых пользователях и новостях
* Юнит-тесты для сервисов аггрегации новостей

## Потенциальные улучшения

* Ограничить количество попыток перезапроса кода подтверждения
* Автоматическое удаление неактивированных учётных записей по истечении определенного срока
* Возможность восстановления пароля
* Автоматическое обновление страницы новостей/пользователей при получении новых данных через SSE
* Полное покрытие функционала юнит-тестами
* Отправка кода подтверждения в личный телеграм-аккаунт пользователя
* Использовать Redis для кэширования данных
* Использовать RMQ для хранения очереди пушей
