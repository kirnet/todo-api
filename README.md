## Restfull ToDo API

### Deploy localhost
```
cp .env.example .env
composer install

vendor/bin/sail up -d
vendor/bin/sail shell

./artisan migrate:install
./artisan migrate

npm install
npm run dev
```

http://localhost/swagger



