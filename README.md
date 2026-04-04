# LazEQEmu Editor
![Laravel](https://img.shields.io/badge/laravel-%23FF2D20.svg?style=for-the-badge&logo=laravel&logoColor=white)![TailwindCSS](https://img.shields.io/badge/tailwindcss-%2338B2AC.svg?style=for-the-badge&logo=tailwind-css&logoColor=white)![DaisyUI](https://img.shields.io/badge/daisyui-5A0EF8?style=for-the-badge&logo=daisyui&logoColor=white)

## Live Demo
You can see this in use [here](https://editor.lazaruseq.com/) User: editor@me.com / Pass: editor011

### Use at your own risk

This project is a work in progress. It may be unstable, not feature complete, and contain bugs. Make backups before using in production.

## Requirements

- PHP >= 8.2, Composer, Mysql/MariaDB, and an EQemu DB.

## Installation

##### To setup a local development environment
```
git clone https://github.com/chadw/lazeqemueditor.git
cd lazeqemueditor

composer install
npm install
npm run dev

cp .env.example .env
```
Create a lazeqemueditor db utf8mb4/utf8mb4_unicode_ci
Edit the .env variables to point to your lazeqemueditor db
```
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=lazeqemueditor
DB_USERNAME=root
DB_PASSWORD=
```

Edit the .env variables to point to your eqemu db.
```
EQEMU_DB_HOST=127.0.0.1
EQEMU_DB_PORT=3306
EQEMU_DB_DATABASE=peq
EQEMU_DB_USERNAME=user
EQEMU_DB_PASSWORD=password
```

Now run migrations and the seeder. This will populate your lazeqemueditor db with tables used for sessions and caching.
```
php artisan migrate
php artisan db:seed
```
After the seed runs your admin user credentials will be shown. Make sure to copy/paste somewhere safe.

### To set this up in production
Copy over your lazeqemueditor db and run the following command on your production server
```
php artisan optimize:clear
```

Next build the assets. Do this on your dev server preferrably.
```
npm run build
```
Then copy the /public/build/ folder to your production server.

Always install this outside your publically accessible web directory. Symlink the /public folder to your public accessible web directory.


## License

The LazEQEmu Editor is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
