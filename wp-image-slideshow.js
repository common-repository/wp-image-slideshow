/**
 *     wp image slideshow 
 *     Copyright (C) 2011 - 2023 www.gopiplus.com
 *     http://www.gopiplus.com/work/2011/05/06/wordpress-plugin-wp-image-slideshow/
 * 
 *     This program is free software: you can redistribute it and/or modify
 *     it under the terms of the GNU General Public License as published by
 *     the Free Software Foundation, either version 3 of the License, or
 *     (at your option) any later version.
 * 
 *     This program is distributed in the hope that it will be useful,
 *     but WITHOUT ANY WARRANTY; without even the implied warranty of
 *     MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *     GNU General Public License for more details.
 * 
 *     You should have received a copy of the GNU General Public License
 *     along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
 
var _wpiscount=0

function wpis(wpis_imgarray, w, h, delay){
	this.id="_dropslide"+(++_wpiscount) //Generate unique ID for this slideshow instance (automated)
	this.createcontainer(parseInt(w), parseInt(h))
	this.delay=delay
	this.wpis_imgarray=wpis_imgarray
	var preloadimages=[]
	for (var i=0; i<wpis_imgarray.length; i++){
		preloadimages[i]=new Image()
		preloadimages[i].src=wpis_imgarray[i][0]
	}
	this.animatestartpos=parseInt(h)*(-1) //Starting "top" position of an image before it drops in
	this.wpis_slidedegree=10 //Slide degree (> is faster)
	this.wpis_slidedelay=30 //Delay between slide animation (< is faster)
	this.wpis_activecanvasindex=0 //Current "active" canvas- Two canvas DIVs in total
	this.wpis_curimageindex=0
	this.zindex=100
	this.isMouseover=0
	this.init()
}


wpis.prototype.createcontainer=function(w, h){
 document.write('<div id="'+this.id+'" style="position:relative; width:'+w+'px; height:'+h+'px; overflow:hidden">')
	document.write('<div style="position:absolute; width:'+w+'px; height:'+h+'px; top:0;"></div>')
	document.write('<div style="position:absolute; width:'+w+'px; height:'+h+'px; top:-'+h+'px;"></div>')
	document.write('</div>')
	this.wpis_slideshowref=document.getElementById(this.id)
	this.wpis_canvases=[]
	this.wpis_canvases[0]=this.wpis_slideshowref.childNodes[0]
	this.wpis_canvases[1]=this.wpis_slideshowref.childNodes[1]
}

wpis.prototype.populatecanvas=function(wpis_canvas, imageindex){
	var imageHTML='<img src="'+this.wpis_imgarray[imageindex][0]+'" style="border: 0" />'
	if (this.wpis_imgarray[imageindex][1]!="")
		imageHTML='<a href="'+this.wpis_imgarray[imageindex][1]+'" target="'+this.wpis_imgarray[imageindex][2]+'">'+imageHTML+'</a>'
	wpis_canvas.innerHTML=imageHTML
}


wpis.prototype.animateslide=function(){
	if (this.curimagepos<0){ //if image hasn't fully dropped in yet
		this.curimagepos=this.curimagepos+this.wpis_slidedegree
		this.activecanvas.style.top=this.curimagepos+"px"
	}
	else{
		clearInterval(this.animatetimer)
		this.activecanvas.style.top=0
		this.setupnextslide()
		var slideshow=this
		setTimeout(function(){slideshow.rotateslide()}, this.delay)
	}
}

wpis.prototype.setupnextslide=function(){
	this.wpis_activecanvasindex=(this.wpis_activecanvasindex==0)? 1 : 0
	this.activecanvas=this.wpis_canvases[this.wpis_activecanvasindex]
	this.activecanvas.style.top=this.animatestartpos+"px"
	this.curimagepos=this.animatestartpos
	this.activecanvas.style.zIndex=(++this.zindex)
	this.wpis_curimageindex=(this.wpis_curimageindex<this.wpis_imgarray.length-1)? this.wpis_curimageindex+1 : 0
	this.populatecanvas(this.activecanvas, this.wpis_curimageindex)
}

wpis.prototype.rotateslide=function(){
	var slideshow=this
	if (this.isMouseover)
		setTimeout(function(){slideshow.rotateslide()}, 50)
	else
		this.animatetimer=setInterval(function(){slideshow.animateslide()}, this.wpis_slidedelay)
}

wpis.prototype.init=function(){
	var slideshow=this
	this.populatecanvas(this.wpis_canvases[this.wpis_activecanvasindex], 0)
	this.setupnextslide()
	this.wpis_slideshowref.onmouseover=function(){slideshow.isMouseover=1}
	this.wpis_slideshowref.onmouseout=function(){slideshow.isMouseover=0}
	setTimeout(function(){slideshow.rotateslide()}, this.delay)
}