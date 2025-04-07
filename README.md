# API Lotação Servidores

## Configuração Inicial

### 1. Subindo os Containers Docker

Execute o comando abaixo para construir e iniciar os containers (API, Banco de Dados e Sistema de Armazenamento):

```bash
docker-compose up -d --build
```

### 2. Configurando a Aplicação

Execute os seguintes comandos em sequência:

#### Instalando Dependências
```bash
docker exec -it api_laravel_lotacao composer install
```

#### Executando Migrações
```bash
docker exec -it api_laravel_lotacao php artisan migrate
```

#### Populando o Banco com Usuário Admin
```bash
docker exec -it api_laravel_lotacao php artisan db:seed --class=AdminUserSeeder
```

### 2.1 Aplicação é acessada localmente em `localhost:8000/api/nome da rota`

### 3. Configurando MinIO

1. Acesse o MinIO em `localhost:9001`
2. Use as seguintes credenciais:
   - Username: `minioadmin`
   - Password: `minioadmin`
3. Apos a execução dos containers o bucket foi criado e chamado de `laravel`

> Após a configuração, você poderá utilizar o bucket para armazenamento de imagens através da API.

### Credenciais do Administrador

Após executar o seeder, você terá acesso com as seguintes credenciais:
- Email: `admin@gmail.com`
- Senha: `@123@123`
