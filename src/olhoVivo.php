<?php

// Biblioteca para acessar a API do olho vivo, SP Trans - Rafael Duarte

namespace RafaelDuarte\OlhoVivo;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class OlhoVivo
{
    public $token;
    public $url = 'http://api.olhovivo.sptrans.com.br/';
    public $versao = "v2.1/"; // Versão da api olho vivo
    private $autenticado = false;
    private $client;

    public function autenticar() // Autenticar usuário na API
    {
        $return = false;
        try {
            $this->client = new Client([
                'base_uri' => $this->url . $this->versao,
                'timeout' => 2.0,
                'cookies' => true,
                'decode_content' => false
            ]); // Inicia um Client na URL informada da api
            
            $login = $this->client->request(
                'POST',
                'Login/Autenticar',
                ['query' => ['token' => $this->token]]
            ); // Realiza a request de autenticação com o token de aplicativo
            if (!(json_decode($login->getBody()))) { // Retorna true para autenticado e false para não autenticado
                throw new \Exception("Erro ao autenticar com este token.");
            } elseif (!($login->hasHeader('Set-Cookie'))) {
                throw new \Exception("O servidor não está configurado para definir as credenciais necessárias para a autenticação do usuário.");
            }
            if ($login->getBody()) {
                $this->autenticado = true;
                $return = $login->getBody();
            }
        } catch (RequestException $e) {
            throw new \Exception("HTTP request/response erro: {$e->getMessage()}");
        } finally {
            return $return;
        }
    }

    private function execute($uri, $params = [], bool $decodeAsJson = true)
    { // Executar requisição via GET para os endpoints da API
        if (!$this->autenticado) {
            return 'Você não está autenticado';
        }
        try {
            do {
				$request = ($this->client->request(
                    'GET',
                    $uri,
                    count($params) > 0 ? ['query' => $params] : []
                ))->getBody();
                $decoded = json_decode($request, true);
            } while (isset($decoded['Message']));
            return $decodeAsJson === true ? $decoded : $request;
        } catch (RequestException $e) {
			throw new \Exception("HTTP request/response error: {$e->getMessage()}");
        }
    }

    private function executeObterKMZ($rota = '')
    {
        $res = $this->client->request('GET', $this->url . $this->versao . 'KMZ' . $rota, [
            'headers' => [
                'Accept-Encoding' => 'gzip',
                'Content-Type' => 'application/vnd.google-earth.kmz'
            ],
            'stream' => true
        ]);
            
        // Verifica se a resposta foi bem-sucedida
        if ($res->getStatusCode() == 200) {
            // Salva o conteúdo do arquivo KMZ
            file_put_contents('mapa.kmz', $res->getBody());
            return true;
        } else {
            return false;
        }
    }

    public function buscarLinhas($linha)
    { // Buscar as linhas expecíficas de São Paulo
        $queryParams = [
            'termosBusca' => $linha,
        ];
        return json_decode(json_encode($this->execute($this->url . $this->versao . 'Linha/Buscar', $queryParams)), false);
        // Retorna um objeto
    }

    public function buscarParadas($endereco)
    {
        $queryParams = [
            'termosBusca' => $endereco,
        ];
        return $this->execute($this->url . $this->versao . 'Parada/Buscar', $queryParams);
    } // Buscar as paradas expecíficas de São Paulo

    public function buscarCorredores()
    {
        return json_decode(json_encode($this->execute($this->url . $this->versao . 'Corredor')), false);
    } // Buscar todos os corredores de São Paulo

    public function buscarEmpresas()
    {
        return json_decode(json_encode($this->execute($this->url . $this->versao . 'Empresa')), false);
    } // Buscar todas as empresas operadoras do transporte público de São Paulo

    public function buscarParadasPorLinha($codigoLinha)
    {
        $queryParams = [
            'codigoLinha' => intval($codigoLinha),
        ];
        return json_decode(json_encode($this->execute($this->url . $this->versao . 'Parada/BuscarParadasPorLinha', $queryParams)), false);
    } // Buscar as paradas por linhas de São Paulo

    public function buscarParadasPorCorredor($codigoCorredor)
    {
        $queryParams = [
            'codigoCorredor' => intval($codigoCorredor),
        ];
        return json_decode(json_encode($this->execute($this->url . $this->versao . 'Parada/BuscarParadasPorCorredor', $queryParams)), false);
    } // Buscar as paradas de São Paulo por corredor

    public function buscarPosicaoTodosOnibus()
    {
        return json_decode(json_encode($this->execute($this->url . $this->versao . 'Posicao')), false);
    } // Buscar as posições de todos os ônibus de de São Paulo

    public function buscarPosicaoOnibusEspecifico($codigoLinha)
    {
        $queryParams = [
            'codigoLinha' => intval($codigoLinha),
        ];
        return json_decode(json_encode($this->execute($this->url . $this->versao . 'Posicao/Linha', $queryParams)), false);
    } // Buscar a posição de ônibus de linhas específicas de São Paulo

    public function buscarVeiculosGaragem($codigoEmpresa, $codigoLinha = '')
    {
        $queryParams = [
            'codigoEmpresa' => intval($codigoEmpresa),
            'codigoLinha' => intval($codigoLinha),
        ];
        return json_decode(json_encode($this->execute($this->url . $this->versao . 'Posicao/Garagem', $queryParams)), false);
    } // Buscar os veículos em garagem de empresas específicas com base ou não na linha (opcional)

    public function buscarPrevisaoChegadaParadaLinha($codigoParada, $codigoLinha)
    {
        $queryParams = [
            'codigoParada' => intval($codigoParada),
            'codigoLinha' => intval($codigoLinha),
        ];
        return json_decode(json_encode($this->execute($this->url . $this->versao . 'Previsao', $queryParams)), false);
    } // Buscar a previsao de chegada de paradas específicas para linhas específicas de São Paulo

    public function buscarPrevisaoChegadaLinha($codigoLinha)
    {
        $queryParams = [
            'codigoLinha' => intval($codigoLinha),
        ];
        return json_decode(json_encode($this->execute($this->url . $this->versao . 'Previsao/Linha', $queryParams)), false);
    } // Buscar a previsao de chegada de linhas específicas em todas as paradas que ela abrange de São Paulo
    
    public function buscarPrevisaoChegadaParada($codigoParada)
    {
        $queryParams = [
            'codigoParada' => intval($codigoParada),
        ];
        return json_decode(json_encode($this->execute($this->url . $this->versao . 'Previsao/Parada', $queryParams)), false);
    } // Buscar a previsao de chegada de paradas específicas em todas as linhas que ela abrange de São Paulo
    
    public function buscarMapa($rota = '')
	{
        return $this->executeObterKMZ($rota);
	} // Busca o mapa geral, de corredores e de outras vias de SP (/Corredor, /OutrasVias)
}
