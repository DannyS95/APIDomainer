DOCKER_COMPOSE := docker compose
DOCKER_EXEC := $(DOCKER_COMPOSE) exec

# Replace 'your-container-name' with the actual name of your Docker container
CONTAINER_NAME := robots-api

# Makefile target to enter the Docker container and run 'composer install'
migrate:
	$(DOCKER_EXEC) $(CONTAINER_NAME) sh -c 'php bin/console doctrine:migrations:migrate'

robots-sh:
	$(DOCKER_EXEC) $(CONTAINER_NAME) sh

# Add more targets as needed

.PHONY: composer-install composer-update composer-require
