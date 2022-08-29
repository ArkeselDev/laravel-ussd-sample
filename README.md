# Laravel USSD Sample

## Set-up process
<br>

### Clone the repo
```
gh repo clone ArkeselDev/laravel-ussd-sample && cd laravel-ussd-sample
```
or

```
git clone git@github.com:ArkeselDev/laravel-ussd-sample.git && cd laravel-ussd-sample
```
<br>

### Install dependencies
```
composer install
```
<br>

### Launch application
```
php artisan serve
```
<br>

### Test Using Postman
The route hanlding the ussd code is `/api/handle-ussd`. Enter this url (most likely to be `http://127.0.0.1:8000/api/handle-ussd`) in [postman](https://postman.com) and you can start experimenting with the ussd process.

<br>

### Test Using Arkesel USSD Simulator
You can also experiment with the sample ussd process using the Arkesel USSD Simulator app which is currently available on the [Google Playstore](https://play.google.com/store/apps/details?id=com.arkesel.mobile.simulator)

