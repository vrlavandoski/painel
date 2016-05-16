var atualizar;
var jaConfigurouGrafico = false;

function atu() {	
	initGrafico();	
}

function iniciarTimer() {
	atualizar = window.setInterval("atu()", 60000);	
}

function initGrafico() {
	xajax_obterDadosGrafico();
}

function montarGrafico(conf,legendaServer,dadosServer){	
	dados = eval(dadosServer);
	legendas = eval(legendaServer);	
	if(conf.animacao == 'S'){
		animar = true;
	}else{
		animar = false;
	}
	if (! jaConfigurouGrafico) {
	    var settings = {
	        title: conf.titulo,
	        description: "",
	        padding: { left: 5, top: 5, right: 5, bottom: 5 },
	        titlePadding: { left: 90, top: 0, right: 0, bottom: 10 },
	        source: dados,	        
	        backgroundColor: 'white',
	        borderLineColor: 'Black',
	        enableAnimations: animar,	        
	        categoryAxis:
	            {
	                dataField: 'data',
	                showGridLines: false,
	                gridLinesColor: 'Black'
	            },
	        seriesGroups:
	            [
	                {
	                    type: 'line',
	                    columnsGapPercent: 30,
	                    seriesGapPercent: 0,
	                    valueAxis:
	                    {
	                        minValue: 0,
	                        maxValue: conf.valorMaximoY,
	                        unitInterval: parseInt(conf.intervaloY),
	                        description: ''
	                    },
	                    series: legendas,
	                }
	            ]
	    };	    
	    $('#chartContainer').jqxChart(settings);
	    jaConfigurouGrafico = true;
	} else {
		apenasAtualizarDadosDoGrafico(dados);
	}
}

function apenasAtualizarDadosDoGrafico(data) {
	$('#chartContainer').jqxChart({source: data});	
}