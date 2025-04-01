## rodar o docker 

## DOCKER - subir os containers - API - BANCO - SISTEMA DE ARMAZENAMENTO

## rode o docker-compose - para subir os containers

docker-compose up -d --build

## depois em seguida rode os comandos na sequência

docker exec -it api_lotacao_servidores-master-app-1 composer install

docker exec -it api_lotacao_servidores-master-app-1 php artisan migrate

docker exec -it api_lotacao_servidores-master-app-1 php artisan db:seed --class=AdminUserSeeder 

## 3 - entre no localhost:90001 e digite as credenciais para acessar o MinIO bucket 

## Username = minioadmin

## Password = minioadmin

## 4 - crie um bucket chamado laravel 
## com isso pode salvar as imagens das pessoas no bucket pela rota da api.

# irá crirar um admin. quando rodar o seed 
# email - carlosadm@gmail.com
# password - @123@123'