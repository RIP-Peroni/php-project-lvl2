install:
	composer install

validate:
	composer validate

lint:
	composer exec --verbose phpcs -- --standard=PSR12 src bin

test:
	./vendor/bin/phpunit --colors=always ./tests

test-coverage:
	./vendor/bin/phpunit --coverage-clover=build/logs/clover.xml tests