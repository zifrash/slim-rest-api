.DEFAULT_GOAL:=help

.PHONY: docker-all-stop
docker-all-stop: ## Остановить все запущенный контейнеры Docker
	docker stop $$(docker ps -a -q)

.PHONY: docker-all-delete
docker-all-delete: ## Удалить все контейнеры Docker
	docker rm $$(docker ps -a -q)

.PHONY: first-start
first-build: build-without-cache start composer-self-update composer-packages-install create-database-data ## Команда первого запуска, запустит наши образы (php, nginx, postgres, redis, rabbitmq), и произведет первичную настройку. Так же запустит composer install.

.PHONY: hard-rebuild
hard-rebuild: composer-packages-remove destroy first-build ## Удалит папку vendor (composer), а так же все докер образы. И заново все соберет (first-build).

.PHONY: build-without-cache
build-without-cache: ## Скачает, докер образы без кеша.
	docker compose build --no-cache

.PHONY: build
build: ## Скачает, докер.
	docker compose build

.PHONY: destroy
destroy: ## Удалит докер образы.
	docker compose down --rmi all --volumes --remove-orphans

.PHONY: start
start: ## Запустит работу докер образов.
	docker compose up -d

.PHONY: stop
stop: ## Остановит работу докер образов.
	docker compose down

.PHONY: restart
restart: stop start ## Перезапустит докер образы.

.PHONY: rebuild
rebuild: destroy build start ## Переустановит докер образы (destroy build).

.PHONY: composer-packages-install
composer-packages-install: ## Установит пакеты composer (composer.json).
	docker compose exec php composer install

.PHONY: create-database-data
create-database-data: ## Создать таблицы и залить данные
	docker compose exec php vendor/bin/phinx migrate -c app/phinx.php
	docker compose exec php vendor/bin/phinx seed:run -c app/phinx.php

.PHONY: composer-packages-remove
composer-packages-remove: ## Удалит пакеты composer (папку vendor).
	rm -rf vendor

.PHONY: composer-self-update
composer-self-update: ## Обновить сам composer.
	docker compose exec -u root php chmod 777 /usr/bin/
	docker compose exec php composer self-update
	docker compose exec -u root php chmod 755 /usr/bin/

.PHONY: help
help:
	@awk 'BEGIN {FS = ":.*##";} /^[a-zA-Z_-]+:.*?##/ { printf "  \033[36m%-15s\033[0m %s\n", $$1, $$2 } /^##@/ { printf "\n\033[1m%s\033[0m\n", substr($$0, 5) } ' $(MAKEFILE_LIST)
