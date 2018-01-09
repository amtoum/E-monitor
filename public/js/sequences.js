var maxEmotions = 2;


var datas = [
	{
		"name":"content",
		"value":1,
		"color": "#FFFF50",
		"nameFinalEmotion":"enthousiaste",
		"colorFinalEmotion": "#FFFF00",
		"intensities" : [
		{ 
			"name" : "Intensité 1",
			"color": "#FFFF50"
		},
		{ 
			"name" : "Intensité 2",
			"color": "#FFFF40"
		},
		{ 
			"name" : "Intensité 3",
			"color": "#FFFF30"
		},
		{ 
			"name" : "Intensité 4",
			"color": "#FFFF20"
		},
		{ 
			"name" : "Intensité 5",
			"color": "#FFFF10"
		}]
	},
	{
		"name":"étonné",
		"value":1,
		"color": "#C8FF50",
		"nameFinalEmotion":"stupéfait",
		"colorFinalEmotion": "#C8FF00",
		"intensities" : [
		{ 
			"name" : "Intensité 1",
			"color": "#C8FF50"
		},
		{ 
			"name" : "Intensité 2",
			"color": "#C8FF40"
		},
		{ 
			"name" : "Intensité 3",
			"color": "#C8FF30"
		},
		{ 
			"name" : "Intensité 4",
			"color": "#C8FF20"
		},
		{ 
			"name" : "Intensité 5",
			"color": "#C8FF10"
		}]
  },
	{
		"name":"intéressé",
		"value":1,
		"color": "#FF8825",
		"nameFinalEmotion":"motivé",
		"colorFinalEmotion": "#FF8800",
		"intensities" : [
		{ 
			"name" : "Intensité 1",
			"color": "#FF8825"
		},
		{ 
			"name" : "Intensité 2",
			"color": "#FF8820"
		},
		{ 
			"name" : "Intensité 3",
			"color": "#FF8815"
		},
		{ 
			"name" : "Intensité 4",
			"color": "#FF8810"
		},
		{ 
			"name" : "Intensité 5",
			"color": "#FF8805"
		}]
	},
	{
		"name":"tranquille",
		"value":1,
		"color": "#00FF50",
		"nameFinalEmotion":"confiant",
		"colorFinalEmotion": "#00FF00",
		"intensities" : [
		{ 
			"name" : "Intensité 1",
			"color": "#00FF50"
		},
		{ 
			"name" : "Intensité 2",
			"color": "#00FF40"
		},
		{ 
			"name" : "Intensité 3",
			"color": "#00FF30"
		},
		{ 
			"name" : "Intensité 4",
			"color": "#00FF20"
		},
		{ 
			"name" : "Intensité 5",
			"color": "#00FF10"
		}]
	},
  {
		"name":"triste",
		"value":1,
		"color": "#4CF0FF",
		"nameFinalEmotion":"déprimé",
		"colorFinalEmotion": "#4CF0AF",
		"intensities" : [
		{ 
			"name" : "Intensité 1",
			"color": "#4CF0FF"
		},
		{ 
			"name" : "Intensité 2",
			"color": "#4CF0EF"
		},
		{ 
			"name" : "Intensité 3",
			"color": "#4CF0DF"
		},
		{ 
			"name" : "Intensité 4",
			"color": "#4CFCFF"
		},
		{ 
			"name" : "Intensité 5",
			"color": "#4CF0BF"
		}]
  },
  {
		"name":"ennuyé",
		"value":1,
		"color": "#0080AA",
		"nameFinalEmotion":"angoissé",
		"colorFinalEmotion": "#008050",
		"intensities" : [
		{ 
			"name" : "Intensité 1",
			"color": "#0080AA"
		},
		{ 
			"name" : "Intensité 2",
			"color": "#008090"
		},
		{ 
			"name" : "Intensité 3",
			"color": "#008080"
		},
		{ 
			"name" : "Intensité 4",
			"color": "#008070"
		},
		{ 
			"name" : "Intensité 5",
			"color": "#008060"
		}]
	},
	{
		"name":"inquiet",
		"value":1,
		"color": "#E502FF",
		"nameFinalEmotion":"angoissé",
		"colorFinalEmotion": "#E502AA",
		"intensities" : [
		{ 
			"name" : "Intensité 1",
			"color": "#E502EE"
		},
		{ 
			"name" : "Intensité 2",
			"color": "#E502DD"
		},
		{ 
			"name" : "Intensité 3",
			"color": "#E502CC"
		},
		{ 
			"name" : "Intensité 4",
			"color": "#E502BB"
		},
		{ 
			"name" : "Intensité 5",
			"color": "#E50FAA"
		}]
  },
  {
		"name":"agacé",
		"value":1,
		"color": "#FF0450",
		"nameFinalEmotion":"furieux",
		"colorFinalEmotion": "#FF0400",
		"intensities" : [
		{ 
			"name" : "Intensité 1",
			"color": "#FF0450"
		},
		{ 
			"name" : "Intensité 2",
			"color": "#FF0440"
		},
		{ 
			"name" : "Intensité 3",
			"color": "#FF0430"
		},
		{ 
			"name" : "Intensité 4",
			"color": "#FF0420"
		},
		{ 
			"name" : "Intensité 5",
			"color": "#FF0410"
		}]
	}
];

// Dimensions of sunburst.
var width = 750;
var height = 600;
var radius = Math.min(width, height) / 2;

// Breadcrumb dimensions: width, height, spacing, width of tip/tail.
var b = {
  w: 75, h: 30, s: 3, t: 10
};

// Mapping of step names to colors.
var colors = {
  "home": "#5687d1",
  "product": "#7b615c",
  "search": "#de783b",
  "account": "#6ab975",
  "other": "#a173d1",
  "end": "#bbbbbb"
};

var selectedEmotion = [];
// Total size of all segments; we set this later, after loading the data.
var totalSize = 0; 

var vis = d3.select("#chart").append("svg:svg")
    .attr("width", width)
    .attr("height", height)
    .append("g")
    .attr("id", "container")
    .attr("transform", "translate(" + width / 2 + "," + height / 2 + ")");

var partition = d3.partition()
    .size([2 * Math.PI, radius * radius]);

var arc = d3.arc()
    .startAngle(function(d) { return d.x0; })
    .endAngle(function(d) { return d.x1; })
    .innerRadius(function(d) { return Math.sqrt(d.y0); })
    .outerRadius(function(d) { return Math.sqrt(d.y1); });

// Use d3.text and d3.csvParseRows so that we do not need to have a header
// row, and can receive the csv as an array of arrays.

  //var csv = d3.csvParseRows(text);
  var json = buildHierarchy(datas);
  createVisualization(json);

// Main function to draw and set up the visualization, once we have the data.
function createVisualization(json) {

  // Basic setup of page elements.
  initializeBreadcrumbTrail();
  drawLegend();
  d3.select("#togglelegend").on("click", toggleLegend);

  // Bounding circle underneath the sunburst, to make it easier to detect
  // when the mouse leaves the parent g.
  vis.append("svg:circle")
      .attr("r", radius)
      .style("opacity", 0);

  // Turn the data into a d3 hierarchy and calculate the sums.
  var root = d3.hierarchy(json)
      .sum(function(d) { return d.size; })
      .sort(function(a, b) { return b.value - a.value; });
  
  // For efficiency, filter nodes to keep only those large enough to see.
  var nodes = partition(root).descendants()
      .filter(function(d) {
          return (d.x1 - d.x0 > 0.005); // 0.005 radians = 0.29 degrees
      });

	  
        var vSlices = vis.selectAll('g').data(nodes).enter().append('g');
	  
  //var path = vis.data([json]).selectAll("path");
  vSlices.append("path")
      /*.data(nodes)
      .enter().append("svg:path")*/
      .attr("display", function(d) { return d.depth ? null : "none"; })
      .attr("d", arc)
      .attr("fill-rule", "evenodd")
      .style("fill", function(d) { return d.data.color; })
      .style("opacity", 1)
      .on("mouseover", mouseover)
	  .on("click", click);
	  
        // Add text
        vSlices.append('text')
            .filter(function(d) { return d.parent; })
            .attr('transform', function(d) {
                return 'translate(' + arc.centroid(d) + ') rotate(' + computeTextRotation(d) + ')'; })
            .attr('dx', '-20')
            .attr('dy', '.5em')
			.on("mouseover", mouseoverText)
            .text(function(d) { return d.data.name })
	  .on("click", click);
	  
  // Add the mouseleave handler to the bounding circle.
  d3.select("#container").on("mouseleave", mouseleave);

  // Get total size of the tree = value of root node from partition.
  //totalSize = path.datum().value;
 };

    /**
     * Calculate the rotation for each label based on its location in the sunburst.
     * @param {Node} d - the d3 note for which we're computing text rotation
     * @return {Number} the value that should populate the transform: rotate() statement
     */
    function computeTextRotation(d) {
        var angle = (d.x0 + d.x1) / Math.PI * 90;
        // Avoid upside-down labels
        return (angle < 120 || angle > 270) ? angle : angle + 180;  // labels as rims
        //return (angle < 180) ? angle - 90 : angle + 90;  // labels as spokes
    }
	
// Fade all but the current sequence, and show it in the breadcrumb trail.
function mouseover(d) {

  
  if(d.data.intensity > 0 && d.data.intensity < 100)
  {
	d3.select(this).style("cursor", "pointer");
  }
  var sequenceArray = d.ancestors().reverse();
  
	//displayMouseOver(d);

	var hasHeSelectedThisEmotionBefore = false;
	for(var i = 0; i < selectedEmotion.length; i++)
	{
		if(selectedEmotion[i].data.emotion == d.data.emotion)
			hasHeSelectedThisEmotionBefore = true;
	}
	
	if((selectedEmotion != null && selectedEmotion.length < maxEmotions) || hasHeSelectedThisEmotionBefore)
	{ // If he has reached the max amount of emotions we don't mouse over anymore
  
  displayAriane(d);
  // Fade all the segments.
  d3.selectAll("path")
      .style("opacity", 0.3);

	  HighlightSelectedEmotions();
	  
  // Then highlight only those that are an ancestor of the current segment.
  vis.selectAll("path")
      .filter(function(node) {
                return (sequenceArray.indexOf(node) >= 0 && (d.data.intensity < 100 || sequenceArray.indexOf(node) < sequenceArray.length - 1));
              })
      .style("opacity", 1);
	}
}
function mouseoverText(d) {

  
  if(d.data.intensity > 0 && d.data.intensity < 100)
  {
	d3.select(this).style("cursor", "pointer");
  }else{
	  d3.select(this).style("cursor", "default");
  }
}
function displayMessageCenter(d)
{

  var messageCenter = d.data.emotion;
	
	if(d.data.intensity > 0)
  messageCenter += " Intensité "+d.data.intensity;
  
  d3.select("#percentage")
      .text(messageCenter);

  d3.select("#explanation")
      .style("visibility", "");
}
function displayAriane(d)
{
  var percentage = (100 * d.value / totalSize).toPrecision(3);
  var percentageString = percentage + "%";
  if (percentage < 0.1) {
    percentageString = "< 0.1%";
  }
  
  var sequenceArray = d.ancestors().reverse();
  sequenceArray.shift(); // remove root node from the array
  var arrayAriane = [ sequenceArray[0], sequenceArray[sequenceArray.length-1]]; // On ne veut pas un fil d'ariane complet, seulement le dernier élément survolé
  if(sequenceArray.length == 1 && sequenceArray[0].data.intensity == 1)
  { // On est sur la racine (intensité 1)
		arrayAriane[1].data.name = "Intensité 1";
  }else if(sequenceArray.length == 1){ // The root is not intensity 1
	  arrayAriane = [sequenceArray[0]];
  }
	updateBreadcrumbs(arrayAriane, percentageString);
}
function displayMouseOver(d)
{
  displayMessageCenter(d);

  displayAriane(d);
}
// Restore everything to full opacity when moving off the visualization.
function mouseleave(d) {
	
	d3.select(this).style("cursor", "default"); 
	  // Hide the breadcrumb trail
	  d3.select("#trail")
		  .style("visibility", "hidden");



	  d3.select("#explanation")
		  .style("visibility", "hidden");
		  
	if(selectedEmotion != null && selectedEmotion.length > 0)
	{
		  // Fade all the segments.
		  d3.selectAll("path")
			  .style("opacity", 0.3);
			
			HighlightSelectedEmotions();
		
	}else{
		
	  // Deactivate all segments during transition.
	  d3.selectAll("path").on("mouseover", null);
	  // Transition each segment to full opacity and then reactivate it.
	  d3.selectAll("path")
		  .transition()
		  .duration(100)
		  .style("opacity", 1)
		  .on("end", function() {
				  d3.select(this).on("mouseover", mouseover);
	});
	}
}
function HighlightSelectedEmotions()
{
		  for(var i = 0; i < selectedEmotion.length; i++)
		  {
			var sequenceArray = selectedEmotion[i].ancestors().reverse();

		  // Then highlight only those that are an ancestor of the current segment.
		  vis.selectAll("path")
		  .filter(function(node) {
					return (sequenceArray.indexOf(node) >= 0);
				  })
		  .style("opacity", 1);
		  
			//displayAriane(selectedEmotion[i]);
		  }
}
function initializeBreadcrumbTrail() {
  // Add the svg area.
  var trail = d3.select("#sequence").append("svg:svg")
      .attr("width", width)
      .attr("height", 50)
      .attr("id", "trail");
  // Add the label at the end, for the percentage.
  trail.append("svg:text")
    .attr("id", "endlabel")
    .style("fill", "#000");
}

// Generate a string that describes the points of a breadcrumb polygon.
function breadcrumbPoints(d, i) {
  var points = [];
  points.push("0,0");
  points.push(b.w + ",0");
  points.push(b.w + b.t + "," + (b.h / 2));
  points.push(b.w + "," + b.h);
  points.push("0," + b.h);
  if (i > 0) { // Leftmost breadcrumb; don't include 6th vertex.
    points.push(b.t + "," + (b.h / 2));
  }
  return points.join(" ");
}

// Update the breadcrumb trail to show the current sequence and percentage.
function updateBreadcrumbs(nodeArray, percentageString) {

  // Data join; key function combines name and depth (= position in sequence).
  var trail = d3.select("#trail")
      .selectAll("g")
      .data(nodeArray, function(d) { return d.data.name + d.depth; });

  // Remove exiting nodes.
  trail.exit().remove();

  // Add breadcrumb and label for entering nodes.
  var entering = trail.enter().append("svg:g");

  entering.append("svg:polygon")
      .attr("points", breadcrumbPoints)
      .style("fill", function(d) { return d.data.color; });

	  
  entering.append("svg:text")
      .attr("x", (b.w + b.t) / 2)
      .attr("y", b.h / 2)
      .attr("dy", "0.35em")
      .attr("text-anchor", "middle")
      .text(function(d) { return d.data.name; });

  // Merge enter and update selections; set position for all nodes.
  entering.merge(trail).attr("transform", function(d, i) {
    return "translate(" + i * (b.w + b.s) + ", 0)";
  });

  /*// Now move and update the percentage at the end.
  d3.select("#trail").select("#endlabel")
      .attr("x", (nodeArray.length + 0.5) * (b.w + b.s))
      .attr("y", b.h / 2)
      .attr("dy", "0.35em")
      .attr("text-anchor", "middle")
      .text(percentageString);*/

  // Make the breadcrumb trail visible, if it's hidden.
  d3.select("#trail")
      .style("visibility", "");

}

function drawLegend() {

  // Dimensions of legend item: width, height, spacing, radius of rounded rect.
  var li = {
    w: 75, h: 30, s: 3, r: 3
  };

  var legend = d3.select("#legend").append("svg:svg")
      .attr("width", li.w)
      .attr("height", d3.keys(colors).length * (li.h + li.s));

  var g = legend.selectAll("g")
      .data(d3.entries(colors))
      .enter().append("svg:g")
      .attr("transform", function(d, i) {
              return "translate(0," + i * (li.h + li.s) + ")";
           });

  g.append("svg:rect")
      .attr("rx", li.r)
      .attr("ry", li.r)
      .attr("width", li.w)
      .attr("height", li.h)
      .style("fill", function(d) { return d.value; });

  g.append("svg:text")
      .attr("x", li.w / 2)
      .attr("y", li.h / 2)
      .attr("dy", "0.35em")
      .attr("text-anchor", "middle")
      .text(function(d) { return d.key; });
}

function toggleLegend() {
  var legend = d3.select("#legend");
  if (legend.style("visibility") == "hidden") {
    legend.style("visibility", "");
  } else {
    legend.style("visibility", "hidden");
  }
}

// Take a 2-column CSV and transform it into a hierarchical structure suitable
// for a partition layout. The first column is a sequence of step names, from
// root to leaf, separated by hyphens. The second column is a count of how 
// often that sequence occurred.
function buildHierarchy(csv) {
  var root = {"name": "root", "color":"", "emotion":"", "intensity":0, "children": []};
  for (var i = 0; i < csv.length; i++) {
    var sequence = csv[i].name;
    var size = +csv[i].value;
    if (isNaN(size)) { // e.g. if this is a header row
      continue;
    }
	var finalEmotion = { "name":csv[i].nameFinalEmotion, "emotion":csv[i].name, "intensity":100, "color":csv[i].colorFinalEmotion, "size": size + 1};
    var parts = [ 
		{ "name":csv[i].name, "emotion":csv[i].name, "intensity":0, "color":csv[i].color}
	   ].concat(csv[i].intensities);
    var currentNode = root;
    for (var j = 0; j < parts.length; j++) {
      var children = currentNode["children"];
      var nodeName = parts[j].name;
      var nodeColor = parts[j].color;
      var childNode;
      if (j + 1 < parts.length) {
   // Not yet at the end of the sequence; move down the tree.
 	var foundChild = false;
 	for (var k = 0; k < children.length; k++) {
 	  if (children[k]["name"] == nodeName) {
 	    childNode = children[k];
 	    foundChild = true;
 	    break;
 	  }
 	}
  // If we don't already have a child node for this branch, create it.
 	if (!foundChild) {
 	  childNode = {"name": nodeName, "emotion":csv[i].name, "color":nodeColor,"intensity":j, "children": []};
 	  children.push(childNode);
 	}
 	currentNode = childNode;
      } else {
 	// Reached the end of the sequence; create a leaf node.
 	childNode = {"name": nodeName, "emotion":csv[i].name, "color":nodeColor,"intensity":j, "children":[finalEmotion]};
 	children.push(childNode);
      }
    }
	
	
  }
  console.log(root);
  return root;
};

function click(d) {
  console.log("Clicked");
  var hasUnselectedEmotion = false;
  if(selectedEmotion != null && selectedEmotion.length > 0 && d.data.intensity < 100)
  { // Click on the same emotion, we unselect it
	for(var i = 0; i < selectedEmotion.length; i++)
	{
		if(selectedEmotion[i].data.emotion == d.data.emotion) 
		{ // We unselect it
			if(selectedEmotion[i].data.intensity == d.data.intensity)
			hasUnselectedEmotion = true;
			selectedEmotion.splice(i, 1);
		}
	}
  }
  
  if(d.data.intensity > 0 && d.data.intensity < 100 && !hasUnselectedEmotion && selectedEmotion.length < maxEmotions)
	selectedEmotion.push(d);

var textToDisplay = "Selected :";
for(var i = 0; i < selectedEmotion.length; i++)
	{
		if(i > 0) textToDisplay += ";;  ";
		textToDisplay+= selectedEmotion[i].data.emotion +" intensité "+selectedEmotion[i].data.intensity;
	}
d3.select("#selectedDisplay")
      .text(textToDisplay);
	  
		  // Fade all the segments.
		  d3.selectAll("path")
			  .style("opacity", 0.3);
	  HighlightSelectedEmotions();
}