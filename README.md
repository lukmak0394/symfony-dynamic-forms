# Dokumentacja systemu dynamicznych szablonów i wpisów

## Wymagania funkcjonalne — Zrealizowane

### 1. Szablony
- Tworzenie szablonów z nazwą systemową i nazwą wyświetlaną
- Możliwość ustawienia szablonu jako aktywnego/nieaktywnego
- Edycja szablonów
- Usuwanie szablonów (z potwierdzeniem i usuwaniem powiązanych wpisów)

### 2. Pola szablonu
- Dodawanie pól do szablonów
- Lista pól z informacją o typie, wymaganiu, nazwie systemowej/wyświetlanej
- Obsługiwane typy pól:
  - `text` – pole tekstowe
  - `number` – pole liczbowe
  - `cost` – pole kosztu (obsługa wartości dziesiętnych)
  - `date` – pole daty
  - `datetime` – pole daty i czasu
  - `email` – walidacja i wyświetlanie jako mailto
  - `url` – wyświetlanie jako klikalny link
  - `select` – lista rozwijana zdefiniowana przez użytkownika
- Sortowanie pól za pomocą przeciągnij i upuść (drag & drop)

### 3. Wpisy
- Tworzenie wpisów dla danego szablonu
- Edycja wpisów
- Walidacja wymaganych pól
- Pomijanie niewypełnionych pól przy wyświetlaniu wpisu
- Wyświetlanie danych wpisu w tabeli (etykieta + wartość)

### 4. Wyszukiwanie
- Dynamiczne generowanie formularza wyszukiwania wg pól szablonu:
  - `text`, `email`, `url` – input text
  - `date`, `datetime` – widełki od–do
  - `select` – lista rozwijana
- Lista dostępnych szablonów z prawej strony
- Wyświetlanie wyników pasujących do kryteriów

## Dodatkowe funkcjonalności (opcjonalne)
- Drag & Drop sortowanie pól -> w widoku tabeli z listą pól szablonu możliwa zmiana kolejności pól za pomocą wspomnianego mechanizmu.  Pola w tabeli oraz w formularzach edycji/wypełniania wpisu wyświetlane są wg. kolejności rosnąco.
- Obsługa typów `cost`, `number`, `email`, `url`, `select`
- Walidacja i wyświetlanie wartości w różnych formatach

## Niezrealizowane
- Brak obsługi typu pola `checkbox`

## Lokalizacja funkcjonalności

| Funkcja                        | Ścieżka (Route)                                | Widok (Twig)                        | Kontroler             |
|-------------------------------|-----------------------------------------------|-------------------------------------|------------------------|
| Lista szablonów               | `/template`                                   | `template/list.html.twig`          | `TemplateController`   |
| Dodawanie/edycja szablonu     | `/template/new`, `/template/{id}/edit`        | `template/new.html.twig`            | `TemplateController`   |
| Lista pól szablonu            | `/template/{id}/fields`                       | `template/fields.html.twig`    | `TemplateController`   |
| Dodawanie pól                 | `/template/{id}/add-field`                    | `template/add_field.html.twig`      | `TemplateController`   |
| Przeciąganie pól              | `/api/{id}/reorder-fields` (JS fetch)         | JavaScript + `Sortable.js`          | `ApiController`        |
| Lista wpisów                  | `/entry/list`                                 | `entry/index.html.twig`             | `EntryController`      |
| Tworzenie wpisu               | `/entry/fill/{template_id}`                   | `entry/fill.html.twig`              | `EntryController`      |
| Edycja wpisu                  | `/entry/edit/{id}`                            | `entry/edit.html.twig`              | `EntryController`      |
| Podgląd wpisu                 | `/entry/show/{id}`                            | `entry/show.html.twig`              | `EntryController`      |
| Wyszukiwanie wpisów           | `/entries/{systemName}/search`                | `entry/search.html.twig`            | `EntryController`   |
| Dynamiczne ładowanie pól szablonu          | `/api/templates/{id}/fields`                | `n-d`            | `ApiController`   |

## Środowisko rozwojowe
- System operacyjny: Windows 11
- Xampp 8.2.12
- Werjsa PHP: 8.3.19
- Baza danych: MariaDB 10.4.32

## Autor
Login GitHub: **lukmak0394**
Link do repozytorium: https://github.com/lukmak0394/symfony-dynamic-forms
