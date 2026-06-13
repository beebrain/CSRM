<?php

namespace App\Libraries;

use App\Models\PaperModel;

class PlagiarismService
{
    /**
     * Simulate a plagiarism check via Turnitin/Copyleaks API.
     * Generates a random similarity score and updates the paper's plagiarism columns.
     *
     * @param int $paperId
     * @param string $filePath
     * @return array
     */
    public function checkSimilarity(int $paperId, string $filePath)
    {
        // Simulating API latency slightly
        usleep(100000); // 100ms

        // Random similarity percentage between 5% and 40%
        $similarity = rand(5, 40);
        
        // Define status: 'failed' if similarity is high (e.g., > 20%), else 'checked'
        // This threshold can be adjusted or managed by admins
        $status = 'checked';
        if ($similarity > 20) {
            $status = 'failed';
        }

        $reportUrl = 'https://copyleaks.com/mock-report/' . md5($paperId . time() . 'salt123');

        $paperModel = new PaperModel();
        $paperModel->update($paperId, [
            'plagiarism_status'     => $status,
            'similarity_percent'    => $similarity,
            'plagiarism_report_url' => $reportUrl
        ]);

        return [
            'status'             => $status,
            'similarity_percent' => $similarity,
            'report_url'         => $reportUrl
        ];
    }
}
