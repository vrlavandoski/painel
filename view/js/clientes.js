function listarClientes(){
	xajax_listarClientes('');
}

function abrirPopupEdicaoCliente(id){
	xajax_obterCliente(id);
	new Boxy($("#div-popup"),{
		title: "Edição de máquina cliente", 
		modal: true,
		unloadOnHide: false,
		afterShow: ajustarForm,
		afterHide: limparForm
	});
}

function abrirPopupCadastroCliente(){
	new Boxy($("#div-popup"),{
		title: "Inclusão de máquina cliente", 
		modal: true,
		unloadOnHide: false,
		afterShow: ajustarForm,
		afterHide: limparForm
	});
	limparForm();
}

function fecharPopupCadastroCliente() {
	Boxy.get("#nome").hide();
	limparForm();
}

function ajustarForm(){
	$("#nome").focus();		
}

function ajustarCorCampo(cor){
	document.getElementById("corGrafico").style.backgroundColor = "#" + cor;
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
	document.getElementById("corGrafico").style.backgroundColor = "";
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