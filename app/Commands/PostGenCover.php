<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Libraries\ContentAIService;

/**
 * (Re)gera a imagem de capa de um ou mais posts existentes, usando o mesmo
 * pipeline da criacao automatica (gpt-image-1-mini -> variantes -> images).
 *
 * Uso:  php spark post:gen-cover 717 719
 */
class PostGenCover extends BaseCommand
{
    protected $group       = 'Content';
    protected $name        = 'post:gen-cover';
    protected $description = 'Regenera a capa (IA) de posts existentes pelo id.';
    protected $usage       = 'post:gen-cover [post_id ...]';

    public function run(array $params)
    {
        if (empty($params)) {
            CLI::error('Informe ao menos um id de post. Ex.: php spark post:gen-cover 717 719');
            return;
        }

        $service = new ContentAIService();

        foreach ($params as $raw) {
            $postId = (int) $raw;
            if ($postId <= 0) {
                CLI::write("Ignorando id invalido: {$raw}", 'yellow');
                continue;
            }

            CLI::write("Gerando capa do post {$postId} (gpt-image-1-mini, pode levar ~30-60s)...", 'yellow');
            try {
                $imageId = $service->regenerateCoverForPost($postId);
            } catch (\Throwable $e) {
                CLI::error("Post {$postId}: excecao - " . $e->getMessage());
                continue;
            }

            if (!empty($imageId)) {
                CLI::write("OK: post {$postId} -> image_id {$imageId}", 'green');
            } else {
                CLI::error("Falha ao gerar capa do post {$postId} (ver writable/logs).");
            }
        }
    }
}
