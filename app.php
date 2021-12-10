<?php
	#criacao de classe para instanciar objeto com todos atributos necessarios
	class Dashboard{

		#atributos publicos
		public $data_inicio; 
		public $data_fim;
		public $numeroVendas;
		public $totalVendas;

		#metodo publico magico do get
		public function __get($atributo) {
			return $this->$atributo;
		}

		#metodo publico magico do get
		public function __set($atributo, $valor) {
			$this->$atributo = $valor;
			return $this;
		}
	}

	#classe de conexao com o banco
	class Conexao{

		private $host = 'localhost';  #host da conexao
		private $dbname = 'dashboard'; #nome do banco de dados
		private $user = 'root'; #usuario do banco de dados
		private $pass = 'bds02101986'; #senha do banco de dados
	
		#metodo publico de conexao da classe conexao
		public function conectar(){
			#bloco try
			try{
				#variavel atribuida para instancia de PDO
				$conexao = new PDO(
					#parametros de conexao
					"mysql:host=$this->host;dbname=$this->dbname",
					"$this->user",
					"$this->pass"
				);
				#parametro para evitar possiveis incompatibilidade de caracteres entre o banco de dados, front e back end
				$conexao->exec('set charset utf8');

				return $conexao;

			} 
            #caso der algum erro, captura no catch
			catch (PDOException $e){
				echo '<p>' .$e->getMessage(). '</p>';
			}
		}

	}

	#classe para manipular o objeto no banco de dados
	class Bd {
		#atributos privados
		private $conexao;
		private $dashboard;

		#construtor privado, para receber conexao e dashboard
		public function __construct(Conexao $conexao, Dashboard $dashboard){
			#recuperar a conexao, atribuindo parametro da conexao ja executando o metodo conectar
			$this->conexao = $conexao->conectar();
			#recuperar o atributo dashboard atribuindo o objeto passado por parametro
			$this->dashboard = $dashboard;  
		}

		#recuperar os dados
		#metodo responsavel por recuperar o indicador de numero de vendas
		public function getNumeroVendas(){
			$query = 'select count(*) as numero_vendas from tb_vendas where data_venda between :data_inicio and :data_fim';
			$stmt = $this->conexao->prepare($query);  #receber o statement do PDO e prepara-la
			$stmt->bindValue(':data_inicio', $this->dashboard->__get('data_inicio')); #operacao de bindvalue
			$stmt->bindValue(':data_fim', $this->dashboard->__get('data_fim')); #operacao de bindvalue
			$stmt->execute(); #executar a query
			#retorna o valor do banco de dados em forma de objeto
			return $stmt->fetch(PDO::FETCH_OBJ)->numero_vendas;   
		}

		#recuperar os dados
		#metodo responsavel por recuperar o indicador de numero total de vendas
		public function getTotalVendas(){
			$query = 'select SUM(total)  as total_vendas from tb_vendas where data_venda between :data_inicio and :data_fim';
			$stmt = $this->conexao->prepare($query);  #receber o statement do PDO e prepara-la
			$stmt->bindValue(':data_inicio', $this->dashboard->__get('data_inicio')); #operacao de bindvalue
			$stmt->bindValue(':data_fim', $this->dashboard->__get('data_fim')); #operacao de bindvalue
			$stmt->execute(); #executar a query

			#retorna o valor do banco de dados em forma de objeto
			return $stmt->fetch(PDO::FETCH_OBJ)->total_vendas;   
		}
	}
	#logica do script
	#juntar tudo atraves de instancias
	$dashboard = new Dashboard();
	$conexao = new Conexao();

	$competencia = explode('-',$_GET['competencia']);
	$ano = $competencia[0];
	$mes = $competencia[1];
	$dias_do_mes = cal_days_in_month(CAL_GREGORIAN, $mes, $ano);

	$dashboard->__set('data_inicio', $ano.'/'.$mes.'-01');
	$dashboard->__set('data_fim',$ano.'-'.$mes.'-'.$dias_do_mes);

	$bd = new Bd($conexao, $dashboard);
	$bd->getNumeroVendas();
	$dashboard->__set('numeroVendas',$bd->getNumeroVendas());
	$dashboard->__set('totalVendas',$bd->getTotalVendas());
	$bd->getTotalVendas();

	echo json_encode($dashboard);

?>