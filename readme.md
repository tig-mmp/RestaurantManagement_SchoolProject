# Restaurant Management

## Installation

-   Copiar o ficheiro `.env.example` com o nome `.env`
-   Configurar o ficheiro `.env`
    -   Utilizar base de dados vazia DB_DATABASE=nome_da_bd_vazia

```
npm install

composer install

php artisan key:generate

php artisan config:cache

php artisan migrate

php artisan db:seed

php artisan passport:install
```

-   Atualizar o CLIENT_SECRET no ficheiro `.env`

```
mklink /D public\imgItems ..\storage\app\public\items
mklink /D public\imgProfiles ..\storage\app\public\profiles
```

### Se der erro ao fazer login

```
php artisan config:cache

php artisan config:clear
```

### Atualizar front-end

```
npm run watch
```

### Iniciar WebServer

```
npm run start
```

## Login credentials

### Manager

`Email: m0@mail.pt`
`Password: 123`

### Waiter

`Email: w0@mail.pt`
`Password: 123`

### Cook

`Email: k0@mail.pt`
`Password: 123`

### Cashier

`Email: c0@mail.pt`
`Password: 123`
