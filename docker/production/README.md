# GitHub CI/CD без SSH

Підготовлено для **Linux x64 із Docker Engine, Compose v2 та приватним GitHub-репозиторієм**. Локальний `docker-compose.yml` не змінюється. Production використовує окремий Compose-проєкт `booking-production`, окрему БД та постійні volumes. Наявні локальні дані автоматично не переносяться.

## Що запускається

- Pull request: Laravel-тести на PHP 8.4 / SQLite, frontend-тести, Vite-збірка та перевірка збірки production-образів.
- Push у `main` або ручний запуск workflow на `main`: ті самі перевірки та публікація образів `ghcr.io/<owner>/<repository>-app` і `…-web` з тегом SHA коміту.
- Якщо repository variable `PRODUCTION_DEPLOY_ENABLED` дорівнює `true`, runner на сервері завантажує ці образи за незмінним digest і розгортає їх.
- CI перевіряє workflow через actionlint, скрипти через ShellCheck та шість сценаріїв деплою з імітацією Docker: успіх, перша інсталяція, помилки backup / міграції / healthcheck і відмова від mutable image tag.
- Окремі контейнери виконують PHP-FPM, чергу, Laravel scheduler, Nginx і MySQL. Node.js та dev-залежності не входять у кінцевий PHP-образ.

## 1. Підготувати сервер

Потрібні Docker Engine, плагін Compose v2 з підтримкою `--wait` і кількох `--env-file`, Bash, `flock`, `gzip` та вихідний HTTPS-доступ до GitHub, GHCR і Docker Hub. SSH не використовується; початкові команди можна виконати через вебконсоль провайдера.

З консолі сервера, у завантаженій через HTTPS копії цього репозиторію:

```bash
sudo install -d -m 700 -o "$USER" -g "$(id -gn)" /opt/booking-system
cp docker/production/.env.example /opt/booking-system/production.env
cp src/.env.example /opt/booking-system/app.env
chmod 600 /opt/booking-system/production.env /opt/booking-system/app.env
```

Каталог має належати користувачу служби runner. Якщо це інший користувач, зміни власника відповідно. Йому також потрібен доступ до Docker daemon, що фактично дає адміністративні права на сервері.

У `production.env` установи **різні сильні паролі** `DOCKER_DB_PASSWORD` і `DOCKER_DB_ROOT_PASSWORD`. Використовуй значення, сумісні з форматом dotenv; для паролів із `$` використовуй одинарні лапки. Зміна пароля в цьому файлі не змінює пароль уже ініціалізованої MySQL.

У `app.env` налаштуй:

- `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://твій-домен`.
- `APP_KEY`: збережи ключ наявного застосунку. Для цілком нової інсталяції створи його один раз у довіреному PHP 8.4 середовищі: `php -r 'echo "base64:".base64_encode(random_bytes(32)).PHP_EOL;'`.
- `SESSION_SECURE_COOKIE=true` для HTTPS.
- Реальні SMTP-параметри, Stripe та інші потрібні інтеграції. Mailhog у production не запускається.
- Для початку залиш `QUEUE_CONNECTION=database`, `CACHE_STORE=database`, `SESSION_DRIVER=database`. Redis-розширення та Redis-сервіс до production-конфігурації не включені.

Параметри підключення MySQL встановлює Compose з `production.env`. Файли із секретами залишаються на сервері, у GitHub їх передавати не потрібно. Ключ застосунку під час деплою не генерується.

## 2. Зареєструвати runner

У GitHub відкрий **Settings → Actions → Runners → New self-hosted runner**, вибери **Linux x64** і виконай наведені GitHub команди на сервері. Додай custom label **`booking-production`** під час реєстрації. Запусти runner як службу за інструкцією GitHub, щоб він працював після перезавантаження.

Runner має бути актуальним і підтримувати Node.js 24 actions. З цього workflow він отримує тільки деплой довіреної гілки `main`; PR виконуються на GitHub-hosted runners. Не реєструй production runner для виконання недовірених workflow або публічних PR. Захисти `main` від неперевірених змін workflow.

Документація: [додавання runner](https://docs.github.com/en/actions/how-tos/manage-runners/self-hosted-runners/add-runners), [мережеві вимоги](https://docs.github.com/en/actions/reference/runners/self-hosted-runners#communication).

## 3. Увімкнути Actions і деплой

Збережи зміни цього налаштування в GitHub. У **Settings → Actions → General** дозволь використані workflow actions. Workflow сам запитує `packages: write` для публікації та `packages: read` для завантаження. Авторизація в GHCR відбувається тимчасовим `GITHUB_TOKEN`, окремий PAT не потрібен. Якщо packages вже існують, надай цьому репозиторію доступ до них у налаштуваннях packages.

У **Settings → Secrets and variables → Actions → Variables** додай:

| Змінна | Значення |
| --- | --- |
| `PRODUCTION_DEPLOY_ENABLED` | `true` після підготовки сервера |
| `PRODUCTION_DEPLOY_PATH` | `/opt/booking-system` або свій абсолютний шлях |

Після підключення runner відкрий **Actions → CI/CD → Run workflow → main**. Надалі push у `main` автоматично запускає перевірки, збірку і деплой. До ввімкнення змінної виконуються лише CI та публікація образів.

Документація: [публікація Docker-образів у GitHub Actions](https://docs.github.com/en/actions/tutorials/publish-packages/publish-docker-images).

## 4. HTTPS і перевірка

За замовчуванням сайт доступний лише на сервері: `http://127.0.0.1:8088`. Налаштуй reverse proxy з TLS перед цим портом; для Cloudflare Tunnel також можна використовувати цей локальний endpoint. У reverse proxy передавай правильний `Host`. Якщо потрібне розпізнавання HTTPS та клієнтського IP через proxy, окремо налаштуй довірені адреси proxy в Laravel; не довіряй довільним forwarded headers.

MySQL не має опублікованого порту; phpMyAdmin і Mailhog відсутні. `storage`, завантаження `public/uploads` та MySQL зберігаються в окремих named volumes між релізами. Nginx отримує ту саму версію `public/` і Vite-ресурсів, що й PHP, та перевіряє `/up`.

Після успішного деплою з консолі сервера:

```bash
DEPLOY_PATH=/opt/booking-system
RELEASE_ID=$(cat "$DEPLOY_PATH/current-release")
docker compose --project-name booking-production --project-directory "$DEPLOY_PATH" \
  --env-file "$DEPLOY_PATH/production.env" \
  --env-file "$DEPLOY_PATH/releases/$RELEASE_ID/images.env" \
  -f "$DEPLOY_PATH/releases/$RELEASE_ID/compose.yml" ps
curl --fail http://127.0.0.1:8088/up
```

`/up` перевіряє запуск Laravel і проходження HTTP middleware; це не перевірка реальних платежів, SMTP чи повного користувацького сценарію. Перевір їх окремо з потрібними обліковими даними.

## Збої, резервні копії та повернення версії

Деплой виконує `artisan down`, зупиняє queue/scheduler, створює SQL backup непорожньої БД у `backups/<release>.sql.gz`, запускає `migrate --force`, оновлює контейнери та очікує HTTP healthcheck. На першій інсталяції порожня БД не резервується. Під час оновлення буде коротка перерва в доступності. HTTP-запити, які вже виконувалися до maintenance, потрібно врахувати під час планування критичних міграцій.

Якщо backup або міграція не вдалися, процес зупиняється. Maintenance може залишитися ввімкненим, фонові обробники — зупиненими. Переглянь workflow logs і `docker compose … logs --tail=100`, виправ причину та повтори workflow. `current-release` змінюється тільки після успіху. Невдалі релізи теж залишають конфігурацію в `releases/`, тому їх можна діагностувати.

Кожен реліз зберігає свій `compose.yml` і `images.env`. Для повернення попередніх контейнерів вибери успішний попередній `RELEASE_ID` і використай команду Compose з попереднього розділу, замінивши `ps` на `up -d --force-recreate --wait`. Перед цим переконайся в сумісності поточної схеми БД з попереднім кодом, зупини фонові обробники та ввімкни maintenance. Якщо потрібно, після запуску попереднього app виконай `exec -T app php artisan up`.

Повернення контейнерів **не відкочує міграції**. Відновлення SQL backup — окрема операція після оцінки нових записів у БД. Автоматичного `migrate:rollback`, видалення volumes чи очищення backups немає. SQL backups містять конфіденційні дані; налаштуй їх захищене зовнішнє зберігання й контроль вільного місця. Файли користувачів резервуй окремо; цей SQL backup їх не містить.

Для перенесення вже наявної інсталяції спочатку окремо перенеси БД, storage і uploads у production volumes та збережи старий `APP_KEY`. Цей workflow не змінює локальні `docker/mysql/data/` і `src/storage/`.
