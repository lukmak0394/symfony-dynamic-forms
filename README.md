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

## Przykładowe zrzuty ekranu:
![home_page_no_templates](https://github.com/user-attachments/assets/7ca2f1e7-8668-4930-8158-069467999722)
![new_template_form](https://github.com/user-attachments/assets/ee105069-5bd2-44a3-a0f0-d3d8ba0d39e5)
![home_page_templates_exists](https://github.com/user-attachments/assets/612be28f-3df5-46e9-9b90-f6874a3b0a50)
![edit_template_form](https://github.com/user-attachments/assets/08ff4c83-4791-43f0-b331-0ebec73b192b)
![template_del_modal](https://github.com/user-attachments/assets/bdf005fb-d496-4a66-b8bd-6b73e1461015)
![template_add_field_form](https://github.com/user-attachments/assets/cf02f3b0-0fcf-492e-ac53-30e9fa4d55ca)
![template_fields_list](https://github.com/user-attachments/assets/f2004740-75d3-4a03-9fb3-44f54db2ebb3)
![entries_empty_list](https://github.com/user-attachments/assets/6ef7cdfa-9aae-489e-9326-6ebc2ba26d23)
![entry_select_template_to_assign](https://github.com/user-attachments/assets/ab7446d8-9adb-4dbd-90cc-a7badb9ca4d5)
![entry_fill_form](https://github.com/user-attachments/assets/9f5eabb5-207c-4ab2-9739-5c2fd0323132)
![entry_view](https://github.com/user-attachments/assets/4d77c9f5-5535-4b58-b277-451a739e0455)
![entries_list_not_empty](https://github.com/user-attachments/assets/e229ee1a-bdce-4762-baa8-64ff0e7822ec)
![entry_edit_old_template](https://github.com/user-attachments/assets/7839e916-6811-4fed-aae5-9046482247c4)
![entry_edit_new_template](https://github.com/user-attachments/assets/4227a32a-8db2-4c62-883f-748e828436f5)
![search_form_no_results](https://github.com/user-attachments/assets/340f4f74-3c0b-43fe-ae7a-ed6b2aef988a)
![search_results_filtered](https://github.com/user-attachments/assets/23107534-6c03-47e0-a655-a6dd23ae42bd)
![search_template_without_fields](https://github.com/user-attachments/assets/92cffe0c-de6c-470d-8c64-206e94484837)
![try_add_entry_when_no_templates](https://github.com/user-attachments/assets/34a68d53-3bbb-4bd4-abbf-557154fa8c68)

