//script criado externamente para facilitar manipulacao
//instrucao que executa as acoes somente apos o carregamento do DOM
$(document).ready(() => {
	//acao de click sobre o link de documentacao
	$('#documentacao').on('click', () => {
		//metodo get usado ao inves do load, para nao ficar explicito a solicitacao no back end
		$.post('documentacao.html', data => {
			$('#pagina').html(data)
		})
	})
	//acao de click sobre o link de documentacao
	$('#suporte').on('click', () => {
		//metodo get usado ao inves do load, para nao ficar explicito a solicitacao no back end
		$.post('suporte.html', data => {
			$('#pagina').html(data)
		})
	})

	//implementar o metodo ajax no script usando jquery, para evitar refresh na pagina, fazendo dela SPA
	$('#competencia').on('change', e =>{
		
		let competencia = $(e.target).val()

		//metodo ajax do jquery, objeto literal para requisicao assincrona, metodo, url, dados, acao em sucesso, e acao em erro
		$.ajax({
			type: 'GET',
			url: 'app.php',
			data: `competencia=${competencia}`,
			dataType: 'json',
			success: dados => {
				$('#numeroVendas').html(dados.numeroVendas)
				$('#totalVendas').html(dados.totalVendas)
				},
			error: erro => { console.log(erro)}
		})
	})
})