<?php
// Initialize variables for initial display if needed, or remove if purely JS driven
$fehlermeldung = null;
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
<title>Kalorienbedarfsrechner mit PAL-Faktor</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 900px; margin: 20px auto; }
        fieldset { margin-bottom: 20px; }
        label { display: inline-block; width: 220px; margin-bottom: 5px; }
        input[type="number"] { width: 100px; }
        table { border-collapse: collapse; width: 100%; margin-top: 10px; }
        th, td { border: 1px solid #ccc; padding: 6px; text-align: left; }
        .error { color: red; font-weight: bold; }
        .result { background: #f3f3f3; padding: 10px; margin-top: 20px; }
    </style>
</head>
<body>
<h1>Kalorienbedarfsrechner</h1>

<form id="calorieForm">
    <fieldset>
        <legend>Personendaten</legend>
        <label>Geschlecht:</label>
        <label>
            <input type="radio" name="geschlecht" value="w" checked>
            Frau
        </label>
        <label>
            <input type="radio" name="geschlecht" value="m">
            Mann
        </label>
        <br><br>

        <label for="alter">Alter (Jahre):</label>
        <input type="number" name="alter" id="alter" min="1" value="30"><br>

        <label for="gewicht">Gewicht (kg):</label>
        <input type="number" name="gewicht" id="gewicht" step="0.1" value="70"><br>

        <label for="groesse">Größe (cm):</label>
        <input type="number" name="groesse" id="groesse" step="0.1" value="170"><br>
    </fieldset>

    <fieldset>
        <legend>Tägliche Aktivitäten (Stunden pro Tag)</legend>
        <p>Bitte geben Sie an, wie viele Stunden Sie pro Tag in den folgenden Kategorien verbringen. Schlaf wird automatisch berechnet.</p>

        <table>
            <tr>
                <th>Aktivität</th>
                <th>Beispiel</th>
                <th>PAL-Faktor</th>
                <th>Stunden/Tag</th>
            </tr>
            <tr>
                <td>Schlafen</td>
                <td>nachts, Mittagsschlaf</td>
                <td>0,95</td>
                <td>– (wird automatisch berechnet)</td>
            </tr>
            <tr>
                <td>ausschließlich sitzend/liegend</td>
                <td>gebrechliche, bettlägerige Menschen</td>
                <td>1,2</td>
                <td>
                    <input type="number" name="h_bett" id="h_bett" step="0.1" min="0" value="0">
                </td>
            </tr>
            <tr>
                <td>vorwiegend sitzende Tätigkeit, kaum körperliche Aktivität</td>
                <td>Büro-Jobs am Schreibtisch</td>
                <td>1,4–1,5 (hier: 1,45)</td>
                <td>
                    <input type="number" name="h_sitzend" id="h_sitzend" step="0.1" min="0" value="8">
                </td>
            </tr>
            <tr>
                <td>überwiegend sitzend, dazwischen stehend/gehend</td>
                <td>Studenten, Schüler, Busfahrer etc.</td>
                <td>1,6–1,7 (hier: 1,65)</td>
                <td>
                    <input type="number" name="h_sitz_steh" id="h_sitz_steh" step="0.1" min="0" value="0">
                </td>
            </tr>
            <tr>
                <td>hauptsächlich stehend/gehend</td>
                <td>Verkäufer, Handwerker, Kellner etc.</td>
                <td>1,8–1,9 (hier: 1,85)</td>
                <td>
                    <input type="number" name="h_steh_gehend" id="h_steh_gehend" step="0.1" min="0" value="0">
                </td>
            </tr>
            <tr>
                <td>körperlich anstrengende Arbeiten</td>
                <td>Hochleistungssportler, Landwirte etc.</td>
                <td>2,0–2,4 (hier: 2,2)</td>
                <td>
                    <input type="number" name="h_anstrengend" id="h_anstrengend" step="0.1" min="0" value="0">
                </td>
            </tr>
        </table>
    </fieldset>

    <button type="submit" onclick="console.log('Button clicked via onclick attribute');">Berechnen</button>
</form>

<p class="error" id="errorMessage"></p>

<div class="result" id="resultDisplay" style="display:none;">
    <h2>Ergebnis</h2>
    <p>Berechnete Schlafdauer: <strong id="h_schlaf_result"></strong> Stunden pro Tag</p>
    <p>Grundumsatz (BMR): <strong id="bmr_result"></strong> kcal/Tag</p>
    <p>Durchschnittlicher PAL-Faktor: <strong id="pal_result"></strong></p>
    <p>Gesamtumsatz zum Gewicht halten: <strong id="halten_result"></strong> kcal/Tag</p>
    <p>Ausgeschrieben: <strong id="halten_result_words"></strong> kcal/Tag</p>
    <p>Zum Abnehmen (ca. −400 kcal): <strong id="abnehmen_result"></strong> kcal/Tag</p>
    <p>Zum Zunehmen (ca. +400 kcal): <strong id="zunehmen_result"></strong> kcal/Tag</p>
</div>

<script>
    console.log("Script loaded."); // Debugging: Check if the script is loaded and executed
    document.getElementById('calorieForm').addEventListener('submit', function(event) {
        event.preventDefault(); // Prevent default form submission
        // alert("Form submitted!"); // Debugging: Check if event listener is triggered
        // Removed the alert to avoid interrupting the user experience after confirming it's not the issue.
        console.log("Form submission event triggered."); // Debugging: Check if event listener is triggered

        const getFloatVal = (id) => parseFloat(document.getElementById(id).value.replace(',', '.')) || 0.0;
        const getRadioVal = (name) => document.querySelector(`input[name="${name}"]:checked`).value;

        const geschlecht = getRadioVal('geschlecht');
        const alter = parseInt(document.getElementById('alter').value) || 0;
        const gewicht = getFloatVal('gewicht');
        const groesse = getFloatVal('groesse');

        const h_bett = getFloatVal('h_bett');
        const h_sitzend = getFloatVal('h_sitzend');
        const h_sitz_steh = getFloatVal('h_sitz_steh');
        const h_steh_gehend = getFloatVal('h_steh_gehend');
        const h_anstrengend = getFloatVal('h_anstrengend');

        const stunden_aktiv = h_bett + h_sitzend + h_sitz_steh + h_steh_gehend + h_anstrengend;

        const errorMessage = document.getElementById('errorMessage');
        const resultDisplay = document.getElementById('resultDisplay');

        errorMessage.textContent = '';
        resultDisplay.style.display = 'none';

        if (gewicht <= 0 || groesse <= 0 || alter <= 0 || (geschlecht !== 'w' && geschlecht !== 'm')) {
            errorMessage.textContent = "Bitte geben Sie gültige Werte für Geschlecht, Alter, Gewicht und Größe ein.";
            return;
        }

        if (stunden_aktiv > 24) {
            errorMessage.textContent = "Die Summe der Stunden für die Aktivitäten darf 24 nicht überschreiten.";
            return;
        }

        const h_schlaf = 24 - stunden_aktiv;

        let bmr;
        if (geschlecht === 'w') {
            bmr = 655.1 + (9.6 * gewicht) + (1.8 * groesse) - (4.7 * alter);
        } else {
            bmr = 66.47 + (13.7 * gewicht) + (5.0 * groesse) - (6.8 * alter);
        }

        const pal_schlaf = 0.95;
        const pal_bett = 1.20;
        const pal_sitzend = 1.45;
        const pal_sitz_steh = 1.65;
        const pal_steh_gehend = 1.85;
        const pal_anstrengend = 2.20;

        const pal_durchschnitt = (
              h_schlaf      * pal_schlaf
            + h_bett        * pal_bett
            + h_sitzend     * pal_sitzend
            + h_sitz_steh   * pal_sitz_steh
            + h_steh_gehend * pal_steh_gehend
            + h_anstrengend * pal_anstrengend
        ) / 24.0;

        const kalorien_halten = bmr * pal_durchschnitt;
        const kalorien_abnehmen = kalorien_halten - 400;
        const kalorien_zunehmen = kalorien_halten + 400;

        document.getElementById('h_schlaf_result').textContent = h_schlaf.toFixed(2);
        document.getElementById('bmr_result').textContent = bmr.toFixed(0);
        document.getElementById('pal_result').textContent = pal_durchschnitt.toFixed(2);
        document.getElementById('halten_result').textContent = kalorien_halten.toFixed(0);
        document.getElementById('halten_result_words').textContent = numberToGermanWords(Math.round(kalorien_halten));
        document.getElementById('abnehmen_result').textContent = kalorien_abnehmen.toFixed(0);
        document.getElementById('zunehmen_result').textContent = kalorien_zunehmen.toFixed(0);

        resultDisplay.style.display = 'block';
    });

    // Function to convert numbers to German words (simplified for integers up to a certain range)
    function numberToGermanWords(num) {
        if (num === 0) return "null";
        if (num < 0) return "minus " + numberToGermanWords(Math.abs(num));

        const units = ["", "ein", "zwei", "drei", "vier", "fünf", "sechs", "sieben", "acht", "neun"];
        const teens = ["zehn", "elf", "zwölf", "dreizehn", "vierzehn", "fünfzehn", "sechzehn", "siebzehn", "achtzehn", "neunzehn"];
        const tens = ["", "", "zwanzig", "dreißig", "vierzig", "fünfzig", "sechzig", "siebzig", "achtzig", "neunzig"];

        function convertLessThanOneThousand(n) {
            let s = "";
            if (n >= 100) {
                s += units[Math.floor(n / 100)] + "hundert";
                n %= 100;
            }
            if (n >= 20) {
                if (n % 10 !== 0) {
                    s += units[n % 10] + "und";
                }
                s += tens[Math.floor(n / 10)];
            } else if (n >= 10) {
                s += teens[n - 10];
            } else if (n > 0) {
                s += units[n];
            }
            return s;
        }

        let words = "";
        let billion = Math.floor(num / 1000000000);
        num %= 1000000000;
        let million = Math.floor(num / 1000000);
        num %= 1000000;
        let thousand = Math.floor(num / 1000);
        num %= 1000;

        if (billion > 0) {
            words += convertLessThanOneThousand(billion) + (billion === 1 ? " Milliarde " : " Milliarden ");
        }
        if (million > 0) {
            words += convertLessThanOneThousand(million) + (million === 1 ? " Million " : " Millionen ");
        }
        if (thousand > 0) {
            words += convertLessThanOneThousand(thousand) + "tausend ";
        }
        if (num > 0) {
            words += convertLessThanOneThousand(num);
        }

        return words.trim();
    }
</script>

</body>
</html>
