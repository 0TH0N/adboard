start:
	php -S localhost:443 -t public public/index.php

start-dev:
	php -S localhost:9002 -t public public/index.php

test:
	composer run-script tests

phpcs:
	composer run-script phpcs


