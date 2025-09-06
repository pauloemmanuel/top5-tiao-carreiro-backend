# top5-tiao-carreiro-backend


## Como iniciar o projeto Laravel (API) com Docker

1. Certifique-se de que o Docker Desktop está rodando.
2. Clone o repositório e acesse a pasta do projeto.
3. Execute o comando abaixo na raiz do projeto:

```
docker compose up -d --build
```

4. O serviço Laravel estará disponível na porta 9000 do container (php-fpm). Para acessar via navegador, utilize um servidor web (Nginx/Apache) ou configure o Laravel Sail/Valet se desejar acesso HTTP.

5. O banco de dados MySQL estará disponível na porta 3306 do container `laravel-api-db`.

6. Para rodar migrations:

```
docker compose exec app php artisan migrate
```

7. Para instalar dependências (caso necessário):

```
docker compose exec app composer install
```

---
Qualquer dúvida, consulte o Dockerfile e o docker-compose.yml para detalhes de configuração.

