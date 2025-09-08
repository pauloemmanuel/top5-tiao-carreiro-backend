# Top 5 Tião Carreiro & Pardinho - Backend API

Este é o backend da aplicação Top 5 Tião Carreiro & Pardinho, desenvolvido em Laravel 11 com MySQL e Docker. A aplicação permite listar as músicas mais tocadas da dupla, receber sugestões de novas músicas e gerenciar aprovações através de uma API REST.

## 🎯 Funcionalidades

- **API REST** completa para gerenciamento de músicas
- **Sistema de sugestões** com aprovação/rejeição
- **Autenticação JWT** com Laravel Sanctum
- **Integração com YouTube** para buscar informações dos vídeos
- **Paginação** para listas grandes
- **Testes Unitários** com PHPUnit
- **Docker** para padronização do ambiente

## 🛠️ Tecnologias

- **Laravel 11** (PHP 8.2)
- **MySQL 8.0**
- **Laravel Sanctum** (Autenticação)
- **Guzzle HTTP** (Requisições HTTP)
- **Docker & Docker Compose**
- **PHPUnit** (Testes)

## 👤 Usuário padrão admin

Após rodar os seeders, utilize o seguinte usuário para acessar rotas protegidas como admin:

- **Email:** admin@tiaocarreiro.com
- **Senha:** password123

## 🚀 Como executar o projeto

### Pré-requisitos

- Docker 
- Git

### Setup do projeto



1. Inicie os containers (Já deve baixar as dependências do composer):
```bash
docker-compose up -d --build
```

2. Baixe as dependências
```bash
docker-compose exec app composer install
```

3. Copie o arquivo de exemplo de variáveis de ambiente:
```bash
# Dentro do container (recomendado)
docker-compose exec app cp .env.example .env

# Ou, no host (PowerShell):
copy .env.example .env
```

4. Gere a chave da aplicação Laravel:
```bash
docker-compose exec app php artisan key:generate
```

5. Execute as migrations:
```bash
docker-compose exec app php artisan migrate:fresh --seed
```

6. Teste a api em:
   - **API**: http://localhost/api/ping


## 🧪 Testes

Execute os testes automatizados:

```bash
docker-compose exec app php artisan test
```

## Usuários padrão criados pelos seeders:

- **Admin**: admin@tiaocarreiro.com / password123
- **Teste**: teste@tiaocarreiro.com / teste123

## 🔧 Comandos úteis

```bash
# Acessar container da aplicação
docker-compose exec app bash

# Ver logs
docker-compose logs -f app

# Baixar as dependencias: 
docker-compose exec app composer install
# Parar containers
docker-compose down

# Recriar banco de dados
docker-compose exec app php artisan migrate:fresh --seed

# Limpar cache
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear
```

