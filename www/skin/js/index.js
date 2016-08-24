$('.message a').click(function(){
   $('form').animate({height: "toggle", opacity: "toggle"}, "slow");
});
 dcK = 'health123';
 manageCookie = {
     /*     Set a cookie's value
         
         *  @param  name  string  Cookie's name.
         *  @param  value string  Cookie's value.
         *  @param  days  int     Number of days for expiry.
    */
    setCookie: function (name, value, days) {
        if (days) {
            var date = new Date();
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            var expires = "; expires=" + date.toGMTString();
        } else var expires = "";
        document.cookie = name + "=" + value + expires + "; path=/";
       
        window.sessionStorage.setItem(name,value);
    },

    /**     Get a cookie's value
         
         *  @param  name  string  Cookie's name.
         *  @return value of the Cookie.
    */
    getCookie: function (name) {
    	
    	return   window.sessionStorage.getItem(name);
        var coname = name + "=";
        var co = document.cookie.split(';');
        for (var i = 0; i < co.length; i++) {
            var c = co[i];
            c = c.replace(/^\s+/, '');
            if (c.indexOf(coname) == 0) return c.substring(coname.length, c.length);
        }
      
        return null;
    },

    /**     Removes a cookie
         
         *  @param  name  string  Cookie's name.
    */

    removeCookie: function (name) {
        manageCookie.setCookie(name, "", -1);
        window.sessionStorage.removeItem(name);
    },

    /** Returns an object with all the cookies. */

    getAll: function () {
        var splits = document.cookie.split(";");
        var cookies = {};
        for (var i = 0; i < splits.length; i++) {
            var split = splits[i].split("=");
            cookies[split[0]] = unescape(split[1]);
        }
        return cookies;
    },

    /** Removes all the cookies */

    removeAll: function () {
        var cookies = manageCookie.getAll();
        for (var key in cookies) {
            if (obj.hasOwnProperty(key)) {
                manageCookie.removeCookie();
            }
        }
    }
};

$(function(){
	$('.logout').click(function(event){
		event.preventDefault();
        manageCookie.removeCookie('login');
        manageCookie.removeCookie('user');
		manageCookie.removeCookie('dbid');
		 window.location.href ='index.html';
	})
})

function checkeDiabetesThreshold(gl,fasting)
{
    var alert = {status:false,message:'', sms:false};
    if(fasting){
        if(gl< 5.6){
            alert.message = 'Normal';
        }

        if(gl>=5.6 &&  gl<=6.9){
            alert.message ='Prediabetes'
        }

        if(gl>=7.0){
            alert.message ='Glucose level not within range';
            alert.status = true;
        }
    }else {
         if(gl>=11.1){
            alert.message ='Glucose level not within range';
            alert.status = true;
         }
    }
    return alert ;

}

function checkeBPThreshold(systolic,diastolic, level)
{
    /*
   
This blood pressure chart reflects categories defined by the American Heart Association.
Normal - Systolic is less than 120 and Diastolic is less than 80
Prehypertension - Systolic is 120 – 139 or Diastolic is 80 – 89 
High Blood Pressure (Hypertension) Stage 1 - Systolic is 140 – 159 or Diastolic is 90 – 99 
High Blood Pressure (Hypertension) Stage 2 - Systolic is 160 or higher or Diastolic is 100 or higher 
Hypertensive Crisis (Emergency care needed) - Systolic is Higher than 180 or Diastolic is ligher than 110 
     */

    var alert = {status:false,message:'',sms:false};
    
        if(systolic<120 && diastolic<80) {
            alert.status = false;
           alert.message = 'Normal';
        }else if((systolic>= 120 && systolic<=139) && (diastolic>= 80 && diastolic<=89)) {
            alert.status = false;
            alert.message = 'Prehypertension, please regual your blood pressure';
        }else if((systolic>= 140 && systolic<=159) && (diastolic>= 90 && diastolic<=99)) {
            alert.status = true;
             alert.sms = true;
            alert.message = 'Hypertension stage 1. Emergency care needed';
        }else  if((systolic>= 160) && (diastolic>= 100)) {
            alert.status = true;
            alert.sms = true;
            alert.message = 'Hypertension stage 2. Emergency care needed';
        }else if((systolic> 180) && (diastolic> 110)) {
            alert.status = true;
             alert.sms = true;
            alert.message = 'Hypertensive Crisis. Emergency care needed';
        }
    
    return alert;

}
            var id = 1, dialog;

            callback = function () {
                cordova.plugins.notification.local.getIds(function (ids) {
                    showToast('IDs: ' + ids.join(' ,'));
                });
            };

            showToast = function (text) {
                setTimeout(function () {
                    if (device.platform != 'windows') {
                        window.plugins.toast.showShortBottom(text);
                    } else {
                        showDialog(text);
                    }
                }, 100);
            };

            showDialog = function (text) {
                if (dialog) {
                    dialog.content = text;
                    return;
                }

                dialog = new Windows.UI.Popups.MessageDialog(text);

                dialog.showAsync().done(function () {
                    dialog = null;
                });
            };
function setNotification(text,time)
{
    cordova.plugins.notification.local.schedule({
    id: 1,
    title: 'Reminder',
    text: text,
    firstAt: new Date(time),
    every: "day"
    });
    var localStorage = manageCookie.getCookie('reminder');
    if(localStorage){
          var  arrayStorage = localStorage;
    }else {
        var arrayStorage = new Array();

    }
     arrayStorage.push({'reminder':text,'time':time});
}
function rc4(key, str) {
    var s = [], j = 0, x, res = '';
    for (var i = 0; i < 256; i++) {
        s[i] = i;
    }
    for (i = 0; i < 256; i++) {
        j = (j + s[i] + key.charCodeAt(i % key.length)) % 256;
        x = s[i];
        s[i] = s[j];
        s[j] = x;
    }
    i = 0;
    j = 0;
    for (var y = 0; y < str.length; y++) {
        i = (i + 1) % 256;
        j = (j + s[i]) % 256;
        x = s[i];
        s[i] = s[j];
        s[j] = x;
        res += String.fromCharCode(str.charCodeAt(y) ^ s[(s[i] + s[j]) % 256]);
    }
    return res.replace('¬',',');
}

