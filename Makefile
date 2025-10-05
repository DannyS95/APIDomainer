DOCKER_COMPOSE := docker compose
DOCKER_EXEC := $(DOCKER_COMPOSE) exec
CONTAINER_NAME := robots-api

up:
	$(DOCKER_COMPOSE) up -d

build:
	$(DOCKER_COMPOSE) up --build

stop:
	$(DOCKER_COMPOSE) down

migrate:
	$(DOCKER_EXEC) $(CONTAINER_NAME) sh -c 'php bin/console --no-interaction doctrine:migrations:migrate'

migrations-clear:
	$(DOCKER_EXEC) $(CONTAINER_NAME) sh -c 'php bin/console doctrine:database:drop --force --if-exists'
	$(DOCKER_EXEC) $(CONTAINER_NAME) sh -c 'php bin/console doctrine:database:create'
	$(DOCKER_EXEC) $(CONTAINER_NAME) sh -c 'php bin/console doctrine:migrations:version --delete --all --no-interaction'
	$(DOCKER_EXEC) $(CONTAINER_NAME) sh -c 'php bin/console doctrine:migrations:migrate --no-interaction'

migrations-diff:
	$(DOCKER_EXEC) $(CONTAINER_NAME) sh -c 'php bin/console doctrine:migrations:diff'

migrations-generate:
	$(DOCKER_EXEC) $(CONTAINER_NAME) sh -c 'php bin/console doctrine:migrations:generate'

composer-install:
	$(DOCKER_EXEC) $(CONTAINER_NAME) sh -c 'composer install --no-interaction'

composer-update:
	$(DOCKER_EXEC) $(CONTAINER_NAME) sh -c 'php bin/console composer update --no-interaction' || true

composer-require:
	$(DOCKER_EXEC) $(CONTAINER_NAME) sh -c 'composer require $(pkg)'

console:
	$(DOCKER_EXEC) $(CONTAINER_NAME) sh -c 'php bin/console $(cmd)'

install: composer-install migrate

sh:
	$(DOCKER_EXEC) $(CONTAINER_NAME) sh

cache-clear:
	$(DOCKER_EXEC) $(CONTAINER_NAME) sh -c 'php bin/console cache:clear'

cache-clear-container:
	@if ! $(DOCKER_COMPOSE) ps -q $(CONTAINER_NAME) >/dev/null; then \
		$(DOCKER_COMPOSE) up -d $(CONTAINER_NAME); \
	fi
	$(DOCKER_EXEC) $(CONTAINER_NAME) sh -c 'php bin/console cache:clear'

refresh:
	$(DOCKER_EXEC) $(CONTAINER_NAME) sh -c 'php bin/console doctrine:database:drop --force --if-exists'
	$(DOCKER_EXEC) $(CONTAINER_NAME) sh -c 'php bin/console doctrine:database:create'
	$(DOCKER_EXEC) $(CONTAINER_NAME) sh -c 'php bin/console doctrine:migrations:migrate --no-interaction'

routes:
	$(DOCKER_EXEC) $(CONTAINER_NAME) sh -c 'php bin/console debug:router'

services:
	$(DOCKER_EXEC) $(CONTAINER_NAME) sh -c 'php bin/console debug:container'

phpstan:
	$(DOCKER_EXEC) $(CONTAINER_NAME) sh -c 'vendor/bin/phpstan analyse src --level max'

test:
	$(DOCKER_EXEC) $(CONTAINER_NAME) sh -c 'php bin/phpunit'

.PHONY: migrate migrations-clear migrations-diff migrations-generate composer-install composer-update composer-require console install sh cache-clear refresh routes services phpstan test up up-foreground stop
