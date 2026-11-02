# Інструкції для агентів

## Контекст проєкту
- Це система бронювання послуг з адміністративною панеллю, підписками та оплатою Stripe.
- Основний застосунок міститься в `src/`; корінь репозиторію містить Docker та допоміжні скрипти.
- Стек: Laravel 13, PHP ^8.3 (Docker: PHP 8.4 FPM), MySQL 8, Blade, Vite 7, Alpine.js, Bootstrap 5.
- API використовує Laravel Sanctum; PDF — Dompdf; резервні копії — Spatie Laravel Backup.
- Спілкуйся з користувачем українською, якщо він не попросив іншу мову.

## Карта коду
- `src/app/Http/Controllers/` — вебконтролери; `Admin/` — адміністрування; `Api/` — API.
- `src/app/Http/Requests/` — класи валідації запитів.
- `src/app/Http/Middleware/` — доступ, журналювання, моніторинг і firewall.
- `src/app/Models/` — Eloquent-моделі, зв'язки, casts та scopes.
- `src/app/Services/` — Stripe, SMS, моніторинг і сканування вразливостей.
- `src/app/Mail/`, `src/app/Events/`, `src/app/Console/` — листи, події та консольні задачі.
- `src/routes/web.php`, `auth.php`, `api.php`, `console.php` — відповідні маршрути.
- `src/bootstrap/app.php` — реєстрація маршрутизації, middleware та обробки винятків.
- `src/resources/views/` — Blade; `resources/css/` і `resources/js/` — вихідні стилі та скрипти.
- `src/database/` — міграції, factories та seeders.
- `src/tests/Feature/` і `src/tests/Unit/` — PHPUnit-тести.
- `docker/`, `Dockerfile`, `docker-compose.yml`, `Makefile` — локальне середовище.

## Робочий процес
- Перед зміною прочитай відповідні маршрути, контролери, моделі та сусідні тести.
- Дотримуйся стилю наявного коду; обмежуй зміни поточною задачею.
- Не переформатовуй сторонні файли й не перезаписуй зміни користувача.
- Використовуй `src/composer.json` і `src/package.json` для залежностей застосунку.
- Кореневий `composer.json` містить окрему залежність Breeze; не плутай його з маніфестом застосунку.
- Не додавай пакети й не оновлюй lock-файли без потреби для задачі.
- Не редагуй вручну `vendor/`, `node_modules/`, `public/build/`, кеші та журнали.
- Не змінюй фотографії в `src/img_book/` без потреби для задачі.

## Команди
Наведені Docker-команди виконуй із кореня репозиторію.
Вони використовують Compose v2; `Makefile` і shell-скрипти використовують `docker-compose`.

```sh
docker compose up -d
docker compose ps
docker compose logs --tail=100 app
docker compose exec app composer install
docker compose exec app npm install
docker compose exec app php artisan migrate
docker compose exec app php artisan route:list
docker compose exec app php artisan test
docker compose exec app php artisan test --filter=ProfileTest
docker compose exec app vendor/bin/pint --test
docker compose exec app npm run build
```

- Для виконання без Docker перейди в `src/` і запускай відповідні `php artisan`, Composer та npm команди.
- Встановлення dev-залежностей потрібне для PHPUnit і Pint; Dockerfile встановлює Composer-пакети з `--no-dev`.
- Перед перебудовою перевір наявність файлів, указаних у `COPY` та bind mounts.
- На момент створення інструкції відсутній `docker/entrypoint.sh`, який потрібен Dockerfile.
- Локальні адреси: сайт `http://localhost`, phpMyAdmin `http://localhost:8080`, Mailhog `http://localhost:8025`.
- MySQL доступний як `db:3306` із контейнера та через порт `3307` із хоста.
- У Windows використовуй PowerShell або Docker; `setup.sh`, `check.sh` і Makefile потребують сумісного середовища.

## Правила реалізації
- Дотримуйся PSR-4 (`App\` → `src/app/`) та наявного форматування PHP.
- Валідуй вхідні дані; використовуй наявні Form Requests або усталений спосіб валідації контролера.
- Зберігай перевірки власника бронювання, адміністративного доступу та прав токенів.
- Для API враховуй префікс `/api/v1`, Sanctum, abilities, throttling і JSON-відповіді.
- Під час зміни бронювань перевір доступність часу, заблоковані дати та переходи статусів.
- Наявні статуси бронювань: `upcoming`, `completed`, `cancelled`; узгоджуй зміни між вебом та API.
- Зберігай логіку інтеграцій у відповідних сервісах і не викликай реальні платежі чи розсилки в тестах.
- Для зміни схеми додавай нові міграції; не переписуй уже застосовані міграції.
- Зміни інтерфейсу внось у Blade та вихідні CSS/JS; перевіряй точки входу в `src/vite.config.js`.
- Дотримуйся мови й оформлення наявного інтерфейсу; не додавай новий UI-фреймворк без потреби.

## Перевірка змін
- Для змін поведінки додавай або оновлюй доречні PHPUnit-тести; наслідуй `Tests\TestCase`.
- Використовуй factories та `RefreshDatabase`, де потрібна ізольована база даних.
- `src/phpunit.xml` налаштовує SQLite `:memory:`; потрібен драйвер `pdo_sqlite`.
- Dockerfile явно встановлює `pdo_mysql`; перевір доступність SQLite перед запуском тестів.
- Спочатку запускай тести зміненого функціоналу; для ширших змін — увесь набір.
- Для PHP перевір форматування Pint, для CSS/JS/Blade-змін перевір Vite-збірку за потреби.
- Для суто документаційних змін перевір достатність, точність шляхів і формат документа.
- У підсумку вкажи зміни, виконані перевірки та конкретні причини, якщо перевірки недоступні.

## Дані та конфігурація
- Не публікуй `.env`, ключі Stripe, токени, паролі або персональні дані в коді чи відповідях.
- Нові параметри середовища описуй без секретів у `.env.example`, якщо такий файл наявний.
- Не видаляй `docker/mysql/data/`, `src/database/database.sqlite`, завантаження чи резервні копії.
- Не запускай `make fresh`, `make clean`, `migrate:fresh` або видалення volumes без прямого запиту.
- Не запускай `setup.sh` чи `make install` як звичайну перевірку: вони генерують ключ і змінюють базу.
- Не регенеруй `APP_KEY` для вже налаштованого середовища без потреби та дозволу користувача.
