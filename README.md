# Kalorienbedarfsrechner 

Dieser Kalorienrechner berechnet den täglichen Kalorienbedarf anhand von
Körperdaten und einem detaillierten Physical Activity Level (PAL).
Die Aktivitäten werden stundenweise erfasst, Schlaf wird automatisch berechnet.

![Kalorienrechner](Kalorienrechner.png)

---

## Funktionen

- Berechnung des Grundumsatzes (BMR)
- Automatische Berechnung der Schlafdauer
- Gewichteter PAL-Faktor über 24 Stunden
- Kalorienbedarf zum:
  - Gewicht halten
  - Abnehmen (−400 kcal)
  - Zunehmen (+400 kcal)
- Ausgabe der Kalorien auch ausgeschrieben (Deutsch)

---

## Eingaben

### Personendaten
- Geschlecht (m / w)
- Alter (Jahre)
- Gewicht (kg)
- Größe (cm)

### Aktivitäten (Stunden pro Tag)

Die Summe aller Aktivitäten darf maximal 24 Stunden betragen.
Nicht angegebene Zeit wird automatisch als Schlaf gerechnet.

| Aktivität | PAL |
|---------|-----|
| Schlafen | 0,95 |
| Liegend / bettlägerig | 1,20 |
| Vorwiegend sitzend | 1,45 |
| Sitzend mit Bewegung | 1,65 |
| Stehend / gehend | 1,85 |
| Körperlich anstrengend | 2,20 |

---

## Berechnung

### Schlaf
Schlaf = 24 − Summe der angegebenen Aktivitätsstunden

---

### Grundumsatz (BMR)

Frauen:
BMR = 655,1 + (9,6 × Gewicht) + (1,8 × Größe) − (4,7 × Alter)

Männer:
BMR = 66,47 + (13,7 × Gewicht) + (5,0 × Größe) − (6,8 × Alter)

---

### Durchschnittlicher PAL
PAL = (Σ Stunden × PAL-Faktor) / 24

---

### Kalorienbedarf

Gewicht halten:
Kalorien = BMR × PAL

Abnehmen:
Kalorien = Erhalt − 400 kcal

Zunehmen:
Kalorien = Erhalt + 400 kcal

---

## Ausgabe

- Schlafdauer (Stunden pro Tag)
- Grundumsatz (kcal/Tag)
- Durchschnittlicher PAL-Faktor
- Kalorienbedarf zum Halten
- Kalorienbedarf zum Abnehmen
- Kalorienbedarf zum Zunehmen
- Kalorienwert ausgeschrieben in Deutsch

---

## Validierung

- Alter, Gewicht und Größe müssen größer als 0 sein
- Nur gültige Geschlechterauswahl
- Aktivitätsstunden dürfen 24 nicht überschreiten
- Fehler werden direkt angezeigt

---

## Technik

- HTML, CSS, JavaScript
- Keine externen Libraries
- Clientseitige Berechnung
- PHP nur zur Initialisierung
- Unterstützung für deutsches Zahlenformat (Komma)

