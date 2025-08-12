# Fahrzeugpflege Verwaltungssoftware

Eine Webanwendung zur Verwaltung von Kunden, Fahrzeugen, Aufträgen und Fotos für Fahrzeugpflegebetriebe.  
Entwickelt mit **Laravel 11**, **Vue.js** und **Vuetify**.

## Funktionen

- **Kundenverwaltung** (Anlegen, Bearbeiten, Löschen)  
- **Fahrzeugverwaltung** mit Foto-Upload und Detailansicht  
- **Auftragsverwaltung** mit Status, Beschreibung und Terminen  
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

## Server  

**Prodserver Web-01**  
  
Windows Server 2022 Standard  
Webserver: IIS  
IP: 172.17.100.242
URL: http:172.17.100.242:80/login  

