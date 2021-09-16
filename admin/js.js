function deleteaccount(){
	var answer = confirm('Are you sure you want to delete your account? This action is irreversible!');
	if(answer==true){
		window.location="account.php?action=delete&verify=1";
	}
}

function denyuser(username){
	var answer = confirm('Are you sure you want to deny the user "'+username+'"? This will completely remove them from the database.');
	if(answer==true){
		window.location="./useraccept.php?action=deny&user="+username;
	}
}
function acceptuser(username){
	var answer = confirm('Are you sure you want to accept the user "'+username+'"? This will update their status to approved and will give them access to the protected area.');
	if(answer==true){
		window.location="./useraccept.php?action=accept&user="+username;
	}
}
function deleteuser(username){
	var answer = confirm('Are you sure you want to delete the user "'+username+'"?');
	if(answer==true){
		window.location="./userdel.php?user="+username;
	}
}
function deleteinactiveuser(username){
	var answer = confirm('Are you sure you want to delete the inactive user "'+username+'"?');
	if(answer==true){
		window.location="./userdel.php?r=inactive&user="+username;
	}
}
function validateuser(username){
	var answer = confirm('Are you sure you want to validate the user "'+username+'"? This will make it as though they verified their email address.');
	if(answer==true){
		window.location="./validateuser.php?user="+username;
	}
}
  function PP_preloadImages()
  {
    if( document.images )
    {
      var preLoadArg = PP_preloadImages.arguments;

      var arrayImages = new Array();

      for( var i = 0; i < preLoadArg.length; i++ )
      {
        arrayImages[i] = new Image();
        arrayImages[i].src = preLoadArg[i];
      }
    }
  }

  function locateObject(name, d)
  {
    var i,x;
    if( !d ) d = document;
  	 x = d[name];
    for(i=0; !x && d.layers && i< d.layers.length; i++)
      x=locateObject(name, d.layers[i].document);
    return x;
  }

  function ImageSwap( Name, URL)  {
    var img;
    img = locateObject(Name);
    img.src = URL;
  }


var horizontal_offset="9px" //horizontal offset of hint box from anchor link

/////No further editting needed

var vertical_offset="0" //horizontal offset of hint box from anchor link. No need to change.
var ie=document.all
var ns6=document.getElementById&&!document.all

function getposOffset(what, offsettype){
	var totaloffset=(offsettype=="left")? what.offsetLeft : what.offsetTop;
	var parentEl=what.offsetParent;
	while (parentEl!=null){
		totaloffset=(offsettype=="left")? totaloffset+parentEl.offsetLeft : totaloffset+parentEl.offsetTop;
		parentEl=parentEl.offsetParent;
	}
	return totaloffset;
}

function iecompattest(){
	return (document.compatMode && document.compatMode!="BackCompat")? document.documentElement : document.body
}

function clearbrowseredge(obj, whichedge){
	var edgeoffset=(whichedge=="rightedge")? parseInt(horizontal_offset)*-1 : parseInt(vertical_offset)*-1
	if (whichedge=="rightedge"){
		var windowedge=ie && !window.opera? iecompattest().scrollLeft+iecompattest().clientWidth-30 : window.pageXOffset+window.innerWidth-40
		dropmenuobj.contentmeasure=dropmenuobj.offsetWidth
		if (windowedge-dropmenuobj.x < dropmenuobj.contentmeasure)
		edgeoffset=dropmenuobj.contentmeasure+obj.offsetWidth+parseInt(horizontal_offset)
	}
	else{
		var windowedge=ie && !window.opera? iecompattest().scrollTop+iecompattest().clientHeight-15 : window.pageYOffset+window.innerHeight-18
		dropmenuobj.contentmeasure=dropmenuobj.offsetHeight
		if (windowedge-dropmenuobj.y < dropmenuobj.contentmeasure)
		edgeoffset=dropmenuobj.contentmeasure-obj.offsetHeight
	}
	return edgeoffset
}

function showhint(menucontents, obj, e, tipwidth){
	if ((ie||ns6) && document.getElementById("hintbox")){
		dropmenuobj=document.getElementById("hintbox")
		dropmenuobj.innerHTML=menucontents
		dropmenuobj.style.left=dropmenuobj.style.top=-500
		if (tipwidth!=""){
			dropmenuobj.widthobj=dropmenuobj.style
			dropmenuobj.widthobj.width=tipwidth
		}
		dropmenuobj.x=getposOffset(obj, "left")
		dropmenuobj.y=getposOffset(obj, "top")
		dropmenuobj.style.left=dropmenuobj.x-clearbrowseredge(obj, "rightedge")+obj.offsetWidth+"px"
		dropmenuobj.style.top=dropmenuobj.y-clearbrowseredge(obj, "bottomedge")+"px"
		dropmenuobj.style.visibility="visible"
		obj.onmouseout=hidetip
	}
}

function hidetip(e){
	dropmenuobj.style.visibility="hidden"
	dropmenuobj.style.left="-500px"
}

function createhintbox(){
	var divblock=document.createElement("div")
	divblock.setAttribute("id", "hintbox")
	document.body.appendChild(divblock)
}

if (window.addEventListener)
window.addEventListener("load", createhintbox, false)
else if (window.attachEvent)
window.attachEvent("onload", createhintbox)
else if (document.getElementById)
window.onload=createhintbox