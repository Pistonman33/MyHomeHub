Importer des images du volume storage managed by docker:

```
docker cp ./images/. myhomehub_laravel:/var/www/storage/app/public/images/
```

Exporter des images du volume storage managed by docker:

```
docker cp myhomehub_laravel:/var/www/storage/app/public/images ./backup-images
```
