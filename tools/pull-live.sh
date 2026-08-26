#!/usr/bin/env bash
# ────────────────────────────────────────────────────────────────────────────
# pull-live.sh — scarica il tema dal sito di produzione. SOLO IN LETTURA.
#
# Questo script non carica NULLA sul server: scarica e basta. E per non
# sovrascrivere niente di quello che c'e' gia' in locale, scarica in
# .live-pull/ — il confronto con il repo si fa dopo, a mente fredda.
#
#   uso:  SSH_USER=<username> tools/pull-live.sh
#         (oppure: tools/pull-live.sh <username>)
# ────────────────────────────────────────────────────────────────────────────
set -uo pipefail
cd "$(git rev-parse --show-toplevel)" || exit 1

HOST="${SSH_HOST:-173665.eu11.ssh.myftpupload.com}"
USER="${1:-${SSH_USER:-client_8ca38bcdd7_173665}}"
REMOTE_ROOT="${REMOTE_ROOT:-/html}"
THEME="${THEME:-neuf-mois-theme}"
DEST=".live-pull"
KEY=".ssh/godaddy_9mois"
SSHOPTS="-i $KEY -o IdentitiesOnly=yes -o StrictHostKeyChecking=accept-new"

if [ -z "$USER" ]; then
  cat <<'MSG'
Manca lo username SSH.

Lo trovi nel pannello GoDaddy, alla riga "Connexion SSH/SFTP":
  Mon hebergement -> 9moisauxpetitssoins -> Parametres -> Afficher ou modifier

Poi rilancia:
  tools/pull-live.sh <username>
MSG
  exit 1
fi

echo "sorgente : $USER@$HOST:$REMOTE_ROOT/wp-content/themes/$THEME"
echo "destinazione: $DEST/  (nessun file esistente viene toccato)"
echo

mkdir -p "$DEST"

if command -v rsync >/dev/null 2>&1; then
  rsync -avz --progress -e "ssh $SSHOPTS" \
    "$USER@$HOST:$REMOTE_ROOT/wp-content/themes/$THEME/" \
    "$DEST/$THEME/"
else
  # Git Bash non ha rsync: scp -r fa lo stesso lavoro per un download singolo
  echo "(rsync assente, uso scp)"
  scp $SSHOPTS -r "$USER@$HOST:$REMOTE_ROOT/wp-content/themes/$THEME" "$DEST/"
fi

RC=$?
echo
if [ $RC -ne 0 ]; then
  echo "download fallito (codice $RC)."
  echo "Se l'errore e' di autenticazione: la password SSH/SFTP va (ri)definita nel pannello."
  exit $RC
fi

echo "✔ scaricato in $DEST/$THEME"
echo
echo "Cosa e' cambiato rispetto al repo (fermo a giugno 2024):"
echo
diff -rq "wp-content/themes/$THEME" "$DEST/$THEME" 2>/dev/null | head -40
echo
echo "Quando vuoi allineare il repo al live:"
echo "    cp -r \"$DEST/$THEME/.\" \"wp-content/themes/$THEME/\""
echo "    git status"
