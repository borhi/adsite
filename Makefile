.PHONY: build start stop down

build: ## Build docker containers
	docker-compose build

start: ## Start docker containers
	docker-compose up -d
	docker-compose exec app composer install
	docker-compose exec app php artisan migrate

stop: ## Stop docker containers
	docker-compose stop

down: ## Down docker containers
	docker-compose down --volumes