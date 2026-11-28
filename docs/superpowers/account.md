Developer account setup

Set `DEVELOPER_NAME`, `DEVELOPER_EMAIL`, and `DEVELOPER_PASSWORD` in `.env`,
then run `php artisan db:seed`.

Do not store passwords in this repository or commit them to documentation.


After cloning/pulling, run this once:
composer install
Copy-Item .env.example .env
php artisan key:generate

Configure .env, especially database settings and DEVELOPER_* values, then run:
php artisan migrate --seed
npm install
npm run build
php artisan storage:link

composer run dev

That already runs the Laravel server and frontend watcher, so you generally do not also run npm run dev or php artisan serve.

If dependencies are already installed and only code/migrations changed:

composer install
npm install
php artisan migrate
php artisan storage:link
composer run dev


For the developer account, set in .env:
DEVELOPER_NAME="Developer"
DEVELOPER_EMAIL="your-email@example.com"
DEVELOPER_PASSWORD="a-secure-password

Then run php artisan db:seed.