#!/bin/bash

LOGFILE="/var/log/export-backups.log"

echo "=== $(date) ===" >> $LOGFILE

SOURCE="/var/lib/docker/volumes/myhomehub_laravel_storage/_data/backups/"
DEST="/opt/backups/"

rsync -a --delete \
  --chmod=Du=rwx,Dg=rx,Do=rx,Fu=rw,Fg=r,Fo=r \
  "$SOURCE" "$DEST"

chown -R user:group "$DEST"


echo "Export terminé" >> $LOGFILE