start:
	php -S localhost:9002 -t public public/index.php

test:
	./vendor/bin/phpunit tests