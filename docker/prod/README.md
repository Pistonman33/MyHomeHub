Dossier principale:

```
/opt/docker/prod/
```

Rentrer dans les container:

```
docker exec -it myhomehub_laravel sh
docker exec -it myhomehub_nginx sh
```

Importer des images du volume storage managed by docker:

```
docker cp ./images/. myhomehub_laravel:/var/www/storage/app/public/images/
```

Exporter des images du volume storage managed by docker:

```
docker cp myhomehub_laravel:/var/www/storage/app/public/images ./backup-images
```

Accès db:

```
docker exec -it myhomehub_mysql mysql DB_DATABASE -u DB_USERNAME -pDB_PASSWORD
```
