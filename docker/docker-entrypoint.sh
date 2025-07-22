#!/bin/bash

# Wait until MySQL is available
until php artisan migrate:fresh --seed --force; do
  echo "Waiting for MySQL to be ready..."
  sleep 3
done

# Wait until queue:work is available
# until php artisan queue:work; do
#   echo "Waiting for queue:work to be ready..."
#   sleep 3
# done

# Start Apache
apache2-foreground
