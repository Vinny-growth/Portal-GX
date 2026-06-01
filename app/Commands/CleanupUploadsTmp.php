<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

/**
 * Removes temporary files left in uploads/tmp/ by AI image generation
 * (post covers, web stories, simulators). Anything older than the
 * configured age in hours is deleted.
 *
 * Usage:
 *   php spark uploads:cleanup-tmp           # default 24h
 *   php spark uploads:cleanup-tmp 6         # 6h
 */
class CleanupUploadsTmp extends BaseCommand
{
    protected $group = 'Uploads';
    protected $name = 'uploads:cleanup-tmp';
    protected $description = 'Remove arquivos antigos de uploads/tmp/ (default 24h).';
    protected $usage = 'uploads:cleanup-tmp [hours]';

    public function run(array $params)
    {
        $hours = isset($params[0]) ? max(1, (int) $params[0]) : 24;
        $tmpDir = FCPATH . 'uploads/tmp/';

        if (!is_dir($tmpDir)) {
            CLI::write('Diretório não existe: ' . $tmpDir, 'yellow');
            return;
        }

        $cutoff = time() - ($hours * 3600);
        $removed = 0;
        $kept = 0;
        $bytesFreed = 0;

        $iterator = new \DirectoryIterator($tmpDir);
        foreach ($iterator as $file) {
            if ($file->isDot() || !$file->isFile()) {
                continue;
            }
            if ($file->getMTime() < $cutoff) {
                $bytesFreed += $file->getSize();
                if (@unlink($file->getPathname())) {
                    $removed++;
                }
            } else {
                $kept++;
            }
        }

        CLI::write(sprintf(
            'Limpeza concluída — removidos: %d, mantidos: %d, espaço liberado: %s',
            $removed,
            $kept,
            $this->formatBytes($bytesFreed)
        ), 'green');
    }

    private function formatBytes($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return number_format($bytes, 2) . ' ' . $units[$i];
    }
}
