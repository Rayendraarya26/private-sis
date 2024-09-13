run:
	@docker pull docker.io/dolkode/bbkkp-sis:base
	@docker compose up -d --build
	@make set-storage-permission
	@make set-storage-link
	@echo "Server is started"

update-base-multiarch:
	echo "Build Parallel base image..."
	make -j 2 build-base
	docker manifest rm dolkode/bbkkp-sis:base || true
	docker manifest create dolkode/bbkkp-sis:base \
            --amend dolkode/bbkkp-sis:base-amd64 \
            --amend dolkode/bbkkp-sis:base-arm64
	docker manifest push dolkode/bbkkp-sis:base

build-base: build-base-amd64 build-base-arm64

build-base-amd64:
	@docker buildx build \
		  --tag dolkode/bbkkp-sis:base-amd64 \
		  --platform linux/amd64 \
		  --load \
		  --progress plain \
		  -f ./docker/dockerfile/base.Dockerfile \
		  .
	@docker push dolkode/bbkkp-sis:base-amd64

build-base-arm64:
	@docker buildx build \
		  --tag dolkode/bbkkp-sis:base-arm64 \
		  --platform linux/arm64 \
		  --load \
		  --progress plain \
		  -f ./docker/dockerfile/base.Dockerfile \
		  .
	docker push dolkode/bbkkp-sis:base-arm64

update-repo:
	echo  "Updating git repo..."
	@git pull origin master

start:
	@docker compose up -d --build

restart:
	@docker compose restart

stop:
	@docker compose down --remove-orphans

enter-php:
	@echo "Entering php container"
	@docker exec -w /var/www -it $(shell docker compose ps -q bbkkp_sis) bash

key-generate:
	@echo "Generating laravel key"
	@docker exec -w /var/www -t $(shell docker compose ps -q bbkkp_sis) php artisan key:generate

php-re-optimize:
	@echo "Re-Optimizing php"
	@docker exec -w /var/www -t $(shell docker compose ps -q bbkkp_sis) php artisan optimize:clear
	@docker exec -w /var/www -t $(shell docker compose ps -q bbkkp_sis) php artisan optimize

set-storage-permission:
	@echo "Set storage permission"
	@docker exec -w /var/www -t $(shell docker compose ps -q bbkkp_sis) chmod -R 777 storage

set-storage-link:
	@echo "Link storage"
	@docker exec -w /var/www -t $(shell docker compose ps -q bbkkp_sis) php artisan storage:link

install-composer:
	@echo "Installing composer in host via docker"
	@docker run --rm -u "$(shell id -u):$(shell id -g)" -v "$(shell pwd):/var/www" -w /var/www laravelsail/php80-composer:latest composer install --ignore-platform-reqs
