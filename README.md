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

- Docker Desktop instalado e rodando
- Git

### Setup do projeto

1. Clone o repositório:
```bash
git clone https://github.com/pauloemmanuel/top5-tiao-carreiro-backend.git
cd top5-tiao-carreiro-backend
```

2. Inicie os containers:
```bash
docker-compose up -d --build
```

3. Gere a chave da aplicação Laravel:
```bash
docker-compose exec app php artisan key:generate
```

4. Aguarde o MySQL inicializar (aproximadamente 30 segundos) e execute as migrations:
```bash
docker-compose exec app php artisan migrate:fresh --seed
```


5. Teste a api em:
   - **API**: http://localhost/api/ping


## 🧪 Testes

Execute os testes automatizados:

```bash
docker-compose exec app php artisan test
```

### Testando a API com Postman/Insomnia

Importe o arquivo `postman_collection.json` no Postman ou Insomnia para testar todos os endpoints da API.

**Variáveis da coleção:**
- `base_url`: http://localhost/api
- `auth_token`: Token JWT retornado no login

**Fluxo recomendado:**
1. Fazer login para obter o token
2. Configurar a variável `auth_token` 
3. Testar endpoints protegidos

## 📊 Banco de Dados

### Usuários padrão criados pelos seeders:

- **Admin**: admin@tiaocarreiro.com / password123
- **Teste**: teste@tiaocarreiro.com / teste123

### Estrutura das tabelas:

- `users` - Usuários do sistema
- `musicas` - Músicas cadastradas
- `sugestoes` - Sugestões enviadas pelos usuários

## 🔧 Comandos úteis

```bash
# Acessar container da aplicação
docker-compose exec app bash

# Ver logs
docker-compose logs -f app

# Parar containers
docker-compose down

# Recriar banco de dados
docker-compose exec app php artisan migrate:fresh --seed

# Limpar cache
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear
```

