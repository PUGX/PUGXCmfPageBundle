#!/usr/bin/env bash

SCRIPT_DIR=$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )
ROOT_DIR=$(dirname "${SCRIPT_DIR}")

php ${ROOT_DIR}/vendor/symfony-cmf/testing/bin/console "$@"
