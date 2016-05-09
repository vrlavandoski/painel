<form id="clienteForm" name="clienteForm" action="" onsubmit="return false">
		<input type="hidden" id="id" name="id" />
		<table class="fields">		 
		<tr>
			<td style="float: right; font-weight: bold; vertical-align: middle;">Nome:</td>
			<td>
				<input type="text" id="nome" name="nome" size="50" maxlength="150" style="margin-top: -2px;" /> 
				<div id="nome-message" class="no-message"></div>
			</td>
		</tr>
		<tr>
			<td style="float: right; font-weight: bold; vertical-align: middle;">Sigla para o gráfico:</td>
			<td>
				<input type="text" id="siglaGrafico" name="siglaGrafico" size="25" style="margin-top: -2px;" /> 
				<div id="sigla-message" class="no-message"></div>
			</td>
		</tr>
		<tr>
			<td style="float: right; font-weight: bold; vertical-align: middle;">Cor para o gráfico:</td>
			<td>
				<input type="text" id="corGrafico" name="corGrafico" size="25" style="margin-top: -2px;" class="jscolor"/> 
				<div id="cor-message" class="no-message"></div>
			</td>
		</tr>	
		<tr>
			<td style="float: right; font-weight: bold; vertical-align: middle;">Endereço ip:</td>
			<td>
				<input type="text" id="ip" name="ip" size="25" style="margin-top: -2px;"/> 
				<div id="ip-message" class="no-message"></div>
			</td>
		</tr>	
		<tr>
			<td style="float: right; font-weight: bold; vertical-align: middle;">Porta para conexão ssh:</td>
			<td>
				<input type="text" id="porta" name="porta" size="25" style="margin-top: -2px;"/> 
				<div id="porta-message" class="no-message"></div>
			</td>
		</tr>	
		<tr>
			<td style="float: right; font-weight: bold; vertical-align: middle;">Ativo:</td>
			<td>
				<select name="ativo" id="ativo"><option value="S">S</option><option value="N">N</option></select>
				
			</td>
		</tr>	
		</table>
		<br/>
		<div class="nomessage" id="mensagem-cliente"></div>
		<div id="controls">	
		<input class="button-default" type="button" value="Salvar" onclick="xajax_salvarCliente($('#id').val(), xajax.getFormValues('clienteForm'));return false;" />
		<input class="button" type="button" value="Cancelar" onclick="fecharPopupCadastroCliente();return false;" />
		</div>	
		
</form>