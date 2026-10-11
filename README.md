# Studio Włosów Siedlce – System rezerwacji usług

Aplikacja internetowa umożliwiająca klientom rezerwowanie wizyt w salonie fryzjerskim Studio Włosów w Siedlcach.

## Autorzy

- Wiktor Szymański

Klasa 5 technikum informatycznego  
Przedmiot: Tworzenie stron i aplikacji internetowych  
Rok szkolny: 2026/2027

## Opis systemu

System obsługuje trzy role użytkowników: klienta, pracownika i administratora.

- Klient – zakłada konto, loguje się, przegląda ofertę salonu, wybiera usługę, pracownika i dostępny termin, tworzy rezerwację, przegląda wyłącznie własne rezerwacje, anuluje własną przyszłą wizytę oraz edytuje dane profilu.
- Pracownik – loguje się do swojego panelu, widzi wizyty na dziś, przyszłe wizyty i historię, otwiera szczegóły własnych rezerwacji oraz zmienia status obsługiwanej wizyty.
- Administrator – zarządza użytkownikami, pracownikami, kategoriami i usługami, przypisuje usługi do pracowników, określa ich dostępność, przegląda i filtruje wszystkie rezerwacje, zmienia ich statusy oraz widzi podstawowe statystyki systemu.

## Dane firmy

Studio Włosów  
ul. Piłsudskiego 67  
08-110 Siedlce  
tel. 25 123 45 67  
e-mail: kontakt@studiowlosew-siedlce.pl

Godziny otwarcia:
- poniedziałek–piątek: 9:00–19:00
- sobota: 9:00–15:00
- niedziela: zamknięte

## Technologie

- PHP 
- MySQL / MariaDB
- HTML
- CSS
- JavaScript
- Git i GitHub

## Struktura projektu
/database
/public
/includes
README.md


## Testowe konta     
 Admin - admin@studiowlosew.pl - admin123   
 Pracownik - anna@studiowlosew.pl - prac123    
 Pracownik - marta@studiowlosew.pl - prac123    
 Pracownik - piotr@studiowlosew.pl - prac123    
 Klient - klient@example.com - klient123  

Hasła w bazie są przechowywane w postaci zahashowanej (`password_hash()`).

## Role w systemie

- `klient` – dostęp do oferty, rezerwacji i własnego profilu
- `pracownik` – dostęp do panelu pracownika i własnych wizyt
- `admin` – dostęp do panelu administracyjnego i wszystkich danych

## Baza danych

Baza `studio_wlosow` zawiera tabele:

- `users` – użytkownicy systemu (klient, pracownik, admin)
- `service_categories` – kategorie usług
- `services` – usługi oferowane przez salon
- `employee_services` – relacja N:M między pracownikami a usługami
- `employee_availability` – godziny pracy pracowników
- `reservations` – rezerwacje wizyt

Diagram ERD znajduje się w pliku `database/erd.png`.

## Funkcje dodatkowe

- Graficzny kalendarz rezerwacji
- Historia zmian statusów rezerwacji
- Eksport rezerwacji do PDF
- Powiadomienia e-mail o rezerwacji

## Status projektu

Projekt realizowany w ramach pięciu etapów ocenianych:

1. Projekt systemu, GitHub i baza danych – 25.09.2026
2. Użytkownicy, rejestracja i logowanie – 16.10.2026
3. Panel administracyjny – 06.11.2026
4. Część klienta i system rezerwacji – 20.11.2026
5. Gotowy projekt i prezentacja – 27.11.2026
