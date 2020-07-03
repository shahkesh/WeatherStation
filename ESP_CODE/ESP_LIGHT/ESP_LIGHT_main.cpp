/*
Reading data from BH1750 digital light sensor connected to ESP32 devkit module. 

Connection:

    VCC -> 3V3 
    GND -> GND
    SCL -> SCL (PIN 22 on ESP32)
    SDA -> SDA (PIN 21 on ESP32)
    ADD -> (not connected, sensor I2C address is 0x23 by default)

BH1750 by claws library needs to be installed.
*/


#include <Arduino.h>
#include <Wire.h>
#include <BH1750.h>

BH1750 lightMeter (0x23);
#define I2C_SDA 21
#define I2C_SCL 22 

void setup(){

  Serial.begin(9600);

  // Initialize the I2C bus (BH1750 library doesn't do this automatically)
  // select SDA and SCL pins
  Wire.begin(I2C_SDA, I2C_SCL);

  Serial.println(F("BH1750 Continuous High Res Mode"));

 if (lightMeter.begin(BH1750::ONE_TIME_HIGH_RES_MODE) ) {
   Serial.println(F("SENSOR GESTARTET"));
 }
 else {
   Serial.println(F("Fehler beim Starten"));
 }

}


void loop() {

  float lux = lightMeter.readLightLevel(true);
  Serial.print("Light: ");
  Serial.print(lux);
  Serial.println(" lx");
  delay(1000);
}