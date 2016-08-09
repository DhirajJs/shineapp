$('.message a').click(function(){
   $('form').animate({height: "toggle", opacity: "toggle"}, "slow");
});

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
		 window.location.href ='/index.html';
	})
})

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