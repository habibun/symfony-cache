
##	database
db-create:	## create database
	symfony console doctrine:database:create --if-not-exists

db-drop:	## drop database
	symfony doctrine:schema:drop --force

db-show-changes:	## schema changes
	symfony console doctrine:schema:update --dump-sql

db-update:	## schema update
	symfony console doctrine:schema:update --dump-sql --force

## symfony binary
server-start:	## symfony server start
	symfony serve -d

server-stop:	## symfony server stop
	symfony server:stop

server-status:	## symfony server status
	symfony server:status

server-log:
	symfony server:log


# docker-compose
dc-up: ## docker compose start
	docker-compose up -d --remove-orphans

dc-list: ## list container
	docker-compose ps

dc-down: ## stop containers
	docker-compose down


dc-down: ## docker compose stop
	docker-compose down


