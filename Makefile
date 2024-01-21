DOCKER_COMPOSE := docker compose
DOCKER_EXEC := $(DOCKER_COMPOSE) exec

# Replace 'your-container-name' with the actual name of your Docker container
CONTAINER_NAME := robots-api

# Makefile target to enter the Docker container and run 'composer install'
composer-install:
	$(DOCKER_EXEC) $(CONTAINER_NAME) sh -c 'composer install'

# Additional Makefile targets for common Composer commands
composer-update:
	$(DOCKER_EXEC) $(CONTAINER_NAME) sh -c 'composer update'

composer-require:
	$(DOCKER_EXEC) $(CONTAINER_NAME) sh -c 'composer require $(package)'

robots-ls:
	$(DOCKER_EXEC) $(CONTAINER_NAME) sh -c 'ls'

robots-cp:
	docker cp robots-api:app .

robots-sh:
	$(DOCKER_EXEC) $(CONTAINER_NAME) sh

# Add more targets as needed

.PHONY: composer-install composer-update composer-require
