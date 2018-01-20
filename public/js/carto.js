// Extract the width and height that was computed by CSS.
var chartDiv = document.getElementById("vis");
var width = chartDiv.clientWidth;
var height = chartDiv.clientHeight;
var maxEmotions = 2;
var selectedEmotion = [];
var loc = window.location.pathname;
var dir = loc.substring(0, loc.lastIndexOf('/'));
// load the external svg from a file
// d3.xml("../svg/carto.svg", "image/svg+xml", function(xml) {
// 	var importedNode = document.importNode(xml.documentElement, true);
// 	d3.select("div#vis").attr("id","svg-container")
// 	  .each(function() {
// 	    this.appendChild(importedNode);
// 	  })
// 	  // inside of our d3.xml callback, call another function
// 	  // that styles individual paths inside of our imported svg
// 	  // styleImportedSVG()
// });

// function styleImportedSVG () {
//   d3.select('#content1')
//     .on('click', function() {
//     	var mouse = d3.mouse(this);
// 	    var elem = document.elementFromPoint(mouse[0], mouse[1]);
// 	    console.log(elem.tagName);
//       console.log('mouseover');
//       console.log('this', this);
//       alert(d3.select('#content1').style('stroke-width'));
//       if (d3.select('#content1').style('stroke-width') == "0px")
//   	{
// 		alert(d3.select('#content1').style);
// 		// carre.style['stroke-width'] = 1;
// 		// carre.style.setProperty('stroke-width','1');
// 		d3.select('#content1').style({'stroke-width': 1});
// 		alert("zebbi");
// 	}
// 	else 
// 	{
// 		alert(d3.select('#content1').style);
// 		// carre.style['stroke-width'] = 0;
// 		// carre.style.setProperty('stroke-width','0');
// 		d3.select('#content1').style({'stroke-width':0});
// 		alert("zebbi2");
// 	}

//       // d3.select('#content1').style({'stroke-width': 1})
//     })
//     // .on('mouseout', function() {
//     //   console.log('mouseout');
//     //   d3.selectAll('path')
//     //     .style({
//     //       'fill-opacity':   1,
//     //       'stroke-opacity': 1
//     //     })
//     // })
// }

function modif (id){
	console.log('clicked !!');
	var carre = document.getElementById(id);
	var d3selection = d3.select(id);
	console.log(carre.style['stroke-width']);

	var hasHeSelectedThisEmotionBefore = false;
	for(var i = 0; i < selectedEmotion.length; i++)
	{
		if(selectedEmotion[i] == id)
			hasHeSelectedThisEmotionBefore = true;
		//si émotion de même catégorie
		if(selectedEmotion[i].substr(0,selectedEmotion[i].length-1) == id.substr(0,id.length-1) )
		{
			d3.select('#'+selectedEmotion[i]).style({'stroke-width':0});
			selectedEmotion.splice(i,1);
		}
	}

	//si on clique sur une case non sélectionnée et qu'on peut encore selectionner des émotions
	if (!hasHeSelectedThisEmotionBefore && selectedEmotion.length < maxEmotions)
	{
		selectedEmotion.push(id);
		d3.select('#'+id).style({'stroke-width':1});
	}

	else if (hasHeSelectedThisEmotionBefore)
	{
		d3.select('#'+id).style({'stroke-width':0});
		for(var i = 0; i < selectedEmotion.length; i++){
			if(selectedEmotion[i] == id)
				selectedEmotion.splice(i,1);
		}
	}

	console.log(carre.style['stroke-width']);
}

