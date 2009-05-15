function dimmer(el,lumen) {
    el.className = lumen;
}

function switchVisibility(el) {
    c 	= el.firstChild;
    sel = el.parentNode.getElementsByTagName("ul")[0];

    if(c.src.search(/minusonly.gif/)!=-1) {
        c.src 	= c.src.replace(/minusonly/,"plusonly");
        c.title = "Unfold this part of the page tree";
        sel.style.display = "none";
    } else {
        c.src 	= c.src.replace(/plusonly/,"minusonly");
        c.title = "Fold this part of the page tree";
        sel.style.display = "block";						
    }

    el.blur();
}
    
function removeItem(el) {
    pel 	= el.parentNode;
    elId 	= pel.id;
    gpel 	= pel.parentNode;
    gpelId 	= gpel.parentNode.id;

    gpel.removeChild(pel);

    if(gpel.childNodes.length==0) {
	document.getElementById(gpelId).getElementsByTagName("img")[0].src="icons/new_level.gif";
	document.getElementById(gpelId).getElementsByTagName("img")[0].title="Create a subpage on next level";
	document.getElementById(gpelId).getElementsByTagName("a")[0].onclick=new Function("","addSubItem(this);");
	document.getElementById(gpelId).removeChild(gpel);	
    } else {
	document.getElementById(gpel.firstChild.id + "up").style.display = "none";
	if(gpel.childNodes.length>1) {
	    document.getElementById(gpel.firstChild.id + "down").style.display = "inline";
	    document.getElementById(gpel.lastChild.id + "up").style.display = "inline";
	}
	document.getElementById(gpel.lastChild.id + "down").style.display = "none";
    }
}

function cloneItem(el) {
    window.clearTimeout(window.t3dkskTimer);
    top.t3dkskel 	= el;
    top.t3dkskclone 	= true;
    addItem2();
}
					    
function addItem(el) {
    top.t3dkskel 	= el;
    window.t3dkskTimer 	= window.setTimeout('addItem2()',500);
}

function addItem2() {
    el 		= top.t3dkskel;
    pel 	= el.parentNode;
    gpel 	= pel.parentNode;
    s 		= new Date();
    nId 	= "t3dksk" + s.getTime();
    nel 	= pel.cloneNode(true);
    nel.id 	= nId;

    document.getElementById(pel.id + "up").style.display = "inline";
    document.getElementById(pel.id + "down").style.display = "inline";

    nel.lastChild.id = nel.lastChild.id.replace(/t3dksk[0-9]*/,nId);
    nel.lastChild.previousSibling.id = nel.lastChild.previousSibling.id.replace(/t3dksk[0-9]*/,nId);
    nel.lastChild.previousSibling.previousSibling.id = nel.lastChild.previousSibling.previousSibling.id.replace(/t3dksk[0-9]*/,nId);

    gpel.insertBefore(nel,pel.nextSibling);    

    if(top.t3dkskclone==false) {
	if(pel.nextSibling.getElementsByTagName("ul").length) {
	    pel.nextSibling.removeChild(pel.nextSibling.getElementsByTagName("ul")[0]);
	}
	pimg 		= pel.nextSibling.getElementsByTagName("img")[0];
	pimg.src 	= "icons/new_level.gif";
	pimg.title 	= "Create a subpage on next level";						
	pel.nextSibling.getElementsByTagName("a")[0].onclick = new Function("", "addSubItem(this);");
    }
    
    for(i=0;i<gpel.childNodes.length;i++) {
	gpel.childNodes[i].getElementsByTagName("input")[0].name = gpel.childNodes[i].getElementsByTagName("input")[0].name.replace(/[0-9]*\]\[title/,(i+1) + "\]\[title");
    }
    
    if(top.t3dkskclone ==true) {
	for(i=0;i<pel.nextSibling.getElementsByTagName("li").length;i++) {	
	    cn = pel.nextSibling.getElementsByTagName("li")[i].parentNode.parentNode.getElementsByTagName("input")[0].name;
	    cnr = cn.substr(0,cn.search(/\[title\]/));
	    en = pel.nextSibling.getElementsByTagName("li")[i].getElementsByTagName("input")[0].name;
	    ens = en.substr(en.search(/\[[0-9]*\]\[title\]/),en.length);
    	    pel.nextSibling.getElementsByTagName("li")[i].getElementsByTagName("input")[0].name = cnr + ens;
    	    pel.nextSibling.getElementsByTagName("li")[i].id = pel.nextSibling.getElementsByTagName("li")[i].id.replace(/t3dksk[0-9]*/,nId+i);
    	    pel.nextSibling.getElementsByTagName("li")[i].lastChild.id = pel.nextSibling.getElementsByTagName("li")[i].lastChild.id.replace(/t3dksk[0-9]*/,nId+i);
    	    pel.nextSibling.getElementsByTagName("li")[i].lastChild.previousSibling.id = pel.nextSibling.getElementsByTagName("li")[i].lastChild.previousSibling.id.replace(/t3dksk[0-9]*/,nId+i);
    	    pel.nextSibling.getElementsByTagName("li")[i].lastChild.previousSibling.previousSibling.id = pel.nextSibling.getElementsByTagName("li")[i].lastChild.previousSibling.previousSibling.id.replace(/t3dksk[0-9]*/,nId+i);
	}
    }
    
    document.getElementById(gpel.firstChild.id + "up").style.display = "none";
    document.getElementById(gpel.firstChild.id + "down").style.display = "inline";
    document.getElementById(gpel.lastChild.id + "up").style.display = "inline";
    document.getElementById(gpel.lastChild.id + "down").style.display = "none";

    top.t3dkskclone = false;
    top.t3dkskel = false;

    el.blur();
}
					    
function addSubItem(el,clone) {
    pel 	= el.parentNode;
    gpel 	= pel.parentNode;
    s 		= new Date();
    nId 	= "t3dksk" + s.getTime();
    nel 	= pel.cloneNode(true);
    nc 		= nel.lastChild;

    nel.getElementsByTagName("input")[0].name = nel.getElementsByTagName("input")[0].name.replace(/\]\[title/,"\]\[1\]\[title");
    nel.getElementsByTagName("input")[0].value += ".1";
    nel.getElementsByTagName("a")[0].onclick = new Function("", "addSubItem(this);");

    if(nc.id.search(/down/)!=-1 || nc.id.search(/up/)!=-1) {
        nc.id = nc.id.replace(/t3dksk[0-9]*/,nId);
        nc.style.display = "none";
    }
    if(nc.previousSibling.id.search(/up/)!=-1) {
	nc.previousSibling.id = nc.previousSibling.id.replace(/t3dksk[0-9]*/,nId);
	nc.previousSibling.style.display = "none";
    }

    pel.appendChild(gpel.cloneNode(true));

    cul = pel.getElementsByTagName("ul")[0];
    while(Number(cul.firstChild)!=0) {
        cul.removeChild(cul.firstChild);
    }
    cul.appendChild(nel);
    cul.firstChild.id = nId;

    pimg 	= pel.getElementsByTagName("img")[0];
    pimg.src 	= "icons/minusonly.gif";
    pimg.title 	= "Fold this part of the pagetree";						

    pel.getElementsByTagName("a")[0].onclick = new Function("", "switchVisibility(this);");

    el.blur();
}
					    
function moveItem(el,targetDirection) {
    pel 	= el.parentNode;
    elId 	= pel.id;
    gpel 	= pel.parentNode;

    document.getElementById(elId + "down").style.display = "inline";
    document.getElementById(elId + "up").style.display = "inline";

    if(targetDirection==-1) {
        paEl = pel.previousSibling.cloneNode(true);
        gpel.replaceChild(pel.cloneNode(true), pel.previousSibling);
        gpel.replaceChild(paEl, pel);						    
    } else {						
        paEl = pel.nextSibling.cloneNode(true);
        gpel.replaceChild(pel.cloneNode(true), pel.nextSibling);
        gpel.replaceChild(paEl, pel);
    }

    document.getElementById(paEl.id + "down").style.display = "inline";
    document.getElementById(paEl.id + "up").style.display = "inline";
    document.getElementById(gpel.firstChild.id + "up").style.display = "none";
    document.getElementById(gpel.lastChild.id + "down").style.display = "none";

    el.blur();	
}