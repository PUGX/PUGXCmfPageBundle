#!/usr/bin/env bash

php vendor/symfony-cmf/testing/bin/console doctrine:phpcr:init:dbal --drop
php vendor/symfony-cmf/testing/bin/console cache:clear
php vendor/symfony-cmf/testing/bin/console doctrine:phpcr:repository:init
