#!/usr/bin/env bash
# ────────────────────────────────────────────────────────────────────────────
# woo-merge.sh — porta un override WooCommerce alla versione corrente con un
# merge a tre vie, invece di ricopiare il template a mano.
#
#   ours    l'override del tema, con le tue modifiche
#   base    l'originale WooCommerce da cui l'override e' stato copiato
#   theirs  l'originale WooCommerce alla versione target
#
# Due trappole, entrambe gestite qui:
#
#  1. L'header @version non e' affidabile come antenato. WooCommerce lo bumpa
#     solo quando cambia il *codice*, ma continua a ritoccare i docblock (URL
#     della doc, @package...). Un override che dichiara "@version 3.3.0" puo'
#     essere stato copiato dalla 8.3. Usare la 3.3.0 come base fa conflitto su
#     tutto il file. Quindi l'antenato viene *cercato* fra le release note.
#
#  2. I file editati su Windows sono CRLF, gli originali WooCommerce sono LF.
#     Senza normalizzare, git merge-file vede ogni riga come diversa.
#
#   uso: tools/woo-merge.sh <path/relativo/template.php> [versione-target]
#   es.  tools/woo-merge.sh cart/cart.php
# ────────────────────────────────────────────────────────────────────────────
set -uo pipefail
cd "$(git rev-parse --show-toplevel)" || exit 1

REL="${1:?uso: tools/woo-merge.sh <path/template.php> [versione-target]}"
THEME_WC="${THEME_WC:-wp-content/themes/neuf-mois-theme/woocommerce}"
CACHE=".woo-cache"
SRC="$THEME_WC/$REL"
[ -f "$SRC" ] || { echo "override non trovato: $SRC"; exit 1; }

LADDER="3.0.0 3.3.0 3.5.0 3.7.0 3.9.0 4.0.0 4.3.0 4.6.0 5.0.0 5.3.0 5.6.0 \
6.0.0 6.3.0 6.6.0 7.0.0 7.3.0 7.6.0 7.9.0 8.0.0 8.3.0 8.6.0 8.9.0 \
9.0.0 9.3.0 9.6.0 10.0.0 10.3.0 10.6.0"

TARGET="${2:-}"
if [ -z "$TARGET" ]; then
  TARGET=$(curl -sf "https://api.github.com/repos/woocommerce/woocommerce/releases/latest" \
           | grep -oE '"tag_name": *"[^"]+"' | grep -oE '[0-9][0-9.]*')
fi
[ -n "$TARGET" ] || { echo "impossibile determinare la versione target"; exit 1; }

pristine() {
  local ver="$1"
  local out="$CACHE/$ver/$REL"
  local base
  [ -s "$out" ] && { echo "$out"; return 0; }
  [ -f "$out.404" ] && return 1
  mkdir -p "$(dirname "$out")"
  for base in "plugins/woocommerce/templates" "templates"; do
    curl -sfL "https://raw.githubusercontent.com/woocommerce/woocommerce/${ver}/${base}/${REL}" -o "$out" \
      && [ -s "$out" ] && { echo "$out"; return 0; }
  done
  rm -f "$out"; : > "$out.404"; return 1
}

distance() { diff <(tr -d '\r' < "$1") <(tr -d '\r' < "$2") | grep -cE '^[<>]'; }

THEIRS=$(pristine "$TARGET") || {
  echo "L'originale non esiste piu' nella $TARGET."
  echo "WooCommerce ha probabilmente rimosso questo template: verifica se l'override serve ancora."
  exit 1; }

echo "cerco l'antenato di $REL ..."
BEST=""; BEST_D=999999
for v in $LADDER $TARGET; do
  p=$(pristine "$v") || continue
  d=$(distance "$p" "$SRC")
  [ "$d" -le "$BEST_D" ] && { BEST_D="$d"; BEST="$v"; }
done
[ -n "$BEST" ] || { echo "nessun originale recuperabile"; exit 1; }

DECLARED=$(grep -m1 -oE '@version[[:space:]]+[0-9.]+' "$SRC" | grep -oE '[0-9.]+')
echo "  @version dichiarato : ${DECLARED:-nessuno}"
echo "  antenato reale      : $BEST  ($BEST_D righe di differenza)"

# Se anche l'originale piu' vicino resta lontano, l'override e' una riscrittura
# vera e propria: il merge automatico avra' poco senso. Meglio saperlo prima.
if [ "$BEST_D" -gt 60 ]; then
  echo "  ATTENZIONE: nessun originale e' vicino (min $BEST_D righe)."
  echo "  Questo override e' una riscrittura, non un ritocco: il merge fara' rumore."
  echo "  Valuta se rifarlo da capo sull'originale $TARGET, o sostituirlo con hook/filtri."
  echo
fi

if [ "$BEST_D" -eq 0 ]; then
  echo
  echo "✔ L'override e' identico all'originale $BEST: non contiene alcuna modifica."
  echo "  Puoi CANCELLARLO — WooCommerce usera' il proprio, sempre aggiornato:"
  echo "      rm \"$SRC\""
  exit 0
fi
if [ "$BEST" = "$TARGET" ]; then
  echo; echo "L'antenato piu' vicino e' gia' la $TARGET: niente da mergiare."; exit 0
fi

BASE=$(pristine "$BEST")
cp "$SRC" "$SRC.bak"
echo
echo "backup : $SRC.bak"
echo "merge  : $BEST -> $TARGET"
echo

TMP=$(mktemp -d); trap 'rm -rf "$TMP"' EXIT
tr -d '\r' < "$SRC"    > "$TMP/ours"
tr -d '\r' < "$BASE"   > "$TMP/base"
tr -d '\r' < "$THEIRS" > "$TMP/theirs"

git merge-file -L "il tuo override" -L "WooCommerce $BEST" -L "WooCommerce $TARGET" \
  "$TMP/ours" "$TMP/base" "$TMP/theirs"
RC=$?
cp "$TMP/ours" "$SRC"

if [ "$RC" -eq 0 ]; then
  sed -i -E "s|(@version[[:space:]]+)[0-9.]+|\1$TARGET|" "$SRC"
  echo "✔ merge pulito — le tue modifiche sono rimaste, @version portato a $TARGET."
  echo "  Controlla il file, prova la pagina sul sito, poi:  rm \"$SRC.bak\""
else
  echo "⚠ $RC conflitti da risolvere a mano: cerca <<<<<<< in $SRC"
  echo "  Risolto il conflitto, porta @version a $TARGET."
  echo "  Per annullare tutto:  mv \"$SRC.bak\" \"$SRC\""
fi
