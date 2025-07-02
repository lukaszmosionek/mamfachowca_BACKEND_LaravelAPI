### App URL
[http://api.mamfachowca.mosioneklukasz.pl/docs/api](http://api.mamfachowca.mosioneklukasz.pl/docs/api)

### Docker
Launch Docker app if using Windows
```sh
git clone https://github.com/lukaszmosionek/mamfachowca-BACKEND-LaravelAPI.git && cd mamfachowca-BACKEND-LaravelAPI
docker build -t mam_fachowca-laravel-api .
docker run -p 8000:80 mam_fachowca-laravel-api
 ```

### Standard installation( php -v PHP 8.2.12 )
```sh
git clone https://github.com/lukaszmosionek/mamfachowca-BACKEND-LaravelAPI.git && cd mamfachowca-BACKEND-LaravelAPI
composer install
php artisan migrate
php artisan serve
```
