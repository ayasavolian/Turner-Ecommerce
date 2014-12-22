//document.getElementById("cartbutton").addEventListener("click", setvisibility);
/*
passSession(passvar){
	var email = passvar;
	console.log(email);
}
*/

function abandonemail()
{
	Munchkin.init("314-QVX-610");
    Munchkin.munchkinFunction('clickLink', {
    href: '/cartabondon'})
}

function blackcardclicked()
{
    document.getElementsByName('creditcard')[0].value='5729202917261928';
    document.getElementsByName('creditnum')[0].value='729';
    document.getElementsByName('expirationcard')[0].value='02/19/2019';
}

function emailtest(email)
{
	var emailused = email;
}

Window.onBeforeClose= function(){
	Munchkin.init("314-QVX-610");
    Munchkin.munchkinFunction('visitWebPage', {
    key: emailused,
    href: '/cartabondon'}
);
};

Window.onbeforeclose = function(){
	alert("I am an alert box!");
};

function setvisibility()
{
	var cart = document.getElementById('cart');
	if(cart.style.visibility == 'hidden')
		cart.style.visibility='visible';
	else
		cart.style.visibility = 'hidden';
}

function lightboxoff()
{
	var lightbox = document.getElementById('lightbox');
	var background = document.getElementById('lightboxbackground');
	lightbox.style.visibility = 'hidden';
	background.style.visibility = 'hidden';
}

function nyanlightboxon()
{
	var lightbox = document.getElementById('nyanlightbox');
	var background = document.getElementById('lightboxbackground');
	lightbox.style.visibility = 'visible';
	background.style.visibility = 'visible';
	document.cookie="autoplay=1";
}

function nyanlightboxoff()
{
	var lightbox = document.getElementById('nyanlightbox');
	var background = document.getElementById('lightboxbackground');
	lightbox.style.visibility = 'hidden';
	background.style.visibility = 'hidden';
	document.cookie="autoplay=0";
}
/*
var cart = document.getElementById('cartbutton');

addEventListener("click", myFunction);

function myFunction() {

    alert ("Hello World!");
}
*/

var COOKIE = {
	
	setCookie: function(name, value, expire, path, domain) {
		'use strict';
		
		var str = encodeURIComponent(name) + '=' + encodeURIComponent(value);
		
		str += ';expires=' + expire.toGMTString();
		
		document.cookie = str;
		
	}, //end of the setCookie() function
	
	getCookie: function(name) {
		var len = name.length;
		
		var cookies = document.cookie.split(';');
		
		for (var i = 0, count = cookies.length; i < count; i++) {
			var value = (cookies[i].slice(0,1) == ' ') ? cookies[i].slice(1) : cookies[i];
			
			value = decodeURIComponent(value);
			
			if (value.slice(0, len) == name) {
				return value.split('=')[1];
			} //end of IF
			
		} //end of FOR loop
		
		return false;
	}, //end of getCookie() function
	
	deleteCookie: function(name, path, domain) {
		'use strict';
		
		//Chapter 9 - Pursue #15 - add path and domain
		document.cookie = encodeURIComponent(name) + '=;expires=Thu, 01-Jan-1970 00:00:00 GMT' + ';path=' + path + ';domain' + domain;
		
	} //end of deleteCookie() function
	
}; //end of COOKIE declaration
