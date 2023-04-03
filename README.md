# Biblioteca para a API olho vivo
Biblioteca para consumir a API olho vivo da SP Trans. <br>
Obtenha todas as informações sobre os ônibus de São Paulo através dessa API. LIB em PHP para consumi-lá facilmente. 

Certifique-se de que você possui o composer em seu projeto e tenha um token de aplicativo da SP Trans. <a href="https://www.sptrans.com.br/desenvolvedores/api-do-olho-vivo-guia-de-referencia/">Guia de referência da API</a>

Instale a lib em seu projeto com o comando no composer abaixo: <br>
 ```shell
 composer require rafaelduarte/olhovivo 
 ```

Importe no arquivo que deseja usar com: use RafaelDuarte\OlhoVivo\OlhoVivo;

# Exemplo de uso

 ```php
 
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use RafaelDuarte\OlhoVivo\OlhoVivo;

class UserController extends Controller
{
    public function index() {
        $olhoVivo = new OlhoVivo();
        $olhoVivo->token = 'seu_token';
        $olhoVivo->autenticar(); // Autenticar na API com seu token
        $linhas = $olhoVivo->buscarLinhas('Vila sabrina'); // Retorna uma array com as linhas resultantes de sua busca
        foreach ($linhas as $linha) {
            echo $linha->cl . '<br>';
            echo $linha->tp . '<br>';
            echo $linha->ts . '<br>';
            echo '<br>';
        } // Exibir as informações das linhas de SP

        // $olhoVivo->buscarParadas('Lapa'); // Buscar paradas específicas
        // $olhoVivo->buscarParadasPorLinha(1273); // Buscar paradas específicas por linhas específicas
        // $olhoVivo->buscarPosicaoTodosOnibus(); // Buscar posição de todos os ônibus em circulação
        // $olhoVivo->buscarPosicaoOnibusEspecifico(34705); // Buscar posicão de ônibus específico
        // $olhoVivo->buscarPrevisaoChegadaParadaLinha(4200953, 1989); // Buscar previsão chegada de uma parada específica e linha específica
        // $olhoVivo->buscarPrevisaoChegadaLinha(34705); // Buscar previsão chegada em todas as paradas uma a linha específica
        // $olhoVivo->buscarPrevisaoChegadaParada(4200953); // Buscar previsão de chegada de todas as linhas em uma parada específica
        
        // Acima, outras funções possíveis.
    }
}
 
 ```

# Documentação API

Para verificar os tipos de retorno e funcionamento da API, você pode acessar a documentação oficial da API <a href="https://www.sptrans.com.br/desenvolvedores/api-do-olho-vivo-guia-de-referencia/documentacao-api/">aqui.</a>
