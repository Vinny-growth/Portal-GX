<?php

namespace App\Controllers;

use App\Models\SimLeadModel;
use CodeIgniter\API\ResponseTrait;

class ApiController extends BaseController
{
    use ResponseTrait;
    
    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        // Desabilitar verificação CSRF para essa classe inteira
        if (isset($this->request)) {
            $this->request->getPost(); // Força a inicialização da coleção POST
            $this->request->isValidCSRF = true; // Define que a validação CSRF foi aprovada
        }
    }

    /**
     * Endpoint aberto para receber leads do simulador
     * Pode ser acessado via POST sem token CSRF
     */
    public function saveSimulatorLead()
    {
        // Log para depuração
        log_message('info', 'API: Requisição recebida em saveSimulatorLead');
        
        // Permitir CORS
        $this->response->setHeader('Access-Control-Allow-Origin', '*');
        $this->response->setHeader('Access-Control-Allow-Methods', 'POST, OPTIONS');
        $this->response->setHeader('Access-Control-Allow-Headers', 'Content-Type, X-Requested-With');
        
        // Se for uma requisição OPTIONS (preflight), retornar 200 OK
        if ($this->request->getMethod() === 'options') {
            return $this->response->setStatusCode(200);
        }
        
        // Obter dados do corpo da requisição
        $name = $this->request->getPost('name');
        $email = $this->request->getPost('email');
        $phone = $this->request->getPost('phone');
        $simData = $this->request->getPost('sim_data');
        $observations = $this->request->getPost('observations');
        
        // Log dos dados recebidos
        log_message('info', 'API: Dados recebidos: ' . json_encode([
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'has_simdata' => !empty($simData),
            'observations' => $observations
        ]));
        
        // Validação básica
        if (empty($name) || empty($email) || empty($phone)) {
            log_message('error', 'API: Dados inválidos - Campos obrigatórios não preenchidos');
            return $this->respond([
                'status' => 'error',
                'message' => 'Os campos nome, email e telefone são obrigatórios'
            ], 400);
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            log_message('error', 'API: Email inválido - ' . $email);
            return $this->respond([
                'status' => 'error',
                'message' => 'O email informado é inválido'
            ], 400);
        }
        
        // Preparar dados para salvar
        $data = [
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'sim_data' => $simData,
            'observations' => $observations
        ];
        
        try {
            // Salvar no banco de dados
            $model = new SimLeadModel();
            $result = $model->addSimLead($data);
            
            if ($result) {
                log_message('info', 'API: Lead salvo com sucesso - ' . $email);
                
                // Retornar resposta JSON formatada corretamente
                $this->response->setHeader('Content-Type', 'application/json');
                $this->response->setStatusCode(200);
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Lead salvo com sucesso'
                ]);
            } else {
                log_message('error', 'API: Erro ao salvar lead - ' . $email);
                
                // Retornar resposta JSON formatada corretamente
                $this->response->setHeader('Content-Type', 'application/json');
                $this->response->setStatusCode(500);
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Erro ao salvar o lead no banco de dados'
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'API: Exceção ao salvar lead - ' . $e->getMessage());
            
            // Retornar resposta JSON formatada corretamente
            $this->response->setHeader('Content-Type', 'application/json');
            $this->response->setStatusCode(500);
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Erro interno do servidor: ' . $e->getMessage()
            ]);
        }
    }
}