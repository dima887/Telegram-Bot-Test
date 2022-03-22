<?php

/**
 * ссылка для установки вебхука
 *
 * https://api.telegram.org/bot<TOKEN>/setWebhook?url=URL
 */


/**
 * Токен вашего Telegram бота
 */
const TOKEN = '';

/**
 * Токен для получения погоды от Weather API.
 * https://openweathermap.org/api
 */
const WEATHER_TOKEN = '';

/**
 * API адрес для получения погоды
 */
const WEATHER_URL = "https://api.openweathermap.org/data/2.5/weather?appid=" . WEATHER_TOKEN . "&units=metric&lang=ru";

