.DEFAULT_GOAL := help

help: ## Show this help.
	@fgrep -h "##" $(MAKEFILE_LIST) | fgrep -v fgrep | sed -e 's/\\$$//' | sed -e 's/##//'

up: ## Up containers
	@docker-compose up -d
	@echo -e "Make: Up containers.\n"

build: ## Build containers
	@docker-compose up -d --build
	@echo -e "Make: Up containers.\n"

down: ## Down containers
	@docker-compose down

stop: ## Stop contrainers
	@docker-compose stop	

restart: stop up ## Restart docker containers	

mysql-cli: ## Mysql Console Failed
	@docker exec -it dev-db /usr/bin/mysql -uroot -pexample

cli: ## php консоль
	@docker exec -it --user www-data dev-php bash

cli-root: ##php консоль под рутом
	@docker exec -it  dev-php bash

to-migration:
	@docker exec -it --user www-data dev-php  bin/console doctrine:migrations:diff --no-interaction --formatted

migrate:
	@docker exec -it --user www-data dev-php bin/console doctrine:migrations:migrate
	@docker exec -it --user www-data dev-php bin/console --env=test doctrine:migrations:migrate

test:
	@docker exec -it --user www-data dev-php bin/phpunit

pint:
	@docker exec -it --user www-data dev-php vendor/bin/pint

phpstan:
	@docker exec -it --user www-data dev-php vendor/bin/phpstan analyze --memory-limit=2G

deptrac:
	@docker exec -it --user www-data dev-php vendor/bin/deptrac
