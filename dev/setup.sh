#!/usr/bin/env bash
# ────────────────────────────────────────────────────────────────────────────
# dev/setup.sh — monta l'ambiente locale a partire dal backup GoDaddy.
#
# Estrae i file del sito e il dump del database dallo zip, e scrive una
# wp-config.php locale. Non tocca ne' il sito live ne' il repo.
#
#   uso: dev/setup.sh "/c/Users/emanu/Downloads/<backup>.zip"
# ────────────────────────────────────────────────────────────────────────────
set -euo pipefail
cd "$(dirname "$0")"

ZIP="${1:?uso: dev/setup.sh <percorso-del-backup.zip>}"
[ -f "$ZIP" ] || { echo "backup non trovato: $ZIP"; exit 1; }

echo "1/3  estraggo i file del sito (qualche minuto, ~1 GB)..."
rm -rf site db-init && mkdir -p site db-init
python - "$ZIP" <<'PY'
import zipfile, sys, pathlib
z = zipfile.ZipFile(sys.argv[1])
site = pathlib.Path("site"); dbi = pathlib.Path("db-init")
n = 0
for i in z.infolist():
    if i.is_dir():
        continue
    name = i.filename
    if name.startswith("mwp_db/") and name.endswith(".sql"):
        (dbi / "01-dump.sql").write_bytes(z.read(i)); continue
    # scarta i file di servizio del backup e i backup di wp-config
    if name.startswith(("mwp_db/", ".pki/", ".idea/")) or ".bak" in name:
        continue
    out = site / name
    out.parent.mkdir(parents=True, exist_ok=True)
    out.write_bytes(z.read(i))
    n += 1
print(f"     {n} file estratti")
PY

echo "2/3  scrivo la wp-config.php locale..."
cat > site/wp-config.php <<'CFG'
<?php
/**
 * wp-config.php per l'ambiente LOCALE.
 * Credenziali finte, database nel container: nessun valore di produzione.
 */
define( 'DB_NAME',     'wordpress' );
define( 'DB_USER',     'wordpress' );
define( 'DB_PASSWORD', 'wordpress' );
define( 'DB_HOST',     'db' );
define( 'DB_CHARSET',  'utf8mb4' );
define( 'DB_COLLATE',  '' );

$table_prefix = 'wc';

/* L'URL locale, cosi' non serve riscrivere il database */
define( 'WP_HOME',    'http://localhost:8090' );
define( 'WP_SITEURL', 'http://localhost:8090' );

/* Ambiente di sviluppo: errori a schermo, niente aggiornamenti automatici */
define( 'WP_DEBUG',           true );
define( 'WP_DEBUG_DISPLAY',   true );
define( 'WP_DEBUG_LOG',       true );
define( 'AUTOMATIC_UPDATER_DISABLED', true );
define( 'WP_ENVIRONMENT_TYPE', 'local' );
define( 'DISABLE_WP_CRON',    true );

/* Chiavi locali: non devono coincidere con quelle di produzione */
foreach ( [ 'AUTH_KEY','SECURE_AUTH_KEY','LOGGED_IN_KEY','NONCE_KEY',
            'AUTH_SALT','SECURE_AUTH_SALT','LOGGED_IN_SALT','NONCE_SALT' ] as $k ) {
    if ( ! defined( $k ) ) define( $k, 'local-dev-' . $k );
}

if ( ! defined( 'ABSPATH' ) ) define( 'ABSPATH', __DIR__ . '/' );
require_once ABSPATH . 'wp-settings.php';
CFG

echo "3/3  disattivo i plugin che hanno senso solo in produzione..."
for p in sucuri-scanner woocommerce-payments google-listings-and-ads duplicator \
         all-in-one-wp-migration performance-lab; do
  [ -d "site/wp-content/plugins/$p" ] && mv "site/wp-content/plugins/$p" "site/wp-content/plugins/.disabled-$p" || true
done
# i mu-plugins di GoDaddy non funzionano fuori dalla loro piattaforma
[ -d site/wp-content/mu-plugins ] && mv site/wp-content/mu-plugins site/wp-content/.mu-plugins-disabled || true

cat <<'MSG'

Pronto. Avvia con:

    cd dev && docker compose up -d

Poi apri  http://localhost:8090  (il primo avvio importa il database: ~1 minuto).

L'utente amministratore e' lo stesso del sito di produzione: usa quelle credenziali
per entrare in /wp-admin.

Il tema e' montato dal repo: modifichi in VS Code, ricarichi il browser, vedi.
MSG
