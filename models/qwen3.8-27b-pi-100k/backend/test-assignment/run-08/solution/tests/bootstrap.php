<?php

declare(strict_types=1);

/*
 * Тестовой бутстрап: загружает автозагрузчик и окружение (`.env`, затем
 * `.env.test` при APP_ENV=test, заданном в phpunit.xml.dist), чтобы
 * интеграционные тесты работали на изолированной тестовой БД и не трогали
 * основную базу применения.
 */

use Symfony\Component\Dotenv\Dotenv;

require dirname(__DIR__).'/vendor/autoload.php';

(new Dotenv())->bootEnv(dirname(__DIR__).'/.env');
