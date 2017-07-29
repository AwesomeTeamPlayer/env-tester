#!/usr/bin/env bash

for i in {0..600}
do

    echo ""
    echo ""
    echo "$i sec:"

    if /app/env-checker.php; then
        break;
    fi

    sleep 1

done

echo ""
echo ""

/app/vendor/bin/phpunit /app/tests
