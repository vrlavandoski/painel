function obterConfiguracoes(){
	xajax_obterConfiguracoes('');
}


function ajustarForm(){
	$("#nome").focus();
}

function limparForm(){
	$("#id").val("");
	$("#nome").val("");
	$("#nome").removeClass("warning");
	$("#nome-message").html("");
	$("#nome-message").removeClass("tip-message");	
	$("#nome").val("");
	$("#nome").removeClass("warning");
	$("#nome-message").html("");
	$("#nome-message").removeClass("tip-message");	
	$("#siglaGrafico").val("");
	$("#siglaGrafico").removeClass("warning");
	$("#sigla-message").html("");
	$("#sigla-message").removeClass("tip-message");	
	$("#corGrafico").val("");
	$("#corGrafico").removeClass("warning");
	$("#cor-message").html("");
	$("#cor-message").removeClass("tip-message");	
	$("#ip").val("");
	$("#ip").removeClass("warning");
	$("#ip-message").html("");
	$("#ip-message").removeClass("tip-message");	
	$("#porta").val("");
	$("#porta").removeClass("warning");
	$("#porta-message").html("");
	$("#porta-message").removeClass("tip-message");	
	$("#mensagem-cliente").html("");
}