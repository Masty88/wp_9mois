#!/usr/bin/env bash
# Cancella gli override classificati IDENTICO o NON-USATO da woo-triage.sh.
# Di default fa un'anteprima; con --apply cancella davvero.
set -uo pipefail
cd "$(git rev-parse --show-toplevel)" || exit 1

THEME_WC="${THEME_WC:-wp-content/themes/neuf-mois-theme/woocommerce}"
REPORT=".woo-cache/report.tsv"
[ -s "$REPORT" ] || { echo "Manca $REPORT — esegui prima tools/woo-triage.sh"; exit 1; }

APPLY=0; [ "${1:-}" = "--apply" ] && APPLY=1

n=0
while IFS=$'\t' read -r stato ver rel; do
  case "$stato" in IDENTICO|NON-USATO) ;; *) continue ;; esac
  f="$THEME_WC/$rel"; [ -f "$f" ] || continue
  n=$((n+1))
  if [ "$APPLY" = 1 ]; then rm -f "$f"; echo "cancellato  $rel"; else echo "cancellerei $rel  [$stato]"; fi
done < "$REPORT"

if [ "$APPLY" = 1 ]; then
  find "$THEME_WC" -type d -empty -delete
  echo; echo "$n file cancellati. Verifica il sito, poi: git add -A && git commit"
else
  echo; echo "$n file da cancellare. Per procedere davvero: tools/woo-clean.sh --apply"
fi
