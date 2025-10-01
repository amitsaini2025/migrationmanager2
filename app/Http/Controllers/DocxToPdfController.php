<?php

namespace App\Http\Controllers;

use App\Services\DocxToPdfService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocxToPdfController extends Controller
{
    protected $docxToPdfService;

    public function __construct(DocxToPdfService $docxToPdfService)
    {
        $this->docxToPdfService = $docxToPdfService;
    }

    /**
     * Show the conversion form
     */
    public function showForm()
    {
        $isHealthy = $this->docxToPdfService->isServiceHealthy();
        return view('docx-to-pdf.form', compact('isHealthy'));
    }

    /**
     * Convert uploaded DOCX file to PDF
     */
    public function convert(Request $request)
    {
        $request->validate([
            'docx_file' => 'required|file|mimes:docx,doc|max:51200', // 50MB max
        ]);

        try {
            // Store uploaded file temporarily
            $file = $request->file('docx_file');
            $tempPath = $file->store('temp', 'local');
            $fullPath = storage_path('app/' . $tempPath);

            // Convert to PDF
            $result = $this->docxToPdfService->convertDocxToPdf($fullPath);

            // Clean up temp file
            Storage::disk('local')->delete($tempPath);

            if (!$result['success']) {
                return back()->withErrors(['error' => $result['error']]);
            }

            return response()->download($result['pdf_file'], basename($result['pdf_file']))
                ->deleteFileAfterSend();

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Conversion failed: ' . $e->getMessage()]);
        }
    }

    /**
     * Check service health
     */
    public function health()
    {
        $isHealthy = $this->docxToPdfService->isServiceHealthy();
        return response()->json([
            'healthy' => $isHealthy,
            'service' => 'python-libreoffice-converter'
        ]);
    }

    /**
     * Test the conversion service
     */
    public function test()
    {
        $result = $this->docxToPdfService->testService();
        return response()->json($result);
    }

    /**
     * Convert existing file on server
     */
    public function convertFile(Request $request)
    {
        $request->validate([
            'file_path' => 'required|string|max:500',
        ]);

        $filePath = $request->input('file_path');
        
        if (!file_exists($filePath)) {
            return response()->json([
                'success' => false,
                'error' => 'File not found: ' . $filePath
            ], 404);
        }

        $result = $this->docxToPdfService->convertDocxToPdf($filePath);
        
        return response()->json($result);
    }

    /**
     * Convert and download existing file
     */
    public function convertAndDownload(Request $request)
    {
        $request->validate([
            'file_path' => 'required|string|max:500',
            'output_filename' => 'nullable|string|max:255',
        ]);

        $filePath = $request->input('file_path');
        $outputFilename = $request->input('output_filename');
        
        if (!file_exists($filePath)) {
            return response()->json([
                'success' => false,
                'error' => 'File not found: ' . $filePath
            ], 404);
        }

        $response = $this->docxToPdfService->convertAndDownload($filePath, $outputFilename);
        
        if (!$response) {
            return response()->json([
                'success' => false,
                'error' => 'Conversion failed'
            ], 500);
        }

        return $response;
    }
}
