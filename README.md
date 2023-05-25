[![Testes](https://img.shields.io/badge/Testes-passing-brightgreen)](https://github.com/r3c4-d3v/lib-api-olhoVivo/actions/workflows/test.yml)

## Biblioteca para a API olho vivo

Biblioteca para consumir a API olho vivo da SP Trans. <br>
Obtenha todas as informações sobre os ônibus de São Paulo através dessa API. LIB em PHP para consumi-lá facilmente.

Certifique-se de que você possui o composer em seu projeto e tenha um token de aplicativo da SP
Trans. 

 ```shell
 composer require rafaelduarte/olhovivo 
 ```

Você pode registrar sua chave de api no site da [SpTrans](https://www.sptrans.com.br/desenvolvedores/)

Com sua chave em mãos adicione as seguintes chaves em seu arquivo .env

```dotenv
SP_TRANS_API_KEY=SUA_CHAVE
SP_TRANS_API_ENDPOINT=http://api.olhovivo.sptrans.com.br/
SP_TRANS_API_VERSION=v2.1/
```

Importe no arquivo que deseja usar com: use RafaelDuarte\OlhoVivo\OlhoVivo;

### Exemplo de uso

 ```php
 
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use RafaelDuarte\SpTrans\OlhoVivo\Classes;

class UserController extends Controller
{
    public function index() {
        # Instanciar um novo client
        $olhoVivo = new OlhoVivo();
        
        # Realiza uma busca das linhas do sistema por denominação ou número da linha
        $olhoVivo->getManyBusLines();
        
        # Realiza uma busca das linhas do sistema por denominação ou número da linha.
        $olhoVivo->getBusLinesByDirection();
        
        # Realiza uma busca fonética das paradas de ônibus do sistema com base no parâmetro informado.
        $olhoVivo->getManyBusStopByAddress();
        
        # Realiza uma busca por todos os pontos de parada atendidos por uma determinada linha.
        $olhoVivo->getManyBusStopByLineCode();
        
        # Retorna a lista detalhada de todas as paradas que compõem um determinado corredor com base no código do corredor.
        $olhoVivo->getManyBusStopsByLane();
        
        # Retorna uma lista com todos os corredores inteligentes
        $olhoVivo->getAllBusLanes();
        
        # Retorna uma lista com todos as empresas operadoras relacionadas por área de operação
        $olhoVivo->getAllBusCompanies();
        
        #Retorna uma lista completa com a última localização de todos os veículos mapeados com suas devidas posições lat / long
        $olhoVivo->getAllBusesPosition();
        
        # Retorna uma lista com todos os veículos de uma determinada linha com suas devidas posições lat / long
        $olhoVivo->getAllBusesByLineCode();
        
        # Retorna uma lista completa de todos os veículos mapeados que estejam transmitindo em uma garagem da empresa informada.
        $olhoVivo->getManyBusesInGarageFromCompany();
        
        # Retorna uma lista com a previsão de chegada dos veículos da linha informada que atende ao ponto de parada informado.
        $olhoVivo->getArrivalPredictionByLineAndStop();
        
        # Retorna uma lista com a previsão de chegada dos veículos de cada uma das linhas que atendem ao ponto de parada informado.
        $olhoVivo->getArrivalPredictionByStop();
        
        # Retorna o mapa completo da cidade.
        $olhoVivo->getKmzMapFile();
    }
}
 
 ```

# Pré-requisitos para contribuir

Certifique-se de ter o Docker e o Docker Compose instalados em seu sistema. <br>
Se ainda não os tiver, você pode instalá-los seguindo as instruções oficiais:
Docker: [Instalação do Docker](https://docs.docker.com/get-docker/) <br>
Docker Compose: [Instalação do Docker Compose](https://docs.docker.com/compose/install/)

### Passos

1. Clone o repositório do projeto para o seu ambiente local.
2. Navegue até o diretório raiz do projeto.

3. No terminal, execute o seguinte comando para construir as imagens do Docker e iniciar os containers:

```shell
docker-compose up --build 
```
Isso irá construir as imagens e iniciar os containers com base nas configurações definidas no arquivo
docker-compose.yml.

- O código fonte do projeto está vinculado ao contêiner e será sincronizado automaticamente. <br>
Isso significa que qualquer alteração feita nos arquivos locais será refletida no contêiner em tempo real.

### Parar execução do container
Para parar a execução do projeto, você pode pressionar `Ctrl + C` no terminal onde o `docker-compose up` foi executado
ou executar o seguinte comando no diretório raiz do projeto:

```shell
docker-compose down
```

Com essas instruções, você poderá executar o projeto utilizando o Docker e o Docker Compose, garantindo um ambiente
isolado e facilitando o processo de desenvolvimento e execução.

### Documentação API

Para verificar os tipos de retorno e funcionamento da API, você pode acessar a documentação oficial da
API da OlhoVivo <a href="https://www.sptrans.com.br/desenvolvedores/api-do-olho-vivo-guia-de-referencia/documentacao-api/">aqui.</a>


