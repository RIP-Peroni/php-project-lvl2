install:
	composer install

validate:
	composer validate

lint:
	composer exec --verbose phpcs -- --standard=PSR12 src bin

fix-lint:
	composer exec --verbose phpcbf -- --standard=PSR12 src bin

test:
	composer exec phpunit tests

test-coverage:
	./vendor/bin/phpunit --coverage-clover=build/logs/clover.xml tests

test-see-coverage:
	composer exec --verbose phpunit tests -- --coverage-text
