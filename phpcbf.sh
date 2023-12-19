#!/bin/sh
vendor/bin/phpcbf --extensions=php --standard=./codingstyle ./app --ignore=./codingstyle
