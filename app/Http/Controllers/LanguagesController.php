<?php

namespace App\Http\Controllers;

use App\Http\Middleware\RedirectIfNotAdmin;
use App\Http\Middleware\RedirectIfNotParmitted;
use App\Models\EmailTemplate;
use App\Models\Language;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Inertia\Inertia;

class LanguagesController extends Controller {
    public function __construct(){
        $this->middleware(RedirectIfNotParmitted::class.':language');
    }

    public function index(){
        return Inertia::render('Languages/Index', [
            'title' => 'Languages',
            'filters' => Request::all('search'),
        ]);
    }

    public function create() {
        return Inertia::render('Languages/Create',[
            'title' => 'Add a new language',
        ]);
    }

    public function store() {
        $data = Request::validate([
            'name' => ['required', 'max:50'],
            'code' => ['required', 'max:3'],
        ]);
        File::put(lang_path($data['code'].'.json'), \json_encode([]));
        Language::create($data);

        return Redirect::route('languages')->with('success', 'Language created.');
    }

    public function delete($language){
        $language = Language::where('id', $language)->first();
        if(!empty($language)){
            if(File::exists(lang_path($language->code.'.json'))){
                File::delete(lang_path($language->code.'.json'));
            }
            $language->delete();
            return Redirect::back()->with('success', 'Language deleted!');
        }else{
            return Redirect::back()->with('error', 'Can not delete the language!');
        }
    }

    public function newItem(){
        $languageItems = Request::input('new_data');
        $origin = $languageItems['en'];
        foreach ($languageItems as $languageItemKey => $languageItemValue){
            $language_file = lang_path($languageItemKey . '.json');
            $decoded_file = json_decode(file_get_contents($language_file), true);
            $decoded_file[$origin] = $languageItemValue;
            file_put_contents($language_file, json_encode($decoded_file, JSON_UNESCAPED_UNICODE));
        }
        return Redirect::back()->with('success', 'Language data added!');
    }

    public function deleteItem($value){
        $languageItems = Language::get();
        foreach ($languageItems as $languageItem){
            $language_file = lang_path($languageItem->code . '.json');
            $decoded_file = json_decode(file_get_contents($language_file), true);
            unset($decoded_file[$value]);
            file_put_contents($language_file, json_encode($decoded_file, JSON_UNESCAPED_UNICODE));
        }
        return Redirect::back()->with('success', 'Language item has been deleted!');
    }

    public function edit(Language $language){
        $language_file = lang_path($language->code . '.json');
        $decoded_file = json_decode(file_get_contents($language_file), true);
        $languageData = [];
        foreach ($decoded_file as $dfk => $dfv){
            $languageData[] = ['name' => $dfk, 'value' => $dfv];
        }
        return Inertia::render('Languages/Edit', [
            'title' => $language->name,
            'languages' => Language::get(),
            'language_data' => [
                'id' => $language->id,
                'name' => $language->name,
                'code' => $language->code,
                'data' => $languageData,
            ],
        ]);
    }

    public function update(Language $language) {
        $languageData = Request::input('language_values');

        $decodedData = [];
        foreach ($languageData as $dataValue){
            $decodedData[$dataValue['name']] = $dataValue['value'];
        }

        $languagePath = lang_path($language->code . '.json');
        file_put_contents($languagePath, json_encode($decodedData, JSON_UNESCAPED_UNICODE));

        return Redirect::back()->with('success', 'Language data updated!');
    }

    public function newLanguageManually(Language $language){
        $language_file = lang_path($language->code . '.json');
        $decoded_file = json_decode(file_get_contents($language_file), true);
//        dd($decoded_file);
        $phpString = "Editar Perfil||Painel||Sair||Ingressos||Bater papo||perguntas frequentes||blog||Base de conhecimento||Mais||Notas||Contatos||Organizações||Usuários||Clientes||Configurações||Global||Categorias||Status||Prioridades||Departamentos||tipos||Modelos de e-mail||Correio SMTP||Empurrador||Pusher Chat||Primeiras páginas||Contato||Serviços||política de Privacidade||Termos de serviços||Filtro||Destruído||Lixo com||Apenas descartado||Procurar...||Reiniciar||Nome||E-mail||Telefone||País||Criar usuário||Primeiro nome||Sobrenome||Cidade||Endereço||Senha||Papel||foto||Novos Ingressos||Tíquetes Abertos||Ingressos Fechados||Bilhetes não atribuídos||Ticket por departamento||Bilhete por tipo||Criador de tickets principais||Histórico de ingressos||Primeiro tempo de resposta||Último tempo de resposta||Técnico||hardware||Desenvolvimento||Gerenciamento||Administrador||Programas||Serviço||Evento||Média||Segundos||este mês||mês passado||Mês||Meses||Dia||Dias||Horas||Hora||Minutos||Minuto||Chave||Assunto||Prioridade||Data||Atualizada||Cliente||Departamento||Atribuído a||Tipo de bilhete||Categoria||Criada||Pedir detalhes||Anexar arquivo||Excluir ingresso||Salvar||Discussão de ingressos||Os históricos de comentários para este ticket estarão disponíveis aqui.||Bilhete||Históricos de comentários||Histórico de comentários||Escreva um comentário e pressione enter para enviar...||Clique em uma conversa da esquerda para ver os históricos.||Digite uma mensagem...||Perguntas frequentes||Criar Ticket||Novo Bilhete||Criar perguntas frequentes||Filtrar por prioridade||Filtrar por estado||Filtrar por função||Excluir perguntas frequentes||Atualizar perguntas frequentes||Criar Base de Conhecimento||Título||Tipo||Detalhes||Excluir base de conhecimento||Atualizar base de conhecimento||Observação||Nenhum bilhete encontrado.||observe os detalhes aqui...||Enviar||Cancelar||Excluir nota||Criar contato||Organização||Excluir contato||Atualizar contato||Criar organização||Província||Estado||Código postal||Excluir organização||Atualizar organização||Excluir||Atualizar||Criar||Criar cliente||Gerenciar usuários||Deletar usuário||Atualizar usuário||Tem certeza de que deseja excluir este usuário?||Nome do aplicativo||Idioma padrão||Notificações por e-mail||Criar ticket por novo cliente||Criar ticket a partir do painel||Notificação para o primeiro comentário||O usuário foi atribuído a uma tarefa||Mudanças de status ou prioridade||Criar um novo usuário||Instrução de trabalho cron||Se você gostaria de enviar e-mail sem atrasos, você precisa definir um cron job para isso com uma vez por minuto.||Criar categoria||Crie um novo||status||Criar estado||lesma||Criar prioridade||Criar Departamento||Criar Tipo||Modelo de e-mail||E-mail Html||Modelo de atualização||Host SMTP||Porta SMTP||Nome de usuário SMTP||Senha SMTP||Criptografia de e-mail||A partir do endereço||De nome||ID do aplicativo pusher||Chave do aplicativo pusher||Segredo do aplicativo pusher||Cluster de aplicativo pusher||Localização||Número de telefone||Endereço de email||Endereço do local||Detalhes do e-mail||Detalhes do local||Mapa de localização||Adicionar novo||Ícone||Marcação||Privacidade||Informações da lista||Fundo||Lista de itens||Conteúdo da página||perguntas frequentes||Contate-nos||Link Útil||Empresa||Se inscrever||Conecte-se||Enviar ticket||Login Helpdesk||perguntas frequentes";
        $languageData = [];
        $inc = 0;
        $phpStringArr = explode('||',$phpString);
        foreach ($decoded_file as $dfk => $dfv){
//            print_r($dfk);
//            echo "<br>";
//            echo "<br>";
//            $languageData[] = ['name' => $dfk, 'value' => $dfv];
            $languageData[$dfk] = $phpStringArr[$inc];
            $inc+=1;
        }
        file_put_contents($language_file, json_encode($languageData, JSON_UNESCAPED_UNICODE));
        dd($languageData);
        exit;
        return Inertia::render('Languages/Edit', [
            'title' => $language->name,
            'languages' => Language::get(),
            'language_data' => [
                'id' => $language->id,
                'name' => $language->name,
                'code' => $language->code,
                'data' => $languageData,
            ],
        ]);
    }
}
