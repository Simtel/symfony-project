.DEFAULT_GOAL := help

help: ## Show this help.
	@fgrep -h "##" $(MAKEFILE_LIST) | fgrep -v fgrep | sed -e 's/\\$$//' | sed -e 's/##//'

up: ## Up containers
	@docker compose up -d
	@echo -e "Make: Up containers.\n"

build: ## Build containers
	@docker compose up -d --build
	@echo -e "Make: Up containers.\n"

down: ## Down containers
	@docker compose down

stop: ## Stop contrainers
	@docker compose stop

restart: stop up ## Restart docker containers	

mysql-cli: ## Mysql Console Failed
	@docker exec -it dev-db /usr/bin/mysql -uroot -pexample

cli: ## php консоль
	@docker exec -it --user www-data dev-php bash

xcli: ## php консоль
	@docker exec -it --user www-data dev-php-xdebug bash

cli-root: ##php консоль под рутом
	@docker exec -it  dev-php bash

composer-install: ##Install composer packages
	docker exec -it --user www-data dev-php sh -c "composer install"

to-migration:
	@docker exec -it --user www-data dev-php  bin/console doctrine:migrations:diff --no-interaction --formatted

migrate:
	@docker exec -it --user www-data dev-php bin/console doctrine:migrations:migrate
	@docker exec -it --user www-data dev-php bin/console --env=test doctrine:migrations:migrate

rollback:
	@docker exec -it --user www-data dev-php bin/console doctrine:migrations:migrate prev

test:
	docker exec -it --user www-data dev-php vendor/bin/phpunit

testf:
	@docker exec -it --user www-data dev-php ./vendor/bin/phpunit --filter $(FILTER) --testdox

xtest:
	docker exec -it --user www-data dev-php-xdebug vendor/bin/phpunit

xtestf:
	@docker exec -it --user www-data dev-php-xdebug ./vendor/bin/phpunit --filter $(FILTER) --testdox

pint:
	@docker exec -it --user www-data dev-php vendor/bin/pint

xpint:
	@docker exec -it --user www-data dev-php-xdebug vendor/bin/pint

phpstan:
	@docker exec -it --user www-data dev-php vendor/bin/phpstan analyze --memory-limit=2G

xphpstan:
	@docker exec -it --user www-data dev-php-xdebug vendor/bin/phpstan analyze --memory-limit=2G --xdebug

deptrac:
	@docker exec -it --user www-data dev-php vendor/bin/deptrac

bench:
	@docker exec -it --user www-data dev-php vendor/bin/phpbench run tests/Bench --report=default
