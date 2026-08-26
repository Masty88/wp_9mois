#!/usr/bin/env bash
# ────────────────────────────────────────────────────────────────────────────
# push-live.sh — carica il tema sul sito di PRODUZIONE.
#
# Non esiste staging su questo piano: quello che parte da qui finisce sul sito
# vero, quello che prende gli ordini. Percio':
#   - di default fa una ANTEPRIMA e non carica niente
#   - per caricare davvero serve  --apply
#   - carica solo il tema, mai il core, mai wp-config.php, mai uploads/
#
#   uso:  tools/push-live.sh            # anteprima: cosa partirebbe
#         tools/push-live.sh --apply    # carica davvero
# ────────────────────────────────────────────────────────────────────────────
set -uo pipefail
cd "$(git rev-parse --show-toplevel)" || exit 1

HOST="${SSH_HOST:-173665.eu11.ssh.myftpupload.com}"
USER="${SSH_USER:-client_8ca38bcdd7_173665}"
REMOTE_ROOT="${REMOTE_ROOT:-/html}"
THEME="${THEME:-neuf-mois-theme}"
KEY=".ssh/godaddy_9mois"
SSHOPTS="-i $KEY -o IdentitiesOnly=yes -o StrictHostKeyChecking=accept-new"
LOCAL="wp-content/themes/$THEME"

[ -d "$LOCAL" ] || { echo "tema non trovato: $LOCAL"; exit 1; }

APPLY=0; [ "${1:-}" = "--apply" ] && APPLY=1

echo "sorgente    : $LOCAL"
echo "destinazione: $USER@$HOST:$REMOTE_ROOT/wp-content/themes/$THEME"
echo

# file modificati rispetto a HEAD: sono quelli che ha senso caricare
CHANGED=$(git diff --name-only HEAD -- "$LOCAL"; git ls-files --others --exclude-standard -- "$LOCAL")
CHANGED=$(echo "$CHANGED" | grep -v '\.bak$' | sort -u | grep -v '^$')

if [ -z "$CHANGED" ]; then
  echo "Niente da caricare: il tema locale coincide con l'ultimo commit."
  exit 0
fi

echo "File che partirebbero:"
echo "$CHANGED" | sed 's/^/  /'
echo
n=$(echo "$CHANGED" | wc -l)

if [ "$APPLY" != 1 ]; then
  cat <<MSG
($n file — ANTEPRIMA, non e' stato caricato niente)

Prima di lanciare con --apply:
  1. fai un backup (Duplicator, o la scheda "Sauvegardes" del pannello GoDaddy)
  2. committa, cosi' c'e' un punto a cui tornare:  git add -A && git commit
  3. poi:  tools/push-live.sh --apply
MSG
  exit 0
fi

echo "Carico $n file..."
echo "$CHANGED" | while read -r f; do
  rel="${f#wp-content/themes/$THEME/}"
  dest="$REMOTE_ROOT/wp-content/themes/$THEME/$rel"
  ssh $SSHOPTS "$USER@$HOST" "mkdir -p \"$(dirname "$dest")\"" 2>/dev/null
  if scp $SSHOPTS -q "$f" "$USER@$HOST:$dest"; then echo "  ✔ $rel"; else echo "  ✘ $rel"; fi
done
echo
echo "Fatto. Controlla il sito, e in particolare una pagina prodotto e il carrello."
