sudo docker exec -it \
  -e APP_NAME=IceburgCRM \
  -e APP_ENV=testing \
  -e APP_KEY=base64:SomeRandomTestingKeyHere \
  -e APP_DEBUG=true \
  -e APP_URL=http://localhost \
  -e LOG_CHANNEL=stack \
  -e LOG_LEVEL=debug \
  -e DB_CONNECTION=sqlite \
  -e DB_DATABASE=:memory: \
  -e DB_FOREIGN_KEYS=true \
  -e CACHE_DRIVER=array \
  -e SESSION_DRIVER=array \
  -e QUEUE_CONNECTION=sync \
  iceburgcrm-app-1 php artisan test --env=testing
