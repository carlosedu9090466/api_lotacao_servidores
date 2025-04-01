## rodar o docker 

## DOCKER - subir os containers - API - BANCO - SISTEMA DE ARMAZENAMENTO

## rode o docker-compose - para subir os containers

docker-compose up -d --build

## depois em seguida rode os comandos na sequência

docker exec -it api_lotacao_servidores-master-app-1 composer install

docker exec -it api_lotacao_servidores-master-app-1 php artisan migrate

docker exec -it api_lotacao_servidores-master-app-1 php artisan db:seed --class=AdminUserSeeder 

