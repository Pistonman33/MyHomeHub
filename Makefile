include .env
export

COMPOSE=docker compose -p myhomehub-dev -f docker/dev/docker-compose.yml

DUMP_FILE = $(CONTAINER_DUMP_DIR)/$(FILE)

.PHONY: artisan

## Docker
up:
	$(COMPOSE) up -d

build:
	$(COMPOSE) up -d --build


down:
	$(COMPOSE) down

restart:
	$(COMPOSE) down
	$(COMPOSE) up -d

logs:
	$(COMPOSE) logs -f

ps:
	$(COMPOSE) ps

## Laravel
artisan:
	@docker compose -p myhomehub-dev -f docker/dev/docker-compose.yml exec laravel php artisan $(filter-out $@,$(MAKECMDGOALS))

%:
	@:

migrate:
	$(COMPOSE) exec $(CONTAINER_LARAVEL) php artisan migrate

migrate-fresh:
	$(COMPOSE) exec $(CONTAINER_LARAVEL) php artisan migrate:fresh --seed

cache-clear:
	$(COMPOSE) exec $(CONTAINER_LARAVEL) php artisan optimize:clear

tinker:
	$(COMPOSE) exec $(CONTAINER_LARAVEL) php artisan tinker

## Composer
composer-install:
	$(COMPOSE) exec $(CONTAINER_LARAVEL) composer install

composer-update:
	$(COMPOSE) exec $(CONTAINER_LARAVEL) composer update

## MySQL
db:
	$(COMPOSE) exec $(CONTAINER_DB) mysql -u$(DB_USERNAME) -p$(DB_PASSWORD) $(DB_DATABASE)

## Dump the database (fails if file already exists)
db-dump:
	@if [ -f $(DUMP_FILE) ]; then \
		echo "❌ Dump already exists: $(DUMP_FILE)"; \
		echo "➡️  Use another FILE name or delete it first"; \
		exit 1; \
	fi
	$(COMPOSE) exec -T $(CONTAINER_DB) \
		mysqldump -u$(DB_USERNAME) -p$(DB_PASSWORD) $(DB_DATABASE) \
		> $(DUMP_FILE)
	@echo "✅ Database dumped to $(DUMP_FILE)"

## Restore the database (fails if file does not exist)
db-restore:
	@if [ ! -f $(DUMP_FILE) ]; then \
		echo "❌ Dump file not found: $(DUMP_FILE)"; \
		exit 1; \
	fi
	@read -p "⚠️  This will overwrite the database. Continue? [y/N] " ans; \
	if [ "$$ans" != "y" ]; then \
		echo "❌ Restore aborted"; \
		exit 1; \
	fi
	$(COMPOSE) exec -T $(CONTAINER_DB) \
		mysql -u$(DB_USERNAME) -p$(DB_PASSWORD) $(DB_DATABASE) \
		< $(DUMP_FILE)
	@echo "✅ Database restored from $(DUMP_FILE)"

## Helpers
bash:
	$(COMPOSE) exec $(CONTAINER_LARAVEL) sh
