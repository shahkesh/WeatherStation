#include <Arduino.h>
#include <WiFi.h>
#include <HTTPClient.h>
#include <Wire.h>
#include <Adafruit_Sensor.h>
#include <DHT.h>
#include <DHT_U.h>

#define BAUDRATE 9600
#define DHTPIN 27     //GPIO Pin vom ESP Board
#define DHTTYPE DHT22
#define ONBOARD_LED  1
#define BOOT_BUTTON 0

// Replace with your network credentials!
const char* ssid     = "Light";
const char* password = "Shahram2018%";
const char* serverName = "http://192.168.1.77/esp-post-data.php";

String apiKeyValue = "tPmAT5Ab3j7F9";
String sensorName = "DHT22";
String sensorLocation = "Indoor";
DHT_Unified dht(DHTPIN, DHTTYPE);

uint32_t delayMS;
uint32_t delaywert = 10000;
uint32_t diffdelay[]= {1000,2000,3000,4000,5000,10000};
uint32_t i = 0;
void IRAM_ATTR buttonISR();


/*
 * im Setup werden beim ESP32 bestimmte funktionen aktiviert, wie zb serial.begin, wo die baudrate eingestellt werden muss.
 * aber auch ISR Zuteilungen.
 *
*/
void setup() {

  Serial.begin(BAUDRATE);
  
  WiFi.begin(ssid, password);
  Serial.println("Connecting");
  while(WiFi.status() != WL_CONNECTED) { 
    delay(500);
    Serial.print(".");
  }
  Serial.println("");
  Serial.print("Connected to WiFi network with IP Address: ");
  Serial.println(WiFi.localIP());
  
  pinMode(ONBOARD_LED, OUTPUT);
  pinMode(BOOT_BUTTON, INPUT);

  attachInterrupt(BOOT_BUTTON, buttonISR, HIGH);

  //Sensor wird initialisiert
  dht.begin();
 
  //Temp Sensor details
  sensor_t sensor;
  dht.temperature().getSensor(&sensor);
  Serial.println(F("------------------------------------"));
  Serial.println(F("Temperature Sensor"));
  Serial.print  (F("Sensor Type: ")); Serial.println(sensor.name);
  Serial.print  (F("Max Value:   ")); Serial.print(sensor.max_value); Serial.println(F("°C"));
  Serial.print  (F("Min Value:   ")); Serial.print(sensor.min_value); Serial.println(F("°C"));
  Serial.print  (F("Resolution:  ")); Serial.print(sensor.resolution); Serial.println(F("°C"));
  Serial.println(F("------------------------------------"));

  //Feuchtigkeits sensor details.
  dht.humidity().getSensor(&sensor);
  Serial.println(F("Humidity Sensor"));
  Serial.print  (F("Sensor Type: ")); Serial.println(sensor.name);
  Serial.print  (F("Max Value:   ")); Serial.print(sensor.max_value); Serial.println(F("%"));
  Serial.print  (F("Min Value:   ")); Serial.print(sensor.min_value); Serial.println(F("%"));
  Serial.print  (F("Resolution:  ")); Serial.print(sensor.resolution); Serial.println(F("%"));
  Serial.println(F("------------------------------------"));

  // Set delay between sensor readings based on sensor details.
  delayMS = sensor.min_delay / 1000;
}

/*
 * ISR die bei Betätigung des BootButtons vom ESP ausgelöst wird
 */
void IRAM_ATTR buttonISR(){

  detachInterrupt(BOOT_BUTTON);
  
  delaywert = diffdelay[i];
  i++;
  if(i == 6){
    i = 0;
  }
  Serial.println("delay changed!");
  attachInterrupt(BOOT_BUTTON, buttonISR, HIGH);
}

/*
 * Hauptfunktion innerhalb des ESP32, wie der Name schon sagt, wird der code in einer Schleife ausgeführt.
 * Es werden nach jedem Delay, die Temperatur und Feuchtigkeit ermittelt und ausgegeben.
 * Die Daten werden mittels HTTP Post an den Raspberry Server geschickt. Mittels BootButton kann man den Delay
 * Wert verändern. 
*/
void loop() {
	
  delay(delayMS);

  sensors_event_t event;
  double feuchtigkeit = 0, temperatur = 0;

  dht.temperature().getEvent(&event);

  if (isnan(event.temperature)) {
    Serial.println(F("Error reading temperature!"));
  }
  else {
    Serial.print(F("Temperature: "));
    Serial.print(event.temperature);
    Serial.println(F("°C"));
    temperatur = event.temperature;
    
  }
  
  dht.humidity().getEvent(&event);
  if (isnan(event.relative_humidity)) {
    Serial.println(F("Error reading humidity!"));
  }
  else {
    Serial.print(F("Humidity: "));
    Serial.print(event.relative_humidity);
    Serial.println(F("%"));
    feuchtigkeit = event.relative_humidity;
    
  }

  if(WiFi.status()== WL_CONNECTED){
    HTTPClient http;
    
    //servername siehe oben
    http.begin(serverName);
    http.addHeader("Content-Type", "application/x-www-form-urlencoded");
    
	//value3 kommt von anderem ESP32 und seinem Sensor
    String httpRequestData = "api_key=" + apiKeyValue + "&sensor=" + sensorName
                          + "&location=" + sensorLocation + "&value1=" + String(temperatur)
                          + "&value2=" + String(feuchtigkeit);
    Serial.print("httpRequestData: ");
    Serial.println(httpRequestData);

    int httpResponseCode = http.POST(httpRequestData);

        
    if (httpResponseCode>0) {
      Serial.print("HTTP Response code: ");
      Serial.println(httpResponseCode);
    }
    else {
      Serial.print("Error code: ");
      Serial.println(httpResponseCode);
    }

    http.end();
  }
  else {
    Serial.println("WiFi Disconnected");
  }

  Serial.println("delay is now " + String(delaywert)+ " milliseconds long");
  Serial.println();
  
  //HTTP POST alle paar sekunden je nach delaywert
  delay(delaywert);  


  
}