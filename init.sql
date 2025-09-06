-- Criação do banco de dados caso não exista
CREATE DATABASE IF NOT EXISTS top5_tiao_carreiro;

-- Configurações de encoding
ALTER DATABASE top5_tiao_carreiro CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Usar o banco de dados
USE top5_tiao_carreiro;

-- Criar usuário com permissões completas (se necessário)
-- GRANT ALL PRIVILEGES ON top5_tiao_carreiro.* TO 'laravel'@'%';
-- FLUSH PRIVILEGES;
