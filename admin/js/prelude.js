// Accessibility links thks to vie-publique.fr
aFocus = function() {
	if(document.getElementById("prelude")) {
		var aElts = document.getElementById("prelude").getElementsByTagName("A");
		for (var i=0; i<aElts.length; i++) {
			aElts[i].className="hidden";
			aElts[i].onfocus=function() {
				$('#prelude a').removeClass('hidden');
				$('#main-menu').css('padding-top', '4.5em');
			}
		}
	}
}
// events onload
function addLoadEvent(func) {
	if (window.addEventListener)
		window.addEventListener("load", func, false);
	else if (window.attachEvent)
		window.attachEvent("onload", func);
}
addLoadEvent(aFocus);

// init
$(function() {
	$('#main-menu').css('padding-top', '.5em');
});