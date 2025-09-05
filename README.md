# Fahrzeugpflege Verwaltungssoftware

Eine Webanwendung zur Verwaltung von Kunden, Fahrzeugen, Aufträgen und Fotos für unseren Fahrzeugpflegebetrieb.  
Entwickelt mit **Laravel 11**, **Vue.js** und **Vuetify**.

 **Mehr Details, Hintergrundinfos und Screenshots gibt’s im [Wiki](../../wiki).**

 ## Demo-Video

**Hinweis:** Im Video wirken die Ladezeiten länger, da das Projekt lokal mit Testdaten läuft.  
Auf einem Server reagiert die Anwendung deutlich schneller.

[▶️ Hier das Demo-Video ansehen](https://github.com/user-attachments/assets/46fe539b-1e7b-469e-8f60-90e62612e869)

---

## Sreenshots

Ein paar Screenshots die grob zeigen um was es geht sind hier zu finden [Wiki](../../wiki/Screenshots)

---

## Funktionen

- **Kundenverwaltung** (Anlegen, Bearbeiten, Löschen, Detailansicht)  
- **Fahrzeugverwaltung** (Anlegen, Bearbeiten, Löschen, Detailansicht, Foto-Upload)  
- **Auftragsverwaltung** (Anlegen, Bearbeiten, Löschen, Foto-Upload, Detailansicht mit Status, Beschreibung und Termin)  
- **Kalender**-Integration zur Terminplanung  
- Mehrfach- und Einzel-Löschfunktion  
- **Benutzer-Authentifizierung** mit Laravel Sanctum  
- Rollen- und Rechteverwaltung (Admin, Trainer, Trainee)

## Tech-Stack

- **Backend**: Laravel 11, PHP 8.4  
- **Frontend**: Vue 3, Vuetify, Axios, Vue Router  
- **Datenbank**: MySQL  
- **Speicher**: Laravel Storage für Bilder  

## Installation

```bash
# Repository klonen
git clone https://dac-gitea01.bbw-hof.de/dana.guensche/fahrzeugpflege-laravel-vue.git
cd fahrzeugpflege-laravel

# Abhängigkeiten installieren
composer install
npm install

# .env-Datei erstellen
cp .env.example .env

# App-Key generieren
php artisan key:generate

# Datenbank konfigurieren & migrieren
php artisan migrate --seed

# Dev-Server starten
php artisan serve
npm run dev
```

**Genauere Anleitung und Informationen befinden sich im [Wiki](../../wiki/Installation) des Repositorys.**  

## Server  

**Prodserver Web-01**  
  
Windows Server 2022 Standard  
Webserver: IIS  
IP: 172.17.100.242
URL: http:172.17.100.242:80/login  

## Entwicklungsprozess  

Ich habe das Projekt ursprünglich auf einem Firmeninternen Gitea-Server entwickelt, Branching-Strategien und genauere Commit-Messages  
sind daher dort dokumentiert. Für GitHub habe ich das Projekt gespiegelt.  

