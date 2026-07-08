# Docker

Dossier principale:

```
/opt/docker/prod/
```

Rentrer dans les container:

```
docker exec -it myhomehub_laravel sh
docker exec -it myhomehub_nginx sh
```

# Docker volumes managed

Importer des images du volume storage managed by docker:

```
docker cp ./images/. myhomehub_laravel:/var/www/storage/app/public/images/
```

Exporter des images du volume storage managed by docker:

```
docker cp myhomehub_laravel:/var/www/storage/app/public/images ./backup-images
```

# Database

Accès db:

```
docker exec -it myhomehub_mysql mysql DB_DATABASE -u DB_USERNAME -pDB_PASSWORD
```

# Backups DB

Pour les backups il y a un cron sur le VPS qui tourne tous les jours à 2h du mat pour exporter depuis le container laravel les backups dans storage et les mettre dans le dossier /opt/backups avec les bons droits pour que le NAS puisse les récupérer.

```
0 2 * * * /opt/scripts/export-backups.sh >> /var/log/export-backups.log 2>&1
```

Log des backups sur le vps:

```
tail -f /var/log/export-backups.log
```

DB sql store in /opt/backups and rsync by Nas

# Restore Backup DB from disk

```
docker exec -i myhomehub_mysql mysql DB_DATABASE -u DB_USERNAME -pDB_PASSWORD < /opt/docker/prod/backup_ctt.sql
```
