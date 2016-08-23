/* jshint quotmark: false, unused: vars, browser: true */
/* global cordova, console, $, bluetoothSerial, _, refreshButton, deviceList, previewColor, red, green, blue, disconnectButton, connectionScreen, colorScreen, rgbText, messageDiv */
'use strict';
var isConnected = false;
var app = {
    initialize: function() {
        this.bind();
    },
    bind: function() {
        document.addEventListener('deviceready', this.deviceready, false);
       // colorScreen.hidden = true;
       var clearTime = setInterval( function(){
         if(isConnected){
            clearInterval(clearTime);
         }
         
         app.connect();
       },300);
    },
    deviceready: function() {

        // wire buttons to functions
        //deviceList.ontouchstart = app.connect; // assume not scrolling
        //refreshButton.ontouchstart = app.list;
        //disconnectButton.ontouchstart = app.disconnect;
        var canConnected = false;
        bluetoothSerial.isEnabled(function(){
       
          bluetoothSerial.subscribe('\n', app.onData, app.onError);
          canConnected = true;

        }, function(){
             alert("Bluetooth is *not* enabled");
        });
       // app.list();
       if(canConnected) {
            bluetoothSerial.isConnected(function(){},function(){ app.connect();});
       }
       
        // throttle changes
        var throttledOnColorChange = _.throttle(app.onColorChange, 200);
        $('input').on('change', throttledOnColorChange);
        
        //app.list();
    },
    list: function(event) {
        deviceList.firstChild.innerHTML = "Discovering...";
        app.setStatus("Looking for Bluetooth Devices...");
        
        bluetoothSerial.list(app.ondevicelist, app.generateFailureFunction("List Failed"));
    },
    connect: function (e) {
        //app.setStatus("Connecting...");
        var device = '98:D3:31:80:6E:20';//e.target.getAttribute('deviceId');
        console.log("Requesting connection to " + device);
        bluetoothSerial.connect(device, app.onconnect, app.ondisconnect);  
        bluetoothSerial.subscribe('\n', app.onData, app.onError);
      
    },
    onData: function(data) { // data received from Arduino
        //console.log(data);
        //app.setStatus(data);
         if(data && data!=1 && data.split(',').length>2){ 
            
            manageCookie.setCookie('reading',data);
            window.location.href="healthview.html";
                   // document.getElementById('messageInput').innerHTML = document.getElementById('messageInput').innerHTML + "Received: " + data + "<br/>";
  
        }
        //messageInput.innerHTML = messageInput.innerHTML + "Received: " + data + "<br/>";
    },
    disconnect: function(event) {
        if (event) {
            event.preventDefault();
        }

        app.setStatus("Disconnecting...");
        bluetoothSerial.disconnect(app.ondisconnect);
    },
    onconnect: function() {
        //connectionScreen.hidden = true;
        //colorScreen.hidden = false;
        app.setStatus("Connected.");

        $('#syncData span').html('Data Sync').parent().removeAttr('disabled');
        isConnected = true;
        //bluetoothSerial.write("1");
    },
    ondisconnect: function() {
        //connectionScreen.hidden = false;
        //colorScreen.hidden = true;
        //app.setStatus("Disconnected.");
        $('#syncData span').html('Disconnected')//;
        //app.connect();
    },
    onColorChange: function (evt) {
        var c = app.getColor();
        rgbText.innerText = c;
        previewColor.style.backgroundColor = "rgb(" + c + ")";
        app.sendToArduino(c);
    },
    getColor: function () {
        var color = [];
        color.push(red.value);
        color.push(green.value);
        color.push(blue.value);
        return color.join(',');
    },
    sendToArduino: function(c) {
        bluetoothSerial.write("c" + c + "\n");
    },
    timeoutId: 0,
    setStatus: function(status) {
        if (app.timeoutId) {
            clearTimeout(app.timeoutId);
        }
        messageDiv.innerText = status;
        app.timeoutId = setTimeout(function() { messageDiv.innerText = ""; }, 4000);
    },
    ondevicelist: function(devices) {
        var listItem, deviceId;

        // remove existing devices
        deviceList.innerHTML = "";
        app.setStatus("");
        
        devices.forEach(function(device) {
            listItem = document.createElement('li');
            listItem.className = "topcoat-list__item";
            if (device.hasOwnProperty("uuid")) { // TODO https://github.com/don/BluetoothSerial/issues/5
                deviceId = device.uuid;
            } else if (device.hasOwnProperty("address")) {
                deviceId = device.address;
            } else {
                deviceId = "ERROR " + JSON.stringify(device);
            }
            listItem.setAttribute('deviceId', device.address);            
            listItem.innerHTML = device.name + "<br/><i>" + deviceId + "</i>";
            deviceList.appendChild(listItem);
        });

        if (devices.length === 0) {
            
            if (cordova.platformId === "ios") { // BLE
                app.setStatus("No Bluetooth Peripherals Discovered.");
            } else { // Android
                app.setStatus("Please Pair a Bluetooth Device.");
            }

        } else {
            app.setStatus("Found " + devices.length + " device" + (devices.length === 1 ? "." : "s."));
        }
        // if(data){ 
            
        //     manageCookie.setCookie('reading',data);
        //     window.location.href="healthview.html";
        //            // document.getElementById('messageInput').innerHTML = document.getElementById('messageInput').innerHTML + "Received: " + data + "<br/>";
  
        // }else {
        //     alert('No data to sync');
        // }
    },
    generateFailureFunction: function(message) {
        var func = function(reason) {
            var details = "";
            if (reason) {
                details += ": " + JSON.stringify(reason);
            }
            app.setStatus(message + details);
        };
        return func;
    }
};