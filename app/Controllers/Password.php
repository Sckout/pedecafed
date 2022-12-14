<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Password extends BaseController {

    private $usuarioModel;

    public function __construct() {

        $this->usuarioModel = new \App\Models\UsuarioModel();
    }

    public function esqueci() {

        $data = [
            'titulo' => 'Esqueci da minha senha',
        ];
        return view('Password/esqueci', $data);
    }

    public function processaEsqueci() {
        if ($this->request->getMethod() === 'post') {
            $usuario = $this->usuarioModel->buscaUsuarioPorEmail($this->request->getPost('email'));
            if ($usuario === null || !$usuario->ativo) {
                return redirect()->to(site_url('password/esqueci'))
                                ->back()
                                ->with('atencao', 'Este email ainda não está cadastrado')
                                ->withInput();
            }

            $usuario->iniciaPasswordReset();

            $this->usuarioModel->save($usuario);

            $this->enviaEmailRedefinicaoSenha($usuario);

            return redirect()->to(site_url('login'))->with('sucesso', 'E-mail de redefinição de senha enviado para sua caixa de entrada');
        } else {
            //nao e post

            return redirect()->back();
        }
    }

    public function reset($token = null) {

        if ($token === null) {
            return redirect()->to(site_url('password/esqueci'))->with('atencao', 'Link inválido ou expirado');
        }

        $usuario = $this->usuarioModel->buscaUsuarioParaResetarSenha($token);

        if ($usuario != null) {
            $data = [
                'titulo' => 'Redefina a sua senha',
                'token' => $token,
            ];
            return view('Password/reset', $data);
        } else {
            return redirect()->to(site_url('password/esqueci'))->with('atencao', 'Link inválido ou expirado');
        }
    }

    public function processaReset($token = null) {
        if ($token === null) {
            return redirect()->to(site_url('password/esqueci'))->with('atencao', 'Link inválido ou expirado');
        }

        $usuario = $this->usuarioModel->buscaUsuarioParaResetarSenha($token);

        if ($usuario != null) {

            $usuario->fill($this->request->getPost());

            if ($this->usuarioModel->save($usuario)) {
                //setamos as colunas resethash e resetexpiraem como null ao invocar o metodo abaixo que fdoi definido na entidade usuario e invalidamos o link antigo
                $usuario->completaPasswordReset();
                // atualizamos novamente o usuario com os novos valores definidos acima
                $this->usuarioModel->save($usuario);
                
                return redirect()->to(site_url("login"))->with('sucesso', 'Nova senha cadastrada com sucesso!');
                
            } else {
                return redirect()->to(site_url("password/reset/$token"))
                                ->with('errors_model', $this->usuarioModel->errors())
                                ->with('atencao', 'Dados inválidos, verifique os erros abaixo')
                                ->withInput();
            }
        }
    }

    private function enviaEmailRedefinicaoSenha(object $usuario) {

        $email = service('email');

        $email->setFrom('no-reply@pedecafe.com.br', 'PedeCafe');
        $email->setTo($usuario->email);

        $email->setSubject('Redefinição de Senha');
        $mensagem = view('Password/reset_email', ['token' => $usuario->reset_token]); // armazene na variavel mensagem a view reset email passando o valor de token

        $email->setMessage($mensagem);

        $email->send();
    }

}
