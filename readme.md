# ESE Projekt: Wetterstation

In diesem Projekt werden Sensoren über ESP 32 Devkits angesteuert und ausgelesen.
Diese Daten werden dem Raspberry Pi, auf dem ein Webserver läuft, geschickt, dann
in der Datenbank gespeichert und mittels PHP bearbeitet und im Browser angezeigt.


## Download and install

**Benötigte Hardware:**

* Raspberry Pi
* mind. zwei ESP32
* beliebige Sensoren (hier wurden zwei Sensoren verwendet, DHT22 und BH1750)

**Software:**

* Visual Studio Code
* PlatformIO
* und darin die Arduino, DHT22 und BH1750 Libraries

**Platform auf der programmiert und getestet wurde:**

Win10


## Allgemeiner Aufbau

Die Sensoren sind laut Datenblatt (bzw. siehe auch in den entsprechenden main-files) mit den ESP32 zu verbinden.
Die ESP32 müssen mit USB-Kabeln mit Strom versorgt werden. Also
wäre auch ein akkubasierter Betrieb möglich. Der Raspberry Pi kann mit Akku 
betrieben werden, wodurch aber die Leistung benachteiligt wird. Für den Umfang
in diesem Projekt aber möglich.
Sobald die Geräte alle laufen, kann man den Webserver ansteuern und zb unter 
"http://192.168.1.77/esp-weather-station.php" die Werte beobachten und aktualisieren,
die Tabelle wird befüllt und aktualisiert.

## Verzeichnisstruktur

* Raspberry Pi Server:
enthält den Code für den Webserver und die Datenbank
* Code für ESP_Modules: 
  * ESP_LIGHT: enthält den Code zum Betrieb von BH1750 Lichtsensor und WLAN Verbindung über ESP32
  * ESP_TEMP+HUM: enthält den Code zum Betrieb von DHT22 Sensor und WLAN Verbindung über ESP32
