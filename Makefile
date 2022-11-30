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

mysql-console: ## Mysql Console Failed
	@docker exec -it dev-db /usr/bin/mysql -uroot -pexample

php-console: ## php консоль
	@docker exec -it --user www-data dev-php bash

php-console-root: ##php консоль под рутом
	@docker exec -it  dev-php bash