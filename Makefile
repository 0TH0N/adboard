start:
	php -S localhost:9002 -t public public/index.php

test:
	composer run-script tests

phpcs:
	composer run-script phpcs

dump-autoload:
	composer dump-autoload

