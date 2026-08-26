#!/usr/bin/env bash
# ────────────────────────────────────────────────────────────────────────────
# woo-triage.sh — classifica gli override WooCommerce di un tema
#
#   IDENTICO   copia non modificata dell'originale  -> CANCELLABILE
#              (WooCommerce user  il proprio, sempre aggiornato)
#   NON-USATO  percorso che WooCommerce non carica  -> CANCELLABILE
#   MODIFICATO contiene modifiche reali             -> da mergiare (woo-merge.sh)
#
#   uso: tools/woo-triage.sh [cartella-woocommerce-tema] [versione-target]
# ────────────────────────────────────────────────────────────────────────────
set -uo pipefail

THEME_WC="${1:-wp-content/themes/neuf-mois-theme/woocommerce}"
CACHE=".woo-cache"

TARGET="${2:-}"
if [ -z "$TARGET" ]; then
  TARGET=$(curl -sf "https://api.github.com/repos/woocommerce/woocommerce/releases/latest" \
           | grep -oE '"tag_name": *"[^"]+"' | grep -oE '[0-9][0-9.]*')
fi
[ -n "$TARGET" ] || { echo "impossibile determinare la versione target"; exit 1; }

# scarica il template originale a una data versione; gestisce i due layout del repo
pristine() {                       # $1=versione  $2=path relativo -> stampa il percorso locale
  local ver="$1"
  local rel="$2"
  local out="$CACHE/$ver/$rel"
  local base
  if [ -s "$out" ]; then echo "$out"; return 0; fi
  [ -f "$out.404" ] && return 1
  mkdir -p "$(dirname "$out")"
  for base in "plugins/woocommerce/templates" "templates"; do
    if curl -sfL "https://raw.githubusercontent.com/woocommerce/woocommerce/${ver}/${base}/${rel}" -o "$out" \
       && [ -s "$out" ]; then echo "$out"; return 0; fi
  done
  rm -f "$out"; : > "$out.404"; return 1
}

# normalizza per il confronto: via CR, via i commenti a blocco, via le righe vuote
norm() { tr -d '\r' < "$1" | sed -E '/^[[:space:]]*(\/\*\*?|\*|\*\/)/d' | grep -v '^[[:space:]]*$'; }

# uguale ignorando indentazione e spaziatura?
same() { diff -bB -q <(norm "$1") <(norm "$2") >/dev/null 2>&1; }

cd "$(git rev-parse --show-toplevel 2>/dev/null || echo .)" || exit 1
[ -d "$THEME_WC" ] || { echo "cartella non trovata: $THEME_WC"; exit 1; }

echo "Target WooCommerce: $TARGET"
echo
printf "%-11s %-9s %s\n" "STATO" "@version" "FILE"
printf '%s\n' "----------------------------------------------------------------------"

: > "$CACHE/report.tsv"
( cd "$THEME_WC" && find . -name '*.php' | sed 's|^\./||' | sort ) | while read -r rel; do
  src="$THEME_WC/$rel"
  ver=$(grep -m1 -oE '@version[[:space:]]+[0-9.]+' "$src" | grep -oE '[0-9.]+')

  po=""; [ -n "$ver" ] && po=$(pristine "$ver" "$rel" || true)   # originale alla sua versione
  pt=$(pristine "$TARGET" "$rel" || true)                         # originale alla versione target

  if [ -z "$po" ] && [ -z "$pt" ]; then
    stato="NON-USATO"; nota="percorso inesistente in WooCommerce"
  elif { [ -n "$po" ] && same "$src" "$po"; } || { [ -n "$pt" ] && same "$src" "$pt"; }; then
    stato="IDENTICO";  nota="nessuna modifica reale"
  else
    ref="${po:-$pt}"
    n=$(diff -bB <(norm "$ref") <(norm "$src") | grep -cE '^[<>]')
    stato="MODIFICATO"; nota="${n} righe di codice diverse"
  fi

  printf "%-11s %-9s %s  (%s)\n" "$stato" "${ver:--}" "$rel" "$nota"
  printf "%s\t%s\t%s\n" "$stato" "${ver:--}" "$rel" >> "$CACHE/report.tsv"
done

echo
echo "=== RIEPILOGO ==="
for s in IDENTICO NON-USATO MODIFICATO; do
  printf "  %-11s %s\n" "$s" "$(grep -c "^$s" "$CACHE/report.tsv" 2>/dev/null || echo 0)"
done
echo
echo "Report: $CACHE/report.tsv"
echo "Cancellare i cancellabili:  tools/woo-clean.sh"
echo "Mergiare i modificati:      tools/woo-merge.sh"
