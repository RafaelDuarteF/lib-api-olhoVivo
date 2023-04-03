# Biblioteca para a API olho vivo
Biblioteca para consumir a API olho vivo da SP Trans.

Certifique-se de que você possui o composer instalado e tenha um token de aplicativo da SP Trans.

Instale a lib em seu projeto com o comando no composer abaixo: <br>
 ```php composer require rafaelduarte/olhovivo ```

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
        $linhas = $olhoVivo->buscarLinhas('Vila sabrina');
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


