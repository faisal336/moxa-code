/* Intellifuel MS2 javascript library */

function getValue(n) {
	return document.getElementById(n).value;
}
function setValue(n,v) {
	document.getElementById(n).value = v;
}
function getContent(n) {
	return document.getElementById(n).textContent;
}
function setContent(n,v) {
	document.getElementById(n).textContent = v;
}
function addToTextArea(n,v) {
	var ta = getContent(n);
	v = v.replace(new RegExp("\\n","g"),"\r\n");
	ta = ta + "\r\n" + v;
	setContent(n,ta);
}
// scroll a text area to the bottom
function scrollToBottom(n) {
	var ta = document.getElementById(n);
	ta.scrollTop = 99999;
}