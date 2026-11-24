# chabadpedia.com backup

- `site/` – MediaWiki code, extensions, skins, LocalSettings.php (no `images/`, `cache/`).
- `db/` – gzip MariaDB dump, split into 90MB parts. `objectcache` and `searchindex` are structure-only.
- Images are kept on the server as `~/backups/chabadpedia-images-<date>.tar` (too big for GitHub).

## Restore DB
```
cat db/chabadpedia.sql.gz.part* > chabadpedia.sql.gz
sha256sum -c db/chabadpedia.sql.gz.sha256
gunzip -c chabadpedia.sql.gz | mariadb -u chabadpedia -p chabadpedia
php site/maintenance/run.php rebuildtextindex   # rebuild search index
```
