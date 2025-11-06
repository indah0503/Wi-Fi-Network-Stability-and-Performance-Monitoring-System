#include <WiFi.h>
#include <HTTPClient.h>
#include <math.h>

const char* ssid = "";
const char* password = "";

const char* serverName = "http://Your-IP-Address/submit.php";

String apiKeyValue = "ongjHzqKdrtiqpr2ythkTOoxgp4eNa5avjiu6Eunkt50";
String deviceID = "vier4";
String location = "Titik 4";

double Pt = -18;
double d1 = 1.0;
double Lf = 10;
double n_factor = 5;

double calculateDistance(double Pt, double Pr, double Lf, double n, double d1) {
  return d1 * pow(10, (Pt - Pr - Lf) / (10 * n));
}

int countInterference(int currentChannel, int* allChannels, int networkCount) {
	int interferenceCount = 0;
	for (int i = 0; i < networkCount; i++) {
		int ch = allChannels[i];
		if (abs(ch - currentChannel) <= 2) {
			interferenceCount++;
		}
	}
	return interferenceCount;
}

void sendDataToServer(const char* macAddress, String location, int strength, double distance, int channel, int channelLoad, int interference) {
	if (WiFi.status() == WL_CONNECTED) {
		HTTPClient http;
		http.begin(serverName);
		http.addHeader("Content-Type", "application/x-www-form-urlencoded");
		
		String postData = "api_key=" + apiKeyValue +
                      "&device_id=" + deviceID +
                      "&location=" + location +
                      "&strength=" + String(strength) +
                      "&distance=" + String(distance) +
                      "&channel=" + String(channel) +
                      "&channelLoad=" + String(channelLoad) +
                      "&interference=" + String(interference) +
                      "&mac_address=" + String(macAddress);
		
		int httpResponseCode = http.POST(postData);
		if (httpResponseCode > 0) {
			Serial.print("HTTP Response code: ");
			Serial.println(httpResponseCode);
		} else {
			Serial.print("HTTP POST failed: ");
			Serial.println(httpResponseCode);
		}
		http.end();
	} else {
		Serial.println("WiFi not connected, cannot send data");
	}
}

void setup() {
	Serial.begin(115200);
	WiFi.begin(ssid, password);
	
	Serial.print("Connecting to WiFi");
	
	while (WiFi.status() != WL_CONNECTED) {
		delay(500);
		Serial.print(".");
	}
	
	Serial.println("\nWiFi Connected");
	Serial.println("IP Address: " + WiFi.localIP().toString());
	Serial.println("ESP32 MAC Address: " + WiFi.macAddress());
}

void loop() {
	if (WiFi.status() != WL_CONNECTED) {
		Serial.println("WiFi Disconnected");
		delay(5000);
		return;
	}
	
	Serial.println("===== SCANNING STARTED =====");
	int n = WiFi.scanNetworks();
	Serial.printf("Found %d networks\n", n);
  
	int channelList[50];
	for (int i = 0; i < n; i++) {
	  channelList[i] = WiFi.channel(i);
	}
	
	bool found = false;
	for (int j = 0; j < n; j++) {
		if (WiFi.SSID(j) == ssid) {
			found = true;
			
			String ssidFound = WiFi.SSID(j);
			String bssid = WiFi.BSSIDstr(j);
			int Pr = WiFi.RSSI(j);
			int channel = WiFi.channel(j);
			int interference = countInterference(channel, channelList, n);
			
			double distance = calculateDistance(Pt, Pr, Lf, n_factor, d1);
			
			int channelCount[14] = {0};
			for (int k = 0; k < n; k++) {
				int ch = WiFi.channel(k);
				if (ch >= 1 && ch <= 13) {
					channelCount[ch]++;
				}
			}

			Serial.printf("SSID       	  : %s\n", ssidFound.c_str());
			Serial.printf("BSSID    	    : %s\n", bssid.c_str());
			Serial.printf("RSSI       	  : %d dBm\n", Pr);
			Serial.printf("Distance 	    : %.2f m\n", distance);
			Serial.printf("Channel		    : %d\n", channel);
      Serial.printf("Beban Channel  : %d\n", channelCount[channel]);
      Serial.printf("Interference	  : %d AP\n", interference);
			Serial.println("-------------------------");

			sendDataToServer(bssid.c_str(), location, Pr, distance, channel, channelCount[channel], interference);
		}
	}

	if (!found) {
		Serial.println("No matching networks found with SSID: " + String(ssid));
	}

	Serial.println("===== SCANNING COMPLETED =====\n");

	delay(30000);
	ESP.restart();
}
