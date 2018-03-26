var layout2 = '';

function getData() {
    console.log("getData");
    console.log("selection : "+w2ui['gridFormations'].getSelection());
    var dateDebut = $('input[type=dateDebut]').w2field().get(); 
    var dateFin = $('input[type=dateFin]').w2field().get();
    var formationSel =w2ui['gridFormations'].getSelection();
    var dt = {"dateDebut": dateDebut, "dateFin":dateFin, "formationSel":formationSel};
    $.ajax({
        url: "getdatastream",
        data: dt,
        type: 'post',
        dataType: 'json',
        error: function(error){
            try {
                var js = JSON.parse(error.responseText);
                w2alert("Erreur : "+error.responseText+"\n error :"+error);
            } catch (e) {
                console.log(error.responseText)            		  	
                w2alert("Erreur : "+e+"\n dt :"+dt);
            }
        },            	
        success: function(result) {
            // w2alert("Données envoyées au serveur et enregistrées avec succès"); 
            // w2ui['layout2'].content('bottom',"onclick clicked !!! "+dateDebut+" jusqu'à"+dateFin+"\n"+result);
            console.log(result);
            w2ui['layout2'].content('main',"<div id='titresViz'>"+
                        "<p align='center' id='major'>-</p>"+
                        "</div>"+
                        "<p align='center' id='viz'>-</p>"+
                        "<div class='chart'>"+
                                    "</div>");
            if(JSON.parse(result["dateJSON"]).length > 1){
                drawStream(JSON.parse(result["emotionsJSON"]), JSON.parse(result["resultJSON"]),true);
            }
            else {//pas assez d'émotions pour tracer le streamgraph
                w2ui['layout2'].content('main',"La sélection ne retourne pas assez d'émotions pour tracer le graphe.")
            }
    }
});
}

$(function () {
    var pstyle = 'font-size:16px; border: 1px solid #dfdfdf; padding: 5px;';
    $('#layout').w2layout({
        name: 'layout',
        panels: [
            { type: 'top', size: 50, style: pstyle, content: "Entête du haut avec nom d'utilisateur" },
            { type: 'main', style: pstyle, content: 'main' },
            { type: 'right', size: 300, style: pstyle, 
            content: '<div class="block">'+
                        '<b>Date</b>'+
                        '<div>'+
                            '<br><label>Date de Début :</label> <input type="dateDebut">'+
                            '<br><br>'+
                            '<label>Date de Fin :  </label> <input type="dateFin">'+
                        '</div>'+
                        '<br><br>'+
                        '<div id="gridFormations" style="width: 100%; height: 350px;"></div>'+
                        '<div class="w2ui-buttons">'+
                            '<button class="w2ui-btn" name="valider" onclick="getData()">Valider</button>'+
                        '</div>'+
                    '</div>'
            }
        ]
    });
    $().w2layout({
        name: 'layout2',
        panels: [
            { type: 'main', size: 80, resizable: true, style: pstyle, 
            content: "<div id='titresViz'>"+
            "<p align='center' id='major'>-</p>"+
            "</div>"+
            "<p align='center' id='viz'>-</p>"+
            "<div class='chart'>"+
                        "</div>" },
                        { type: 'bottom', size: 200, resizable: true, style: pstyle, 
                content: 'la liste des étudiants se foutera ici' }
            ]
    });
    
    w2ui['layout'].content('main', w2ui['layout2']);

    var grid ={
        name: 'gridFormations',
        show: { selectColumn: true },
        multiSelect: true,
        recid: 'recid',
        
	    columns: [
	    	{ field: 'nom', caption: 'Formations', size: '100%'}
	    ],
	    records : formations
    };
    
    $('#gridFormations').w2grid(grid);
    
});




setTimeout(function(){
    console.log("formations : "+formations);
    $('input[type=dateDebut]').w2field('date',  { format: 'yyyy-mm-dd', end: $('input[type=dateFin]') });
    $('input[type=dateFin]').w2field('date',  { format: 'yyyy-mm-dd', start: $('input[type=dateDebut]') });
    // layout2 = w2ui['layout2'].get('main');
    // console.log("putain height :"+document.getElementById('layout_layout2_panel_main').height);
    // console.log(document.getElementsByName('layout2'));
    // console.log("layout2 :"+layout2);
    // console.log(w2ui['layout2'].get('main').height);
    // console.log(w2ui['layout2'].get('main').width);
    
    // var refData=[],refKey=[],refTag=[],legData={colors:[],labels:[]}, nbTotal=0, refTotal=[];

    
    
    var keys = emotionsJSON;
    var data = resultJSON;
    drawStream(keys, data,false);
    
    
}, 300);

function drawStream(keys,data,update){
    colorrange = ["#B30000", "#E34A33", "#FC8D59", "#FDBB84", "#FDD49E", "#FEF0D9", "#DD1BC6", "#8AD724"];
    strokecolor = colorrange[0];
    // data : { resultJSON: resutJSON ; emotionsJSON: emotionsJSON };
    
    // var keys = <?php echo $this->emotionsJSON;?>;
    var datearray = [];
    var parse = d3.timeParse("%Y-%m-%d %H:%M:%S");
    //   var data = <?php echo $this->resultJSON;?>;
    data.forEach(function(d){
        d.key = d.key;
        d.value = +d.value;
        d.date = parse(d.date);
    });
    
    // refTotal['total']=0;
    // data.forEach(function(dt){
    //     //transforme le temps en date
    //     var dRef = dt.date;		  
    //     var k = dt.key+'-'+dRef;		  
    //     refData[k] = dt;
    //     //cumul les clefs
    //     if(refKey.indexOf(dt.key)<0)refKey.push(dt.key);
    //     //cumul les dates
    //     if(!refTotal[dt.temps]){			  
    //         refTotal[dt.temps]=Math.trunc(dt.value);
    //     }else{
    //         refTotal[dt.temps] += Math.trunc(dt.value);
	// 	  }
	// 	  refTotal['total'] += Math.trunc(dt.value);
		  
	// 	  //cumul les tags
	// 	  if(!refTag[dt.key]){			  
    //           refTag[dt.key]={"type":dt.type,"desc":dt.desc,"value":Math.trunc(dt.value)};
	// 	  }else{
    //           refTag[dt.key].value += Math.trunc(dt.value);
	// 	  }
		  
    //     });
    //     //création des couleurs et du tableau de la légende	  
    //     var sc = d3.scaleLinear().range([0, 1]).domain([0, refKey.length+1]);
    //     refKey.forEach(function(r,i){
    //         nbTotal += refTag[r].value;
    //         var numColor = sc(i);	  
    //         refTag[r].color=d3.color(numColor);
    //         legData.colors.push(refTag[r].color);		  
    //         legData.labels.push(refTag[r].type);		  
    //     });
        
    var nested_data = d3.nest ()
    .key(function(d) {return d.date;})
    .entries(data);
    var mqpdata = nested_data.map(function(d){
        var obj = {
            utc: d.key,
            dt: d.key,	      
        }
        
        d.values.forEach(function(v){
            obj[v.key] = v.value;
            
        })
        
        return obj;
    });

    var data = mqpdata;var stack = d3.stack()
    .keys(keys)
    .order(d3.stackOrderNone)
    .offset(d3.stackOffsetWiggle);

    var series = stack(data);
    // var divTitreHeight = document.getElementById('titresViz').clientHeight;	
    var divTitreHeight = document.getElementById('titresViz').clientHeight;	
    var margin = {top: 20, right: 30, bottom: 30, left: 30};
    // var width = window.innerWidth - margin.left - margin.right;
    // var height = window.innerHeight - margin.top - margin.bottom - divTitreHeight;

    var height = parseInt(document.getElementById('layout_layout2_panel_main').style.height,10) -20;
    var width = parseInt(document.getElementById('layout_layout2_panel_main').style.width,10) -20;

    var extData = d3.extent(data, function(d){ 
        return new Date(d.utc); 
    });
    var x = d3.scaleTime()
    .domain([extData[0],extData[1]])
    .range([margin.top, height-margin.bottom]);	

    var y = d3.scaleLinear()
    .domain([0, d3.max(series, function(layer) { return d3.max(layer, function(d){ return d[0] + d[1];}); })])
    .range([width/2, margin.left+margin.right]);

    var z = d3.scaleOrdinal()
    .range(colorrange);

    // setup axis
    var xAxis = d3.axisLeft(y);
    var yAxis = d3.axisBottom(x);
    
    
    var area = d3.area()
        .x(function(d) { 
            var yTest =x(new Date(d.data.utc));
            return yTest; 
        })
        .y0(function(d) { 
            var xTest = y(d[0]);
            return xTest; 
            })
            .y1(function(d) { 
                var xTest = y(d[1]);
                return xTest; 
            })
            .curve(d3.curveBasis);
                
                
    
    if (update){
        w2ui['layout2'].content('main',"<div id='titresViz'>"+
        "<p align='center' id='major'>-</p>"+
        "</div>"+
        "<p align='center' id='viz'>-</p>"+
        "<div class='chart'>"+
                    "</div>");


        // d3.selectAll("g > *").remove()

        // d3.selectAll("path")
        // .data(series)
        // .transition()
        // .duration(750)
        // .attr("d", area)
        // .style("fill", function(d, i) { return z(i); });

        
    }
    // else {

        var tooltip = d3.select("body")
        .append("div")
        .attr("class", "remove")
        .style("position", "absolute")
        .style("z-index", "20")
        .style("visibility", "hidden")
        .style("top", "30px")
        .style("left", "55px");
        
        var svg = d3.select("#viz").append("svg")
        .attr("id", "svgGlobal")
        .attr("width", width)
        .attr("height", height);
        
        svg.selectAll("path")
        .data(series)
        .enter().append("path")
        .attr("d", area)
        .style("fill", function(d, i) { return z(i); })
        .on('mouseover', function(d){
            colorInit = d3.select(this).style("fill");      
            d3.select(this).style('fill',d3.rgb(colorInit).brighter());
            // d3.select("#major").text(refTag[d.key].type);
            tooltip.transition()
            .duration(700)
            .style("opacity", 1);
        })
        .on("mousemove", function(d, i) {
            mousex = d3.mouse(this);
            mousex = mousex[0];
            var invertedx = x.invert(mousex);
            var dateToCompare = new Date(invertedx);
            invertedx = invertedx.getMonth() + invertedx.getDate()+ invertedx.getHours() + invertedx.getMinutes();
            for (var k = 0; k < d.length; k++) {
                datearray[k] = d[k].data.utc;
            }
            var foundDate = dateFns.closestTo(dateToCompare,datearray);
            
            var foundDateIndex = dateFns.closestIndexTo(dateToCompare,datearray);
            
            pro = d[foundDateIndex].data[d.key];
            ladate = d[foundDateIndex].data.utc;
            
            d3.select(this)
            .classed("hover", true)
            .attr("stroke", strokecolor)
            .attr("stroke-width", "0.5px"), 
            tooltip.html( "<p>" + d.key + "<br>" + pro +"<br>"+ladate+ "</p>" ).style("visibility", "visible");
            
        })
        .on("mouseout", function(d, i) {
            d3.select(this).style('fill',d3.rgb(colorInit));
            svg.selectAll(".layer")
            .transition()
            .duration(250)
            .attr("opacity", "1");
            d3.select(this)
            .classed("hover", false)
            .attr("stroke-width", "0px"), tooltip.html( "<p>" + d.key + "<br>" + pro +"<br>"+ladate+ "</p>" ).style("visibility", "hidden");
        })
        
        //construction de l'axe y
        svg.append("g")
        .attr("class", "axis axis--y")
        .attr("transform", "translate(" + 0 + "," + 600 + ")")
        .call(yAxis);  
        
        var vertical = d3.select("#viz")
        .append("div")
        .attr("class", "remove")
        .style("position", "absolute")
        .style("z-index", "19")
        .style("width", "1px")
        .style("height", height)
        .style("top", "10px")
        .style("bottom", "30px")
        .style("left", "0px")
        .style("background", "#fff");
        
        d3.select("#viz")
        .on("mousemove", function(){  
            mousex = d3.mouse(this);
            mousex = mousex[0] + 5;
            vertical.style("left", mousex + "px" )})
            .on("mouseover", function(){  
                mousex = d3.mouse(this);
                mousex = mousex[0] + 5;
                vertical.style("left", mousex + "px")});
    // }
        
}