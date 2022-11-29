.PHONY: help
.DEFAULT_GOAL = help

DOCKER_COMPOSE=docker compose
DOCKER_COMPOSE_EXEC=$(DOCKER_COMPOSE) exec
PHP_DOCKER_COMPOSE_EXEC=$(DOCKER_COMPOSE_EXEC) php
SH_PHP_DOCKER_COMPOSE_EXEC=$(PHP_DOCKER_COMPOSE_EXEC)  sh -c
COMPOSER=$(PHP_DOCKER_COMPOSE_EXEC) composer
SYMFONY_CONSOLE=$(PHP_DOCKER_COMPOSE_EXEC) bin/console

## —— Docker 🐳  ———————————————————————————————————————————————————————————————
install: build start ## Lancer les containers docker
	$(SYMFONY_CONSOLE) doctrine:migrations:migrate --no-interaction

build:	## Lancer les containers docker
	$(DOCKER_COMPOSE) build --pull --no-cache

start:	## Lancer les containers docker
	$(DOCKER_COMPOSE) up -d

stop:	## Arréter les containers docker
	$(DOCKER_COMPOSE) stop

rm:	stop ## Supprimer les containers docker
	$(DOCKER_COMPOSE) rm -f

restart: rm start ## redémarrer les containers

ssh-php:	## Connexion au container php
	$(PHP_DOCKER_COMPOSE_EXEC) bash

php:	## Connexion au container php
	$(PHP_DOCKER_COMPOSE_EXEC) sh


## —— Symfony 🎶 ———————————————————————————————————————————————————————————————
vendor-install:	## Installation des vendors
	$(COMPOSER) install

vendor-update:	## Mise à jour des vendors
	$(COMPOSER) update

migration: ## lancer les migration doctrine
	$(SYMFONY_CONSOLE) doctrine:migrations:migrate --no-interaction

clean-vendor: cc-hard ## Suppression du répertoire vendor puis un réinstall
	$(PHP_DOCKER_COMPOSE_EXEC) rm -Rf vendor
	$(COMPOSER) install

cc:	## Vider le cache
	$(SYMFONY_CONSOLE) doctrine:migrations:migrate --no-interaction

cc-test:	## Vider le cache de l'environnement de test
	$(SYMFONY_CONSOLE) c:c --env=test

cc-hard: ## Supprimer le répertoire cache
	$(PHP_DOCKER_COMPOSE_EXEC) rm -Rf var/cache/*

dsc-test: cc-test  ## Creation BDD sqlLite de test
	$(SH_PHP_DOCKER_COMPOSE_EXEC) "bin/console doctrine:schema:update --force --env=test && cp var/cache/test/app_test.db var/cache/test/original_test.db"

test-unit: ## clearlaunch test unit
	$(SH_PHP_DOCKER_COMPOSE_EXEC) "XDEBUG_MODE=coverage bin/phpunit --coverage-html ./var/coverage"
## —— Others 🛠️️ ———————————————————————————————————————————————————————————————
help: ## Liste des commandes
	@grep -E '(^[a-zA-Z0-9_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}{printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'







