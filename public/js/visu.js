var layout2 = '';
var colorInit = "";
var dtEmo = {"date": "", "emotion":""};


function getData() {
    // console.log("getData");
    // console.log("selection : "+w2ui['gridFormations'].getSelection());
    var dateDebut = $('input[type=dateDebut]').w2field().get(); 
    var dateFin = $('input[type=dateFin]').w2field().get();
    var formationSel =w2ui['gridFormations'].getSelection();
    var emos = w2ui['gridEmos'].getSelection();
    var dt = {"dateDebut": dateDebut, "dateFin":dateFin, "formationSel":formationSel, "emos":emos};
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
            // console.log(result);
            w2ui['layout2'].content('main',"<div id='titresViz'>"+
                        "<p  id='major'></p>"+
                        "</div>"+
                        "<p  id='viz'></p>"+
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

function identifier(){
    // console.log("click sur identifier !!");
    // console.log("dtEmo : date="+dtEmo["date"]+" emotion="+dtEmo["emotion"]);
    $.ajax({
        url: "identifieretudiants",
        data: dtEmo,
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
            // console.log(result);
            if (w2ui['gridEtudiants']){
                w2ui['gridEtudiants'].destroy();
                } 
            var gridEtudiants ={
                name: 'gridEtudiants',
                recid: 'recid',
                
                columns: [
                    { field: 'idEtu', caption: 'Id Etudiant', size: '100%'},
                    { field: 'nom', caption: 'Nom', size: '100%'},
                    { field: 'prenom', caption: 'Prénom', size: '100%'},
                    { field: 'formation', caption: 'Formation', size: '100%'},
                    { field: 'emotion', caption: 'Emotion', size: '100%'},
                    { field: 'valeur', caption: 'Valeur', size: '100%'},
                    { field: 'date', caption: 'Date', size: '100%'}
                ],
                records : JSON.parse(result["rs"])
            };
            $('#gridEtudiants').w2grid(gridEtudiants);
        }
    });

}

$(function () {
    var pstyle = 'font-size:16px; border: 1px solid #dfdfdf; padding: 5px;';
    var textTop = '';
    if (role.indexOf('admin') !== -1){
        textTop = '<div class="row">'+
        '<div class="column left" style="width:75%;">'+
            '<div id="text" style="float: left; font-size:20px;">'+
                'Espace Enseignant - Utilisateur : '+user+
            '</div>'+
        '</div>'+
        '<div class="column right" style="width:25%;" >'+
            '<div class="w2ui-buttons" style="float: right;" >'+
            '<button class="w2ui-btn w2ui-btn-grey"  name="admin" onclick="administration()">Administration</button> '+
            '<button class="w2ui-btn w2ui-btn-grey"  name="deconnexion" onclick="disconnect()">Déconnexion</button><br><br>'+
        '</div>'+
        '</div>';
    }
    else {
        textTop = '<div class="row">'+
        '<div class="column left" style="width:75%;">'+
            '<div id="text" style="float: left; font-size:20px;">'+
                'Espace Enseignant - Utilisateur : '+user+
            '</div>'+
        '</div>'+
        '<div class="column right" style="width:25%;" >'+
            '<div class="w2ui-buttons" style="float: right;" >'+
            '<button class="w2ui-btn w2ui-btn-grey"  name="deconnexion" onclick="disconnect()">Déconnexion</button><br><br>'+
        '</div>'+
        '</div>';
    }
    $('#layout').w2layout({
        name: 'layout',
        panels: [
            { type: 'top', size: 50, style: 'font-size:16px; border: 1px solid #dfdfdf; padding: 1px;',
                     content: textTop },
            { type: 'main', style: pstyle, content: 'main' },
            { type: 'right', size: 300, style: pstyle, 
            content: '<div class="block">'+
                        '<b>Filtres</b>'+
                        '<br><u>Dates</u> :'+
                        '<div>'+
                            '<br><label>Date de Début :</label> <input type="dateDebut">'+
                            '<br><br>'+
                            '<label>Date de Fin :  </label> <input type="dateFin">'+
                        '</div>'+
                        '<br><u>Formations</u> :<br>'+
                        '<div id="gridFormations" style="width: 100%; height: 200px;"></div>'+
                        '<br><u>Emotions</u> :<br>'+
                        '<div id="gridEmos" style="width: 100%; height: 220px;"></div>'+
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
            "<p  id='major'></p>"+
            "</div>"+
            "<p  id='viz'></p>"+
            "<div class='chart'>"+
                        "</div>" },
                        { type: 'bottom', size: 250, resizable: true, style: pstyle, 
                content: "Cliquez sur le graphe pour afficher plus d'informations sur la saisie des étudiants." }
            ]
    });
    
    w2ui['layout'].content('main', w2ui['layout2']);

    var gridFormations ={
        name: 'gridFormations',
        show: { selectColumn: true },
        multiSelect: true,
        fixedBody: true,
        recid: 'recid',
        
	    columns: [
	    	{ field: 'nom', caption: '', size: '100%'}
	    ],
	    records : formations
    };

    var gridEmos ={
        name: 'gridEmos',
        show: { selectColumn: true },
        multiSelect: true,
        fixedBody: true,
        recid: 'recid',
        
	    columns: [
	    	{ field: 'code', caption: '', size: '100%'}
	    ],
	    records : emos
    };
    
    $('#gridFormations').w2grid(gridFormations);
    $('#gridEmos').w2grid(gridEmos);

    $('#gridFormations').w2grid().selectAll();
    $('#gridEmos').w2grid().selectAll();

    

    
});




setTimeout(function(){
    $('input[type=dateDebut]').w2field('date',  { format: 'yyyy-mm-dd', end: $('input[type=dateFin]') });
    $('input[type=dateFin]').w2field('date',  { format: 'yyyy-mm-dd', start: $('input[type=dateDebut]') });

    $('.loader').fadeOut();
    var keys = emotionsJSON;
    var data = resultJSON;
    drawStream(keys, data,false);
    
}, 3000);



// function to ensure the tip doesn't hang off the side
function tipX(x){
    // var winWidth = $(window).width();
    var winWidth = parseInt(document.getElementById('layout_layout2_panel_main').style.width,10) -20;//document.getElementById('titresViz').clientHeight;
    var tipWidth = $('.tip').width();
    // if (breakpoint == 'xs'){
    //   x > winWidth - tipWidth - 20 ? y = x-tipWidth : y = x;
    // } else {
      x > winWidth - tipWidth - 30 ? y = x-45-tipWidth : y = x+10;
    // }
    return y;
  }

function drawStream(keys,data,update){
    // colorrange = ["#B30000", "#E34A33", "#FC8D59", "#FDBB84", "#FDD49E", "#FEF0D9", "#DD1BC6", "#8AD724"];
    colorrange =['#66c2a5','#fc8d62','#8da0cb','#e78ac3','#a6d854','#ffd92f','#e5c494','#b3b3b3'];
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
    // var divTitreHeight = document.getElementById('titresViz').clientHeight;	
    var margin = {top: 20, right: 30, bottom: 30, left: 30};
    // var width = window.innerWidth - margin.left - margin.right;
    // var height = window.innerHeight - margin.top - margin.bottom - divTitreHeight;

    var height = parseInt(document.getElementById('layout_layout2_panel_main').style.height,10) -40;
    var width = parseInt(document.getElementById('layout_layout2_panel_main').style.width,10) -20;

    var extData = d3.extent(data, function(d){ 
        return new Date(d.utc); 
    });
    var x = d3.scaleTime()
    .domain([extData[0],extData[1]])
    // .range([margin.top+100, height-margin.bottom+100]);	
    .range([100,width-100]);	

    var y = d3.scaleLinear()
    .domain([0, d3.max(series, function(layer) { return d3.max(layer, function(d){ return d[0] + d[1];}); })])
    // .range([width/4, margin.left+margin.right-100]);
    .range([height-20,0]);	

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
            var xTest = y(d[0])-.2;
            return xTest; 
            })
        .y1(function(d) { 
            var xTest = y(d[1])+.2;
            return xTest; 
        })
        .curve(d3.curveBasis);
                
                
    
    if (update){
        w2ui['layout2'].content('main',"<div id='titresViz'>"+
        "<p  id='major'></p>"+
        "</div>"+
        "<p  id='viz'></p>"+
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


        $('#viz').prepend('<div class="legend"><div class="title">Emotions :</div></div>');
        $('.legend').hide();
        var legend = []
        series.forEach(function(d,i){
        var obj = {};
        obj.key = d.key;
        obj.color = colorrange[i];
        legend.push(obj);
        });

        // others
        // if (series.length>7){legend.push({key: "Other",color: "#b3b3b3"});}

        legend.forEach(function(d,i){
        $('.legend').append('<div class="item"><div class="swatch" style="background: '+d.color+'"></div>'+d.key+'</div>');
        });

        $('.legend').fadeIn();

        var chartTop = $('#viz').offset().top;

        var tooltip = d3.select("#viz")
        .append("div")
        .attr("class", "tooltip")
        .style("position", "absolute")
        .style("z-index", "20")
        .style("visibility", "hidden")
        .style("top", chartTop/2+"px");
        // .style("top", "30px");
        // .style("left", tipX(mousex)+"px");
        
        var svg = d3.select("#viz").append("svg")
        .attr("id", "svgGlobal")
        .attr("width", width)
        .attr("height", height);
        
        svg.selectAll(".layer")
        .data(series)
        .enter().append("path")
        .attr("class",'layer')
        .attr("d", area)
        .style("fill", function(d, i) { return z(i); });

        svg.selectAll(".layer")
        .attr("opacity", 1)
        .on('mouseover', function(d,i){
            
            // colorInit = d3.select(this).style("fill");      
            // d3.select(this).style('fill',d3.rgb(colorInit).brighter());
            
            // d3.select("#major").text(refTag[d.key].type);
            // tooltip.transition()
            // .duration(700)
            // .style("opacity", 1);
            svg.selectAll(".layer").transition()
            .duration(250)
            .attr("opacity", function(d, j) {
                return j != i ? 0.6 : 1;
            })
        })
        .on("mousemove", function(d, i) {
            // colorInit = d3.select(this).style("fill");    
            // d3.select(this).style('fill',d3.rgb(colorInit).brighter());
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
            ladate = dateFns.format(d[foundDateIndex].data.utc,'YYYY-MM-DD HH:mm');
            
            d3.select(this)
            .classed("hover", true)
            .attr("stroke", strokecolor)
            .attr("stroke-width", "0.5px"), 
            tooltip.html( "<p>Emotion :" + d.key + "<br>Valeur :" + pro +"<br>Date :"+ladate+ "</p>" ).style("visibility", "visible")
            .style("left", tipX(mousex)+"px");
            
        })
        .on("mouseout", function(d, i) {
            // d3.select(this).style('fill',d3.rgb(colorInit));
            svg.selectAll(".layer")
            .transition()
            .duration(250)
            .attr("opacity", "1");
            d3.select(this)
            .classed("hover", false)
            .attr("stroke-width", "0px"), tooltip.html( "<p>" + d.key + "<br>" + pro +"<br>"+ladate+ "</p>" ).style("visibility", "hidden");
        })
        .on("click", function(d,i){
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
            
            
            ladate = dateFns.format(d[foundDateIndex].data.utc,'YYYY-MM-DD HH:mm');

            w2ui['layout2'].content('bottom', '<div id="gridEtudiants" style="width: 100%; height: 180px;"></div>'+
                                '<br>'+
                                '<div class="w2ui-buttons" style="float: right;">'+
                                    '<button class="w2ui-btn" name="valider" onclick="identifier()">Identifier</button>'+
                                '</div>');

            dtEmo = {"date": ladate, "emotion":d.key};
            $.ajax({
                url: "getemotiondate",
                data: dtEmo,
                type: 'post',
                dataType: 'json',
                error: function(error){
                    try {
                        var js = JSON.parse(error.responseText);
                        w2alert("Erreur : "+error.responseText+"\n error :"+error);
                    } catch (e) {
                        console.log(error.responseText)            		  	
                        w2alert("Erreur : "+e+"\n dt :"+dtEmo);
                    }
                },            	
                success: function(result) {
                   
                    if (w2ui['gridEtudiants']){
                        w2ui['gridEtudiants'].destroy();
                        } 
                    var gridEtudiants ={
                        name: 'gridEtudiants',
                        recid: 'recid',
                        
                        columns: [
                            { field: 'idEtu', caption: 'Id Etudiant', size: '100%'},
                            { field: 'emotion', caption: 'Emotion', size: '100%'},
                            { field: 'valeur', caption: 'Valeur', size: '100%'},
                            { field: 'date', caption: 'Date', size: '100%'}
                        ],
                        records : JSON.parse(result["rs"])
                    };
                    $('#gridEtudiants').w2grid(gridEtudiants);
                }
            });

        })
        
        //construction de l'axe y
        svg.append("g")
        .attr("class", "axis axis--y")
        .attr("transform", "translate(" + 0 + "," + (height-20) + ")")
        .call(yAxis);  
        
        // var vertical = d3.select("#viz")
        // .append("div")
        // .attr("class", "remove")
        // .style("position", "absolute")
        // .style("z-index", "19")
        // .style("width", "1px")
        // .style("height", height)
        // .style("top", "10px")
        // .style("bottom", "30px")
        // .style("left", "0px")
        // .style("background", "#fff");
        
        // d3.select("#viz")
        // .on("mousemove", function(){  
        //     mousex = d3.mouse(this);
        //     mousex = mousex[0] + 5;
        //     vertical.style("left", mousex + "px" )})
        //     .on("mouseover", function(){  
        //         mousex = d3.mouse(this);
        //         mousex = mousex[0] + 5;
        //         vertical.style("left", mousex + "px")});
    // }
        
}

function disconnect() {
    window.location.href="../auth/deconnexion";
}

function administration() {
    window.location.href="../admin/importcsv";
}