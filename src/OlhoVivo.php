<?php

namespace RafaelDuarte;

use stdClass;
use Exception;
use InvalidArgumentException;

use Dotenv\Dotenv;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Message;
use GuzzleHttp\Exception\{ClientException, GuzzleException};
use function PHPUnit\Framework\fileExists;


Dotenv::createImmutable(dirname(__DIR__))->load();

class OlhoVivo extends Exception
{
    private const SP_TRANS_API_KEY = 'SP_TRANS_API_KEY';

    private const SP_TRANS_API_BASE_URL = 'SP_TRANS_API_BASE_URL';

    private const SP_TRANS_API_VERSION = 'SP_TRANS_API_VERSION';

    private Client $client;

    /**
     * @param bool $isAuthenticated
     * @param array $clientOptions
     * @param string $apiBaseUrl
     * @param string $apiToken
     * @param string $apiVersion
     *
     * @throws Exceptions | GuzzleException
     */
    public function __construct(
        public bool   $isAuthenticated = false,
        public array  $clientOptions = [],
        public string $apiBaseUrl = '',
        public string $apiToken = '',
        public string $apiVersion = '',
    )
    {
        parent::__construct();

        $this->setApiEnvironmentVariables();

        $this->checkApiEnvironmentVariables();

        $this->setClientOptions();

        $this->authenticate();
    }

    /**
     * Define as propriedades da api com base no arquivo env
     *
     * @return void
     *
     * @noinspection PhpUndefinedFunctionInspection
     */
    private function setApiEnvironmentVariables(): void
    {
        if (!fileExists(dirname(__DIR__) . '.env')) {
            $this->apiBaseUrl = getenv(self::SP_TRANS_API_BASE_URL);
            $this->apiVersion = getenv(self::SP_TRANS_API_VERSION);
            $this->apiToken = getenv(self::SP_TRANS_API_KEY);
            return;
        }

        if (function_exists('env')) {
            $this->apiBaseUrl = env(self::SP_TRANS_API_BASE_URL);
            $this->apiVersion = env(self::SP_TRANS_API_VERSION);
            $this->apiToken = env(self::SP_TRANS_API_KEY);
            return;
        }

        $this->apiBaseUrl = $_ENV[self::SP_TRANS_API_BASE_URL];
        $this->apiVersion = $_ENV[self::SP_TRANS_API_VERSION];
        $this->apiToken = $_ENV[self::SP_TRANS_API_KEY];

    }

    /**
     * Define as opções do Client
     *
     * @return void
     */
    private function setClientOptions(): void
    {
        $this->clientOptions = [
            'base_uri' => $this?->apiBaseUrl . $this?->apiVersion,
            'timeout' => 2.0,
            'cookies' => true,
            'decode_content' => false
        ];
    }

    /**
     * Verifica se todas as propriedades da api foram corretamente definidas
     *
     * @return void
     */
    private function checkApiEnvironmentVariables(): void
    {
        try {
            empty($this->apiToken) && throw new InvalidArgumentException;
            empty($this->apiBaseUrl) && throw new InvalidArgumentException;
            empty($this->apiVersion) && throw new InvalidArgumentException;
        } catch (InvalidArgumentException) {
            echo "Failed to read one or more environment variables from the .env file.";
            die;
        }
    }

    /**
     * Efetua a autenticação com a api
     *
     * @throws GuzzleException|Exceptions
     */
    protected function authenticate(): void
    {
        try {
            $this->client = new Client($this->clientOptions);

            $response = $this->client->post(
                'Login/Autenticar',
                ['query' => ['token' => $this->apiToken]]
            );

            $this->isAuthenticated = json_decode($response->getBody());

            $this->checkAuthentication();

        } catch (ClientException $e) {
            echo Message::toString($e->getRequest());
            die;
        }
    }

    /**
     * Verifica se o Client está autenticado com a api
     *
     * @throws Exceptions
     */
    private function checkAuthentication(): void
    {
        if (!$this->isAuthenticated) {
            throw new Exceptions('Unauthenticated');
        }
    }

    /**
     * Executa requisições do tipo GET
     *
     * @param string $endpoint O endpoint a ser consultado ex: 'Linha/Buscar'.
     * @param array $params ['param name' => 'param value'] Os query params a serem passados na consulta.
     *
     * @return array | stdClass
     */
    protected function executeGetRequest(string $endpoint, array $params = []): array|stdClass
    {
        $response = null;

        try {
            $response = $this->client->request('GET', $endpoint, ['query' => $params]);
        } catch (GuzzleException $e) {
            echo $e->getMessage();
        }

        return json_decode($response->getBody());
    }

    /**
     * Realiza uma busca das linhas do sistema por denominação ou número da linha
     *
     * - Se a linha não é encontrada então é realizada uma busca fonetizada na denominação das linhas.
     *
     * @param string|int $busLine Exemplo: 8000, Lapa ou Ramos
     *
     * @return stdClass|array
     *
     * @noinspection PhpUnused
     */
    public function getManyBusLines(string|int $busLine): stdClass|array
    {
        return $this->executeGetRequest('Linha/Buscar', ['termosBusca' => $busLine]);
    }

    /**
     * Realiza uma busca das linhas do sistema por denominação ou número da linha.
     *
     * - Se a linha não é encontrada então é realizada uma busca fonetizada na denominação das linhas.
     * A linha retornada será unicamente aquela cujo sentido de operação seja o informado no parâmetro sentido.
     *
     * @param string $busLine
     * @param int $lineDirection 1:Terminal Principal>> Terminal Secundário | 2:Terminal Secundário>> Terminal Principal
     *
     * @return array|stdClass|null
     *
     * @noinspection PhpUnused
     */
    public function getBusLinesByDirection(string $busLine, int $lineDirection): array|stdClass|null
    {
        $response = null;

        try {
            if ($lineDirection === 1 || $lineDirection === 2) {
                $response = $this->executeGetRequest(
                    'Linha/BuscarLinhaSentido',
                    ['termosBusca' => $busLine, 'sentido' => $lineDirection]
                );
            }
            throw new InvalidArgumentException;
        } catch (InvalidArgumentException) {
            echo 'Parameter lineDirection should be 1 or 2';
        }

        return $response;
    }

    /**
     * Realiza uma busca fonética das paradas de ônibus do sistema com base no parâmetro informado.
     *
     * - A consulta é realizada no nome da parada e também no seu endereço de localização.
     *
     * @param string $address Denominação ou número da linha 'total ou parcial'. Exemplo: 8000, Lapa ou Ramos
     *
     * @return array|stdClass
     *
     * @noinspection PhpUnused
     */
    public function getManyBusStopByAddress(string $address): array|stdClass
    {
        return $this->executeGetRequest('Parada/Buscar', ['termosBusca' => $address]);
    }

    /**
     * Realiza uma busca por todos os pontos de parada atendidos por uma determinada linha.
     *
     * - O Código identificador da linha é único de cada linha do sistema (por sentido)
     * e pode ser obtido através do método getManyBusLines()
     *
     * @param int $lineCode Código identificador da linha.
     *
     * @return array|stdClass
     *
     * @noinspection PhpUnused
     */
    public function getManyBusStopByLineCode(int $lineCode): array|stdClass
    {
        return $this->executeGetRequest(
            'Parada/BuscarParadasPorLinha',
            ['codigoLinha' => $lineCode]
        );
    }

    /**
     * Retorna a lista detalhada de todas as paradas que compõem um
     * determinado corredor com base no código do corredor.
     *
     * - Código identificador do corredor é um identificador único de cada corredor do sistema e pode
     * ser obtido através do método getBusLanes()
     *
     * @param int $laneCode
     *
     * @return array|stdClass
     *
     * @noinspection PhpUnused
     */
    public function getManyBusStopsByLaneCode(int $laneCode): array|stdClass
    {
        return $this->executeGetRequest(
            'Parada/BuscarParadasPorCorredor',
            ['codigoCorredor' => $laneCode]
        );
    }

    /**
     * Retorna uma lista com todos os corredores inteligentes
     *
     * @return array Retorna uma lista com todos os corredores inteligentes.
     *
     * @noinspection PhpUnused
     */
    public function getAllBusLanes(): array
    {
        return $this->executeGetRequest('Corredor');
    }

    /**
     * Retorna uma lista com todos as empresas operadoras relacionadas por área de operação
     *
     * @return stdClass
     *
     * @noinspection PhpUnused
     */
    public function getAllBusCompanies(): stdClass
    {
        return $this->executeGetRequest('Empresa');
    }

    /**
     * Retorna uma lista completa com a última localização de todos os
     * veículos mapeados com suas devidas posições lat / long
     *
     * @return array|stdClass
     *
     * @noinspection PhpUnused
     */
    public function getAllBusesPosition(): array|stdClass
    {
        return $this->executeGetRequest('Posicao');
    }

    /**
     * Retorna uma lista com todos os veículos de uma determinada linha
     * com suas devidas posições lat / long
     *
     * - Código identificador da linha. Este é um código identificador único de cada linha do
     * sistema (por sentido) e pode ser obtido através do método getManyBusLines()
     *
     * @param int $lineCode
     *
     * @return array|stdClass
     *
     * @noinspection PhpUnused
     */
    public function getAllBusesByLineCode(int $lineCode): array|stdClass
    {
        return $this->executeGetRequest(
            'Posicao/Linha',
            ['codigoLinha' => $lineCode]
        );
    }

    /**
     * Retorna uma lista completa de todos os veículos mapeados que estejam
     * transmitindo em uma garagem da empresa informada.
     *
     * - Código identificador da empresa. Este é um código identificador único que pode ser
     * obtido através do método getAllBusCompany()
     *
     * - Código identificador da linha. Este é um código identificador único de
     * cada linha do sistema (por sentido) e pode ser obtido através do método getManyBusLines()
     *
     * @param int $companyCode Código identificador da empresa
     * @param int $lineCode Código identificador da linha
     *
     * @return array|stdClass
     *
     * @noinspection PhpUnused
     */
    public function getManyBusesInGarageFromCompany(int $companyCode, int $lineCode): array|stdClass
    {
        return $this->executeGetRequest(
            'Posicao/Garagem',
            ['codigoEmpresa' => $companyCode, 'codigoLinha' => $lineCode]
        );
    }

    /**
     * Retorna uma lista com a previsão de chegada dos veículos da
     * linha informada que atende ao ponto de parada informado.
     *
     * - Código identificador da parada: Este é um código identificador único de
     * cada ponto de parada do sistema (por sentido) e pode ser
     * obtido através do método BUSCAR da categoria Paradas
     *
     * - Código identificador da parada. Este é um código identificador único de
     * cada ponto de parada do sistema (por sentido) e pode ser obtido através do
     * método BUSCAR da categoria Paradas
     *
     * - Código identificador da linha: Este é um código identificador
     * único de cada linha do sistema (por sentido)
     * e pode ser obtido através do método getManyBusLines()
     *
     * @param int $stopCode
     * @param int $lineCode Código identificador da linha
     *
     * @return array|stdClass
     *
     * @noinspection PhpUnused
     */
    public function getArrivalPredictionByLineAndStop(int $stopCode, int $lineCode): array|stdClass
    {
        return $this->executeGetRequest(
            'Previsao',
            ['codigoParada' => $stopCode, 'codigoLinha' => $lineCode]
        );
    }

    /**
     * Retorna uma lista com a previsão de chegada dos veículos de cada uma das linhas que
     * atendem ao ponto de parada informado.
     *
     * -Código identificador da parada. Este é um código identificador único de cada ponto
     * de parada do sistema (por sentido) e pode ser obtido através do método getBusS
     *
     * @param int $stopCode Código identificador de parada.
     *
     * @return array|stdClass
     *
     * @noinspection PhpUnused
     */
    public function getArrivalPredictionByStop(int $stopCode): array|stdClass
    {
        return $this->executeGetRequest(
            'Previsao',
            ['codigoParada' => $stopCode]
        );
    }
}
