/**
* JavaScript routines for Krumo
*
* @version $Id: krumo.js 22 2007-12-02 07:38:18Z Mrasnika $
* @link http://sourceforge.net/projects/krumo
*//////////////////////////////////////////////////////////////////////////////
/**
* Krumo JS Class
*/function krumo(){}krumo.reclass=function(e,t){e.className.indexOf(t)<0&&(e.className+=" "+t)};krumo.unclass=function(e,t){e.className.indexOf(t)>-1&&(e.className=e.className.replace(t,""))};krumo.toggle=function(e){var t=e.parentNode.getElementsByTagName("ul");for(var n=0;n<t.length;n++)t[n].parentNode.parentNode==e.parentNode&&(t[n].parentNode.style.display=t[n].parentNode.style.display=="none"?"block":"none");t[0].parentNode.style.display=="block"?krumo.reclass(e,"krumo-opened"):krumo.unclass(e,"krumo-opened")};krumo.over=function(e){krumo.reclass(e,"krumo-hover")};krumo.out=function(e){krumo.unclass(e,"krumo-hover")};