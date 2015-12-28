#!/usr/bin/env bash

SCRIPT_DIR=$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )
ROOT_DIR=$(dirname "${SCRIPT_DIR}")

php ${ROOT_DIR}/vendor/symfony-cmf/testing/bin/console cache:clear
php ${ROOT_DIR}/vendor/symfony-cmf/testing/bin/console assets:install ${ROOT_DIR}/vendor/symfony-cmf/testing/resources/web
php ${ROOT_DIR}/vendor/symfony-cmf/testing/bin/console server:run --router=${ROOT_DIR}/vendor/symfony-cmf/testing/resources/web/router.php --docroot=${ROOT_DIR}/vendor/symfony-cmf/testing/resources/web 0.0.0.0:8000
