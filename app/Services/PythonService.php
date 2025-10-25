<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\UploadedFile;
use Exception;

/**
 * Python Service Integration
 * 
 * This service provides integration with the unified Python services
 * for PDF processing, email parsing, analysis, and rendering.
 */
class PythonService
{
    private $baseUrl;
    private $timeout;
    private $maxRetries;

    public function __construct()
    {
        $this->baseUrl = config('services.python.url', 'http://localhost:5000');
        $this->timeout = config('services.python.timeout', 120);
        $this->maxRetries = config('services.python.max_retries', 3);
    }

    /**
     * Check if Python service is available
     */
    public function isHealthy(): bool
    {
        try {
            $response = Http::timeout(10)->get($this->baseUrl . '/health');
            return $response->successful() && $response->json('status') === 'healthy';
        } catch (Exception $e) {
            Log::warning('Python service health check failed', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Get service status and information
     */
    public function getStatus(): array
    {
        try {
            $response = Http::timeout(10)->get($this->baseUrl);
            return $response->json();
        } catch (Exception $e) {
            return [
                'service' => 'Migration Manager Python Services',
                'status' => 'unavailable',
                'error' => $e->getMessage()
            ];
        }
    }

    // ============================================================================
    // PDF Service Methods
    // ============================================================================

    /**
     * Convert PDF pages to images
     */
    public function convertPdfToImages(UploadedFile $file, int $dpi = 150): array
    {
        try {
            $response = Http::timeout($this->timeout)
                ->attach('file', file_get_contents($file->getPathname()), $file->getClientOriginalName())
                ->post($this->baseUrl . '/pdf/convert-to-images', [
                    'dpi' => $dpi
                ]);

            if (!$response->successful()) {
                throw new Exception('PDF conversion failed: ' . $response->body());
            }

            return $response->json();
        } catch (Exception $e) {
            Log::error('PDF to images conversion failed', [
                'file' => $file->getClientOriginalName(),
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Merge multiple PDF files
     */
    public function mergePdfs(array $files): array
    {
        try {
            $multipart = [];
            foreach ($files as $index => $file) {
                $multipart[] = [
                    'name' => 'files',
                    'contents' => file_get_contents($file->getPathname()),
                    'filename' => $file->getClientOriginalName()
                ];
            }

            $response = Http::timeout($this->timeout)
                ->attach($multipart)
                ->post($this->baseUrl . '/pdf/merge');

            if (!$response->successful()) {
                throw new Exception('PDF merge failed: ' . $response->body());
            }

            return $response->json();
        } catch (Exception $e) {
            Log::error('PDF merge failed', [
                'file_count' => count($files),
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    // ============================================================================
    // Email Service Methods
    // ============================================================================

    /**
     * Parse .msg file and extract email data
     */
    public function parseEmail(UploadedFile $file): array
    {
        try {
            $response = Http::timeout($this->timeout)
                ->attach('file', file_get_contents($file->getPathname()), $file->getClientOriginalName())
                ->post($this->baseUrl . '/email/parse');

            if (!$response->successful()) {
                throw new Exception('Email parsing failed: ' . $response->body());
            }

            return $response->json();
        } catch (Exception $e) {
            Log::error('Email parsing failed', [
                'file' => $file->getClientOriginalName(),
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Analyze email content for categorization, priority, sentiment, etc.
     */
    public function analyzeEmail(array $emailData): array
    {
        try {
            $response = Http::timeout($this->timeout)
                ->post($this->baseUrl . '/email/analyze', $emailData);

            if (!$response->successful()) {
                throw new Exception('Email analysis failed: ' . $response->body());
            }

            return $response->json();
        } catch (Exception $e) {
            Log::error('Email analysis failed', [
                'subject' => $emailData['subject'] ?? 'Unknown',
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Render email with enhanced HTML and styling
     */
    public function renderEmail(array $emailData): array
    {
        try {
            $response = Http::timeout($this->timeout)
                ->post($this->baseUrl . '/email/render', $emailData);

            if (!$response->successful()) {
                throw new Exception('Email rendering failed: ' . $response->body());
            }

            return $response->json();
        } catch (Exception $e) {
            Log::error('Email rendering failed', [
                'subject' => $emailData['subject'] ?? 'Unknown',
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Complete email processing pipeline: parse + analyze + render
     */
    public function processEmail(UploadedFile $file): array
    {
        try {
            $response = Http::timeout($this->timeout * 2) // Longer timeout for full pipeline
                ->attach('file', file_get_contents($file->getPathname()), $file->getClientOriginalName())
                ->post($this->baseUrl . '/email/parse-analyze-render');

            if (!$response->successful()) {
                throw new Exception('Email processing failed: ' . $response->body());
            }

            return $response->json();
        } catch (Exception $e) {
            Log::error('Email processing pipeline failed', [
                'file' => $file->getClientOriginalName(),
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    // ============================================================================
    // Utility Methods
    // ============================================================================

    /**
     * Test the Python service with a simple request
     */
    public function testConnection(): array
    {
        try {
            $response = Http::timeout(10)->get($this->baseUrl . '/health');
            
            if ($response->successful()) {
                return [
                    'success' => true,
                    'status' => $response->json('status'),
                    'services' => $response->json('services', []),
                    'response_time' => $response->transferStats->getHandlerStat('total_time')
                ];
            } else {
                return [
                    'success' => false,
                    'error' => 'Service returned status: ' . $response->status(),
                    'body' => $response->body()
                ];
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get service configuration
     */
    public function getConfig(): array
    {
        return [
            'base_url' => $this->baseUrl,
            'timeout' => $this->timeout,
            'max_retries' => $this->maxRetries,
            'endpoints' => [
                'health' => $this->baseUrl . '/health',
                'pdf_convert' => $this->baseUrl . '/pdf/convert-to-images',
                'pdf_merge' => $this->baseUrl . '/pdf/merge',
                'email_parse' => $this->baseUrl . '/email/parse',
                'email_analyze' => $this->baseUrl . '/email/analyze',
                'email_render' => $this->baseUrl . '/email/render',
                'email_process' => $this->baseUrl . '/email/parse-analyze-render'
            ]
        ];
    }

    /**
     * Retry a request with exponential backoff
     */
    private function retryRequest(callable $request, int $maxRetries = null): mixed
    {
        $maxRetries = $maxRetries ?? $this->maxRetries;
        $attempt = 0;
        $lastException = null;

        while ($attempt < $maxRetries) {
            try {
                return $request();
            } catch (Exception $e) {
                $lastException = $e;
                $attempt++;
                
                if ($attempt < $maxRetries) {
                    $delay = pow(2, $attempt) * 1000; // Exponential backoff in milliseconds
                    usleep($delay * 1000); // Convert to microseconds
                    
                    Log::warning("Python service request failed, retrying", [
                        'attempt' => $attempt,
                        'max_retries' => $maxRetries,
                        'delay_ms' => $delay,
                        'error' => $e->getMessage()
                    ]);
                }
            }
        }

        throw $lastException;
    }
}
