# Biblioteca para a API olho vivo
Biblioteca para consumir a API olho vivo da SP Trans.

Certifique-se de que você possui o composer instalado e tenha um token de aplicativo da SP Trans.

Instale a lib em seu projeto com o comando: <b>composer require rafaelduarte/olhovivo</b>

Importe no arquivo que deseja usar com: use RafaelDuarte\OlhoVivo\OlhoVivo;

 ```php
 
 
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use RafaelDuarte\OlhoVivo\OlhoVivo;

class UserController extends Controller
{
    public function index() {
        $olhoVivo = new OlhoVivo();
        $olhoVivo->token = 'a45aaa502f6b721b5959c713896a9aa27b98a615a46d98a9f875be516732f090';
        $olhoVivo->autenticar();
        $linhas = $olhoVivo->buscarLinhas('Vila sabrina');
        foreach ($linhas as $linha) {
            echo $linha->cl . '<br>';
            echo $linha->tp . '<br>';
            echo $linha->ts . '<br>';
            echo '<br>';
        }

        // $olhoVivo->buscarParadas('Lapa');
        // $olhoVivo->buscarParadasPorLinha(1273);
        // $olhoVivo->buscarPosicaoOnibusEspecifico(34705);
        // $olhoVivo->buscarPrevisaoChegadaParadaLinha(4200953, 1989);
        // $olhoVivo->buscarPrevisaoChegadaLinha(34705); 4200953
        // $olhoVivo->buscarPrevisaoChegadaParada(4200953);
    }
}
 
 ```


