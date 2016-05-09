<script type="text/javascript" src="view/js/clientes.js"></script>
<script type="text/javascript" src="utils/lib/js/jscolor.min.js"></script>
<div id="conteudo" style="margin-top: 20px;">
    <div id="lista">			
		<div class="titulo">
		<h1>MÁQUINAS CLIENTES</h1>
			<div class="search">						
				<span id="mini-pesquisa">
					<form onsubmit="return false;" action="">		
						<input name="pesquisa" id="pesquisa" type="text" size="30" value="" onkeypress="if((event.keyCode==13)||(event.keyCode==9)){xajax_listarClientes(document.getElementById('pesquisa').value)}" />
						<input type="button" value="Pesquisar" onclick="xajax_listarClientes(document.getElementById('pesquisa').value);" />
					</form> 
				</span>
				<span id="top-commands">  
					<input type='hidden' id='consulta' value='<?=$_SESSION["consulta"];?>' />					
					
					<input class="" type="button" value="Incluir Cliente" onclick="abrirPopupCadastroCliente();" />
					<input class="" type="button" value="Excluir selecionados" onclick="if(confirm('Confirma exclusão dos clientes selecionadas?')){xajax_excluirClientes(xajax.getFormValues('dataForm'))}">		
				
				</span>
			</div>
		</div>
		<div id="content">
			<div id="datatable-area">
				<div id="datatable"></div>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	listarClientes();
</script>

<div id="div-popup" style="display:none">
	<? include("view/forms/cliente.cadastro.view.php"); ?>
</div>
