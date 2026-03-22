<?php namespace App\Models;

class ContentRunModel extends BaseModel
{
    protected $table = 'content_runs';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'calendar_id',
        'run_type',
        'status',
        'error',
        'prompt',
        'response',
        'started_at',
        'finished_at',
    ];

    public function startRun(?int $calendarId, string $runType): int
    {
        $data = [
            'calendar_id' => $calendarId,
            'run_type' => $runType,
            'status' => 'running',
            'started_at' => date('Y-m-d H:i:s'),
        ];
        $this->builder()->insert($data);
        return (int) $this->db->insertID();
    }

    public function finishRun(int $id, string $status, ?string $error = null, ?string $prompt = null, ?string $response = null): bool
    {
        $data = [
            'status' => $status,
            'error' => $error,
            'prompt' => $prompt,
            'response' => $response,
            'finished_at' => date('Y-m-d H:i:s'),
        ];
        return (bool) $this->builder()->where('id', $id)->update($data);
    }

    public function markStaleRuns(int $minutes = 10): int
    {
        $minutes = max(1, $minutes);
        $cutoff = date('Y-m-d H:i:s', time() - ($minutes * 60));
        $data = [
            'status' => 'failed',
            'error' => 'stale run timeout',
            'finished_at' => date('Y-m-d H:i:s'),
        ];
        $this->builder()
            ->where('status', 'running')
            ->where('started_at <', $cutoff)
            ->update($data);
        return (int) $this->db->affectedRows();
    }
}
