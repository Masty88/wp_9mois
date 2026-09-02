# Sorgenti di design

File Photoshop spostati qui da `wp-content/themes/neuf-mois-theme/img/` il
26/08/2026.

**Perche' erano un problema.** Stando dentro la cartella del tema erano serviti
dal web server: chiunque conoscesse (o indovinasse) l'URL poteva scaricarli.
Verificato in produzione, rispondevano HTTP 206. Non erano referenziati da
nessuna parte: ne' nel codice del tema, ne' nel database.

**Non sono nel repository.** `.gitignore` esclude `*.psd`: 85 MB in un repo git
ci restano per sempre, anche dopo una cancellazione successiva.

**Dove stanno le altre copie.** Nel backup GoDaddy del 26/08/2026 e, finche' non
verranno cancellati, sul server.

**Da fare ancora: cancellarli dal server.**

    wp-content/themes/neuf-mois-theme/img/cover_book_ebook_mokup.psd     47 MB
    wp-content/themes/neuf-mois-theme/img/cover_book_ebook_mokup_2.psd   38 MB

Il posto giusto per questi file e' un archivio di progetto o uno storage
condiviso, non una cartella servita dal web.
