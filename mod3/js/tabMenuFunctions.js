function triggerTab(triggerItem,triggeredContent) {
    allTriggers = triggerItem.parentNode.parentNode.getElementsByTagName("li");
    countTriggers = allTriggers.length;
    for(i=1;i<=countTriggers;i++) {
	if(i>triggeredContent) {
	    allTriggers[i-1].className = "redbutton";
	} else {
	    allTriggers[i-1].className = "greenbutton";	
	}
	document.getElementById("tabcontent" + i).className = "tabcontent_off";
    }
    document.getElementById("tabcontent" + triggeredContent).className = "tabcontent_on";
    triggerItem.blur();
}
