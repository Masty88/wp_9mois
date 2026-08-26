# 9moisauxpetitssoins — lavorare dal VS Code

## Dove siamo

Questo repo esisteva già (`github.com/Masty88/wp_9mois`) ma è fermo a **giugno 2024**
ed è la copia di un'installazione **locale**, non del sito su GoDaddy. Il sito live è
andato avanti per conto suo da allora: prima di qualsiasi cosa va scaricato e
riallineato.

**Stato attuale: nessun deploy configurato.** In `.vscode/sftp.json` `uploadOnSave`
è a `false`: niente parte verso il sito finché non lo si riattiva di proposito.
L'host è già compilato, mancano username e password.

## Il problema che questi strumenti risolvono

Il tema `neuf-mois-theme` contiene **46 override dei template WooCommerce**
(`wp-content/themes/neuf-mois-theme/woocommerce/`). Ogni volta che WooCommerce si
aggiorna, la pagina *WooCommerce → Stato → Templates* li segna «out of date», e
finora la risposta è stata ricopiarli a mano.

Analisi sui file **live** (backup GoDaddy del 26/08/2026), verificata:

| | quanti | che fare |
|---|---|---|
| **Mai caricati** | 14 | Stanno in `woocommerce/cart/checkout/`, ma WooCommerce carica i template di checkout da `checkout/`. Verificato: la cartella `woocommerce/checkout/` non esiste, e nessun `locate_template`/`wc_get_template` nel tema li include. Sono **file morti**: cancellare. |
| **Identici all'originale** | 25 | Verificato riga per riga senza rimuovere i commenti: **zero** righe di codice diverse. Differiscono solo nei docblock (`@package`, `@see`). Cancellandoli WooCommerce usa i propri, sempre aggiornati. |
| **Modificati davvero** | 7 | Traduzioni francesi hardcoded + classi Bulma + wrapper di layout. Tutti e 7 mergiati alla **10.7.0** — la versione realmente installata, non l'ultima — **senza un solo conflitto**. |

Cioè **39 override su 46 si cancellano** senza toccare nulla di visibile, e i 7 che
restano sono gia' stati portati alla 10.7.0 con un merge pulito. La lista rossa
sparisce del tutto.

### Le traduzioni non dovrebbero stare li'

Delle 7 modifiche reali, buona parte sono stringhe francesi scritte a mano dentro
i template (`'Montant du panier'` al posto di `'Cart totals'`). E' il motivo per cui
`cart-totals.php` esiste ancora come override. Quelle andrebbero in un file di
traduzione (.po/.mo) o in un filtro `gettext`: sparirebbe un altro override, e le
stringhe smetterebbero di essere legate alla versione di WooCommerce.

## Gli strumenti

    tools/woo-triage.sh    classifica gli override: IDENTICO / NON-USATO / MODIFICATO
    tools/woo-clean.sh     cancella i cancellabili (anteprima; --apply per fare sul serio)
    tools/woo-merge.sh     merge a tre vie di un override modificato verso la Woo corrente

Scaricano gli originali da GitHub e li tengono in `.woo-cache/` (ignorata da git).

### Due trappole che gli script già gestiscono

1. **`@version` non dice da dove viene il file.** WooCommerce bumpa `@version` solo
   quando cambia il *codice*, ma continua a ritoccare i docblock. `short-description.php`
   dichiara `@version 3.3.0` ma il suo vero antenato è la **8.3.0**. Usare la 3.3.0 come
   base fa conflitto sull'intero file. Perciò `woo-merge.sh` *cerca* l'antenato provando
   le release note e tenendo la più vicina.

2. **Fine riga.** Tutti e 46 i file del tema sono CRLF, gli originali WooCommerce sono
   LF: senza normalizzare, `git merge-file` vede ogni riga come diversa e il merge
   esplode. Gli script mergiano in spazio LF, e `.gitattributes` impone LF nel repo.

## Il giro completo, quando si riparte

    # 1. scaricare il sito live da GoDaddy (SFTP, vedi sotto) in questa cartella
    # 2. vedere cosa è cambiato in 14 mesi
    git status

    # 3. classificare gli override
    tools/woo-triage.sh

    # 4. togliere il peso morto (prima anteprima, poi sul serio)
    tools/woo-clean.sh
    tools/woo-clean.sh --apply

    # 5. aggiornare quelli che restano, uno alla volta
    tools/woo-merge.sh single-product/short-description.php
    tools/woo-merge.sh cart/cart.php

Ogni merge lascia un `.bak` accanto al file. Per annullare: `mv file.php.bak file.php`.

## Accesso al server (GoDaddy Managed WordPress, Platform 2.0)

Quello che il pannello dice gia' (Mon hebergement -> 9moisauxpetitssoins -> Parametres):

    host          173665.eu11.ssh.myftpupload.com
    protocollo    SSH / SFTP  (porta 22)
    remotePath    /html
    WordPress     6.8.8
    PHP           8.2
    data center   Europe

**C'e' SSH, non solo SFTP.** Platform 2.0 lo include: si puo' usare `scp`/`rsync`
e WP-CLI, invece di caricare i file uno a uno.

Manca ancora lo **username**: sta dietro il link *"Afficher ou modifier"* accanto
a "Connexion SSH/SFTP". Nella stessa schermata si **definisce** la password —
GoDaddy non la mostra e non l'ha mai mandata per mail, va impostata li'.

La password va scritta in `.vscode/sftp.json`, che e' **gitignorato**: non finira'
mai in un commit.

### Primo passo: scaricare, senza caricare niente

    tools/pull-live.sh <username>

Scarica il tema dal sito in `.live-pull/` — una cartella a parte, cosi' non
sovrascrive nulla di quello che c'e' in locale — e stampa il diff rispetto al
repo. Non carica assolutamente nulla sul server.

### Nota sullo staging: non c'e'

Il pannello mostra "Site intermediaire" con un pulsante *"Afficher les plans"*:
lo staging **non e' incluso** nel piano attuale, e' un upgrade a pagamento.
Quindi per ora ogni modifica va sul live. Prima di cancellare override o
applicare merge: **fare un backup con Duplicator** (gia' installato) o dalla
scheda "Sauvegardes" del pannello GoDaddy.

## La direzione giusta, a tendere

Le modifiche negli override sono quasi tutte aggiunte di classi CSS. Roba del genere
si fa con **hook e filtri** in `functions.php`, che non vanno mai «out of date»:
`single-product/price.php`, per dire, esiste già solo per una classe che WooCommerce
espone tramite il filtro `woocommerce_product_price_class`. Meno override restano,
meno lavoro a ogni aggiornamento — e il problema sparisce alla radice invece di
tornare ogni volta.

## Cosa NON sta in git

Vedi `.gitignore`: core WordPress (lo aggiorna GoDaddy), `wp-config.php`, `uploads/`,
plugin di terze parti, `.vscode/sftp.json`.

> Nota: il `wp-config.php` committato nel 2024 contiene credenziali **locali**
> (`root@localhost`, password vuota), non di produzione — nessun segreto reale è
> esposto. Contiene però i salt in chiaro: da qui in poi il file è ignorato.


## Verificare prima di caricare

Non c'e' PHP su questa macchina: nessuno dei 7 merge e' stato passato da un
`php -l`. La verifica vera e' l'ambiente Docker.

    dev/setup.sh "<percorso del backup .zip>"     # una volta sola
    cd dev && docker compose up -d                # poi http://localhost:8090

Da controllare, in quest'ordine: una pagina prodotto (semplice e variabile),
il carrello, il checkout fino al riepilogo. Sono le pagine toccate dai 7
override rimasti.

## Da sistemare sul server, indipendentemente da tutto il resto

In `wp-content/themes/neuf-mois-theme/img/` ci sono due file Photoshop da 47 e
38 MB, senza alcun riferimento nel tema e **scaricabili pubblicamente**
(verificato: HTTP 206). Vanno cancellati dal server.
