# Facebook Messenger Bot Game CITIES

[Messenger](https://developers.facebook.com/products/messenger/) Bot written on php via lets-code session at [Netology Online University](http://netology.ru/). Beginners guide.

## Demo

[@NetoCityBot](https://m.me/netocitybot)

## Requirements

1. PHP 5.5
2. HTTPS public web-server

## Setup

```bash
git clone https://github.com/netology-code/NetoCityBot-messenger.git
cd NetoCityBot-messenger
composer install
```

## Configuration

Use [guide](https://developers.facebook.com/docs/messenger-platform/guides/quick-start)

1. Create a Bot Page and App following first step of guide.
2. Set enviroment variables `APP_ID` and `APP_SECRET`, using App data.
3. Set enviroment variable `HOOK_SUBSCRIBE_TOKEN` using any string.
4. Run server and open `https://your.host/hook.php`
5. Configure webhook following second step of guide, using `https://your.host/hook.php` as callback url and string from `HOOK_SUBSCRIBE_TOKEN`. Select `messages` and `messaging_postbacks` in subscription fields.
6. Subscribe app to page following step thrie of guide and get page token.
7. Set enviroment variable `PAGE_TOKEN` using page token.

## Usage

### Setting bot config

Change texts and commands in `config.json` and run composer script

```bash
composer run-script setup
```

### Change bot reactions

In `hook.php`

`$app->on` — set event handler at any recived webhook event.

`$app->postback` — set event handler for any postback by name. What is postback? It`s commands runs by buttons, like *Start*.

`$app->messages` — set event handler for any text message from user. You can filter messages by second callback: if it returns `true`, handler will fired.