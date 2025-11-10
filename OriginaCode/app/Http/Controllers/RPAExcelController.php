<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class RPAExcelController extends Controller
{
    /**
     * Display the upload form
     */
    public function index()
    {
        $files = $this->getUploadedFiles();
        return view('rpa.upload', compact('files'));
    }

    /**
     * Handle the file upload
     */
    public function upload(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'excel_file' => 'required|file|mimes:xlsx,xls,csv|max:10240', // 10MB max
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            if ($request->hasFile('excel_file')) {
                $file = $request->file('excel_file');
                $originalName = $file->getClientOriginalName();
                $timestamp = now()->format('Y-m-d_H-i-s');
                $filename = $timestamp . '_' . $originalName;
                
                // Store in public/RPA directory
                $path = $file->move(public_path('RPA'), $filename);
                
                return back()->with('success', 'File uploaded successfully: ' . $filename);
            }

            return back()->with('error', 'No file was uploaded.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error uploading file: ' . $e->getMessage());
        }
    }

    /**
     * Download an uploaded file
     */
    public function download($filename)
    {
        $path = public_path('RPA/' . $filename);
        
        if (!file_exists($path)) {
            abort(404, 'File not found');
        }

        return response()->download($path);
    }

    /**
     * Delete an uploaded file
     */
    public function delete($filename)
    {
        try {
            $path = public_path('RPA/' . $filename);
            
            if (file_exists($path)) {
                unlink($path);
                return back()->with('success', 'File deleted successfully.');
            }

            return back()->with('error', 'File not found.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting file: ' . $e->getMessage());
        }
    }

    /**
     * Get list of uploaded files
     */
    private function getUploadedFiles()
    {
        $directory = public_path('RPA');
        $files = [];

        if (is_dir($directory)) {
            $items = scandir($directory);
            foreach ($items as $item) {
                if ($item != '.' && $item != '..' && is_file($directory . '/' . $item)) {
                    $files[] = [
                        'name' => $item,
                        'size' => filesize($directory . '/' . $item),
                        'date' => date('Y-m-d H:i:s', filemtime($directory . '/' . $item)),
                    ];
                }
            }
        }

        return $files;
    }
}
