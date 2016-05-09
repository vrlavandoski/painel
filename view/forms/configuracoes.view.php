<script type="text/javascript" src="view/js/configuracoes.js"></script>
<div id="conteudo" style="margin-top: 20px;">
    <div id="lista">			
		<div class="titulo2">
			<h1>CONFIGURAÇÕES DO GRÁFICO</h1>
		</div>
	<div id="content">
		<form id="configForm" name="configForm" action="" onsubmit="return false">				
				<table class="fields">		 
				<tr>
					<td style="float: right; font-weight: bold; vertical-align: middle;">Titulo do gráfico:</td>
					<td>
						<input type="text" id="titulo" name="titulo" size="50" maxlength="150" style="margin-top: -2px;" /> 
						<div id="titulo-message" class="no-message"></div>
					</td>
				</tr>
				<tr>
					<td style="float: right; font-weight: bold; vertical-align: middle;">Valor máximo para a linha do X:</td>
					<td>
						<input type="text" id="valorMaximoX" name="valorMaximoX" size="20" style="margin-top: -2px;" /> 
						<div id="valorMaximoX-message" class="no-message"></div>
					</td>
				</tr>
				<tr>
					<td style="float: right; font-weight: bold; vertical-align: middle;">Valor máximo para a linha do Y:</td>
					<td>
						<input type="text" id="valorMaximoY" name="valorMaximoY" size="20" style="margin-top: -2px;"/> 
						<div id="valorMaximoY-message" class="no-message"></div>
					</td>
				</tr>	
				<tr>
					<td style="float: right; font-weight: bold; vertical-align: middle;">Intervelo para a linha do X:</td>
					<td>
						<select name="intervaloX" id="intervaloX">
							<option value="10">10 minutos</option>
							<option value="30">30 minutos</option>
							<option value="60">60 minutos</option>
						</select>
						
					</td>
				</tr>	
				<tr>
					<td style="float: right; font-weight: bold; vertical-align: middle; margin-top: 4px;">Intervalo para a linha do Y:</td>
					<td>
						<input type="text" id="intervaloY" name="intervaloY" size="20"/> 
						<div id="intervaloY-message" class="no-message"></div>
					</td>
				</tr>	
				<tr>
					<td style="float: right; font-weight: bold; vertical-align: middle;">Animar o gráfico?:</td>
					<td>
						<select name="animacao" id="animacao"><option value="S">S</option><option value="N">N</option></select>
						
					</td>
				</tr>	
				</table>
				<br/>
				<div class="nomessage" id="mensagem-configuracao"></div>
				<div id="controls">	
				<input class="button-default" type="button" value="Salvar" onclick="xajax_salvarConfiguracoes(xajax.getFormValues('configForm'));return false;" />
				<input class="button" type="button" value="Cancelar" onclick="window.location.href='index.php';return false;" />
				</div>					
		</form>		
	</div>
	</div>
	<script type="text/javascript">
		obterConfiguracoes();
	</script>
	