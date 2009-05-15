//Create user extensions namespace (Ext.ux)
Ext.namespace('Ext.ux');

/**
 * Ext.ux.ColorSlider Extension Class
 * 
 * @author JoH asenau
 * @version 1.0.0
 *
 * Homepage: http://www.eqony.com
 * 
 */
 
Ext.ux.SliderTip = Ext.extend(Ext.Tip, {
    minWidth:10,
    offsets:[0,-10],
    init: function(slider){
	slider.on('dragstart', this.onSlide, this);
	slider.on('drag', this.onSlide, this);
	slider.on('dragend', this.hide, this);
	slider.on('destroy', this.destroy, this);
    },
    onSlide: function(slider){
	this.show();
	this.body.update(this.getText(slider));
	this.doAutoWidth();
	this.el.alignTo(slider.thumb, 'b-t?', this.offsets);
	updateColors(slider.id.substr(0,3),slider.getValue());
    },
    getText: function(slider){
	if(slider.hexOutput) {
	    return makeHexValue(slider.getValue());
	} else {
	    return slider.getValue().toString() + (slider.unit ? slider.unit : '');
	}
    }    
});
 
Ext.ux.ColorSlider = function( element, config ) {
    Ext.ux.ColorSlider.superclass.constructor.call( this, element, config );
    this.init( element, config );
}
 
Ext.extend(Ext.ux.ColorSlider, Ext.Slider, {
    init: function ( element, config ) {
	if(this.vertical) {
	    this.height = this.size;
	} else {
	    this.width = this.size;
	}
	this.minValue = 0;
	if(this.unit=='%'){
	    this.maxValue = 100;
	} else if(this.maxValue!=360){
	    this.maxValue = 255;
	    this.hexOutput = true;
	}
    },
});

Ext.ux.ColorPanel = function( element, config ) {
    Ext.ux.ColorPanel.superclass.constructor.call( this, element, config );
    this.init( element, config );
}
 
Ext.extend(Ext.ux.ColorPanel, Ext.Panel, {
    init: function ( element, config ) {
    },
    title: 'Color Picker',
    frame:true,
    hidden:true,
    width:210,
    height:172,
    calculateWebSafe:function(baseValue){
	alert(baseValue);
    }
});

Ext.reg('colorSlider', Ext.ux.ColorSlider);

function updateColors(sliderName,sliderValue){
    colorhex = Ext.get('colorhex').dom;
    colorfield = Ext.get('colorfield').dom.style;
    webhex = Ext.get('webhex').dom;    
    webcolorfield = Ext.get('webcolorfield').dom.style;
    switch(sliderName) {
	case 'red':
			window.RGB[0] = sliderValue;
			colorhex.value = calculateHexFromRGB();
			break;
	case 'gre':	
			window.RGB[1] = sliderValue;
			colorhex.value = calculateHexFromRGB();
			break;
	case 'blu':
			window.RGB[2] = sliderValue;
			colorhex.value = calculateHexFromRGB();
			break;
	case 'hue':
			window.HSL[0] = sliderValue;			
			colorhex.value = calculateHexFromHSL();
			break;
	case 'sat':
			window.HSL[1] = sliderValue;			
			colorhex.value = calculateHexFromHSL();
			break;
	case 'lum':
			window.HSL[2] = sliderValue;			
			colorhex.value = calculateHexFromHSL();
			break;
    }
    webhex.value = '#' + calculateWebSafeHexColor(colorhex.value.substr(1,6));
    colorfield.backgroundColor = colorhex.value;
    window.newHexColor = colorhex.value.substr(1,6);
    webcolorfield.backgroundColor = webhex.value;
    window.webSafeColor = webhex.value.substr(1,6);
    switch(sliderName) {
	case 'red': 		    
	case 'gre': 		    
	case 'blu': 		    
		    window.HSL = calculateToHSL(window.newHexColor);
		    Ext.getCmp('hueslider').setValue(window.HSL[0],false);
		    Ext.getCmp('satslider').setValue(window.HSL[1],false);
		    Ext.getCmp('lumslider').setValue(window.HSL[2],false);
		    break;	
	case 'hue':
	case 'sat':
	case 'lum':
		    window.RGB = calculateRGB(window.newHexColor);
		    Ext.getCmp('redslider').setValue(window.RGB[0],false);
		    Ext.getCmp('greenslider').setValue(window.RGB[1],false);
		    Ext.getCmp('blueslider').setValue(window.RGB[2],false);
		    break;	
    }
}

function calculateHexFromHSL() {
    h = window.HSL[0];
    s = window.HSL[1];
    l = window.HSL[2];
    if(h==360) {
	h=0
    };
    hCalc = h/60;
    if(hCalc>=5) {
	hCalc = hCalc-6;
    }
    s = s/100;
    if(l<=50) {
	cD = s*l*2;
    } else {
	cD = s*(200-2*l);
    }
    if(h==0 && s==0) {
	cD = 0;
    }
    var cMin = (2*l-cD)/2;
    var cMax = cD + cMin;
    var cMed;
    
    if(h<=60 || h>=300) {
	dGB = hCalc*cD;
	cMed = cMin + Math.abs(dGB);
	r = cMax;
	if(dGB<0) {
	    b = cMed;
	    g = cMin;
	} else {
	    g = cMed;
	    b = cMin;
	}
    } else if(h>=180 && h<=300) {
	dGB = hCalc*cD-4*cD;
	cMed = cMin + Math.abs(dGB);
	b = cMax;
	if(dGB<0) {
	    g = cMed;
	    r = cMin;
	} else {
	    r = cMed;
	    g = cMin;
	}
    } else if(h>=60 && h<=180) {
	dGB = hCalc*cD-2*cD;
	cMed = cMin + Math.abs(dGB);
	g = cMax;
	if(dGB<0) {
	    r = cMed;
	    b = cMin;
	} else {
	    b = cMed;
	    r = cMin;
	}
    }
    
    window.RGB[0] = Math.round(r*2.55);
    window.RGB[1] = Math.round(g*2.55);
    window.RGB[2] = Math.round(b*2.55);
    
    return calculateHexFromRGB();    
}

function calculateHexFromRGB() {
    return '#' + makeHexValue(window.RGB[0]) + makeHexValue(window.RGB[1]) + makeHexValue(window.RGB[2]); 
}

function calculateToHSL(value){
    r = makeDezValue(value.substr(0,2))/255;
    g = makeDezValue(value.substr(2,2))/255;
    b = makeDezValue(value.substr(4,2))/255;
    var min, max, delta, h, s, l;
    min = Math.min(Math.min(r, g), b);
    max = Math.max(Math.max(r, g), b);
    delta = max-min;
    switch(max){
	case min:	h = 0;
			break;
	case r:		h = 60*(g-b)/delta;
			if(g<b){
			    h+=360;
			}
			break;
	case g:   	h = (60*(b-r)/delta)+120;
			break;
	case b:   	h = (60*(r-g)/delta)+240;
			break;
    }
    s = Math.round((max===0) ? 0 : (1-(min/max))*100);
    l = Math.round((max+min)/2*100);
    return [Math.round(h), s, l];    
}

function calculateRGB(value){
    r = makeDezValue(value.substr(0,2));
    g = makeDezValue(value.substr(2,2));
    b = makeDezValue(value.substr(4,2));
    return [r, g, b];
}

function calculateWebSafeHexColor(value) {
    r = makeWebSafeHexValue(value.substr(0,2));
    g = makeWebSafeHexValue(value.substr(2,2));
    b = makeWebSafeHexValue(value.substr(4,2));
    return r + g + b;
}

function makeWebSafeHexValue(value) {
    calculatedValue = Math.round(makeDezValue(value)/51)*51;
    hexValue = makeHexValue(calculatedValue);
    return hexValue;
}

function makeDezValue(value) {
    return parseInt(value,16);
}

function makeHexValue(value) {
    hexValue = value.toString(16).toUpperCase();
    if(value < 16) {
	return '0'+hexValue;	
    } else {
	return hexValue;
    }   
}


Ext.onReady(function(){
    var colorBoxCounter = 0;
    Ext.get(document.body).select(".colorbox").each(function(element) {
	colorBoxCounter++;
	element.dom.firstChild.id = 'colorbox' + colorBoxCounter;
	Ext.EventManager.on(element.dom.firstChild, 'click', function(){
	    if(window.colorPanel && (Ext.get(this.id).dom.firstChild == Ext.get(this.id).dom.lastChild)) {
		window.colorPanel.destroy();
	    }
	    if((Ext.get(this.id).dom.firstChild == Ext.get(this.id).dom.lastChild)) {		
		window.newHexColor = this.nextSibling.value.substr(1,6);
		window.webSafeColor = calculateWebSafeHexColor(window.newHexColor);
		window.RGB = calculateRGB(window.newHexColor);
		window.HSL = calculateToHSL(window.newHexColor);
		window.colorPanel = new Ext.ux.ColorPanel({
		    renderTo:this.id,
		    hideMode:'offsets',
		    autoDestroy:true,
		    bbar:[
    			{
    			    text: 'Standard',
			    scope: this,
			    handler: function(){
				this.style.backgroundColor = '#'+window.newHexColor;
				this.nextSibling.value = '#'+window.newHexColor;
		    		window.colorPanel.destroy();
			    },
			    cls:'savebutton'
			},
    			{
    			    text: 'Websafe',
			    scope: this,
			    handler: function(){
				this.style.backgroundColor = '#'+window.webSafeColor;
				this.nextSibling.value = '#'+window.webSafeColor;
		    		window.colorPanel.destroy();
			    },
			    cls:'savebutton'
			}
		    ],
		    tools:[
    			{
			    id:'close',
			    handler: function(){
				window.colorPanel.destroy();
			    }
			},
		    ],		
		    items:[
			{
			    xtype:'colorSlider',
			    vertical:'true',
			    size:100,
			    minValue:0,
			    maxValue:360,
			    id:'hueslider',
			    cls:'hueslider',
			    value:window.HSL[0],
			    plugins: new Ext.ux.SliderTip()
			},
			{
			    xtype:'container',
			    autoEl:{
				tag:'div'
			    },
			    cls:'currentcolor',
			    id:'currentcolor',
			    style:'background-color:#' + window.newHexColor + '!important;',
			    items:{
				xtype:'container',
				autoEl:{
				    tag:'input',
				    value:'#'+window.newHexColor,
				    readOnly:'readOnly'
				},
				cls:'hexvalue',
				id:'currenthex'
			    }
			},
			{
			    xtype:'container',
			    autoEl:{
				tag:'div'
			    },
			    cls:'colorfield',
			    id:'colorfield',
			    style:'background-color:#' + window.newHexColor + '!important;',
			    items:{
				xtype:'container',
				autoEl:{
				    tag:'input',
				    value:'#'+window.newHexColor,
				    readOnly:'readOnly'
				},
				cls:'hexvalue',
				id:'colorhex'
			    }
			},
			{
			    xtype:'container',
			    autoEl:{
				tag:'div'
			    },
			    cls:'webcolorfield',
			    id:'webcolorfield',
			    style:'background-color:#' + window.webSafeColor + '!important;',
			    items:{
				xtype:'container',
				autoEl:{
				    tag:'input',
				    value:'#'+window.webSafeColor,
				    readOnly:'readOnly'
				},
				cls:'hexvalue',
				id:'webhex'
			    }
			},
			{
			    xtype:'colorSlider',
			    size:100,
			    unit:'%',
			    id:'lumslider',
			    cls:'lumslider',
			    value:window.HSL[2],
			    plugins: new Ext.ux.SliderTip()
			},
			{
			    xtype:'colorSlider',
			    vertical:'true',
			    size:100,
			    unit:'%',
			    id:'satslider',
			    cls:'satslider',
			    value:window.HSL[1],
			    plugins: new Ext.ux.SliderTip()
			},
			{
			    xtype:'colorSlider',
			    vertical:'true',
			    size:100,
			    id:'redslider',
			    cls:'redslider',
			    value: window.RGB[0],
			    plugins: new Ext.ux.SliderTip()
			},
			{
			    xtype:'colorSlider',
			    vertical:'true',
			    size:100,
			    id:'greenslider',
			    cls:'greenslider',
			    value: window.RGB[0],
			    plugins: new Ext.ux.SliderTip()
			},
			{
			    xtype:'colorSlider',
			    vertical:'true',
			    size:100,
			    id:'blueslider',
			    cls:'blueslider',
			    value: window.RGB[2],
			    plugins: new Ext.ux.SliderTip()
			}
		    ],
		});
		Ext.get(this.id).fadeIn({
		    duration:1,
		});
		window.colorPanel.show();
	    }
	});
	Ext.EventManager.on(element.dom.lastChild, 'keyup', function(){
	    if(this.value.length==7 && this.value.substr(0,1)=='#' && this.value.replace(/[0123456789ABCDEFabcdef]/g,'')=='#') {
		this.value = this.value.toUpperCase();
		this.previousSibling.style.backgroundColor = this.value.toUpperCase();
	    }
	});
    });
});