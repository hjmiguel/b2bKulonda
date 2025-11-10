<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Imports\ProductsImport;
use Maatwebsite\Excel\Facades\Excel;

class RPAExcelController extends Controller
{
    public function index()
    {
        $files = $this->getUploadedFiles();
        $user = auth()->user();
        $shop = $user->shop ?? null;
        
        // Get RPA products count
        $productsCount = \App\Models\Product::where("user_id", $user->id)->count();
        
        return view("rpa.upload", compact("files", "user", "shop", "productsCount"));
    }

    public function upload(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "excel_file" => "required|file|mimes:xlsx,xls,csv|max:10240",
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            if ($request->hasFile("excel_file")) {
                $file = $request->file("excel_file");
                $originalName = $file->getClientOriginalName();
                $timestamp = now()->format("Y-m-d_H-i-s");
                $filename = $timestamp . "_" . $originalName;
                
                // Store file
                $path = $file->move(public_path("RPA"), $filename);
                
                $user = auth()->user();
                $shop = $user->shop;
                
                if (!$shop) {
                    return back()->with("error", "Você precisa ter uma loja para importar produtos.");
                }
                
                // Start time for performance tracking
                $startTime = microtime(true);
                
                // Import products
                $import = new ProductsImport($user->id, $shop->id);
                Excel::import($import, $path);
                
                $endTime = microtime(true);
                $duration = round($endTime - $startTime, 2);
                
                $importedCount = $import->getImportedCount();
                $skippedCount = $import->getSkippedCount();
                $errors = $import->getErrors();
                
                $message = "✅ Importação concluída com sucesso!\n\n";
                $message .= "📦 Produtos importados: {$importedCount}\n";
                
                if ($skippedCount > 0) {
                    $message .= "⏭️ Linhas vazias ignoradas: {$skippedCount}\n";
                }
                
                $message .= "⏱️ Tempo de processamento: {$duration} segundos\n";
                $message .= "👤 Fornecedor: {$user->name}\n";
                $message .= "🏪 Loja: {$shop->name}";
                
                if (!empty($errors)) {
                    $errorCount = count($errors);
                    $message .= "\n\n⚠️ {$errorCount} erro(s) encontrado(s):\n";
                    $message .= implode("\n", array_slice($errors, 0, 10));
                    
                    if ($errorCount > 10) {
                        $message .= "\n... e mais " . ($errorCount - 10) . " erros.";
                    }
                }
                
                return back()->with("success", $message);
            }

            return back()->with("error", "Nenhum arquivo foi enviado.");
        } catch (\Exception $e) {
            return back()->with("error", "Erro ao processar arquivo: " . $e->getMessage());
        }
    }

    public function download($filename)
    {
        $path = public_path("RPA/" . $filename);
        
        if (!file_exists($path)) {
            abort(404, "Arquivo não encontrado");
        }

        return response()->download($path);
    }

    public function delete($filename)
    {
        try {
            $path = public_path("RPA/" . $filename);
            
            if (file_exists($path)) {
                unlink($path);
                return back()->with("success", "Arquivo deletado com sucesso.");
            }

            return back()->with("error", "Arquivo não encontrado.");
        } catch (\Exception $e) {
            return back()->with("error", "Erro ao deletar arquivo: " . $e->getMessage());
        }
    }

    private function getUploadedFiles()
    {
        $directory = public_path("RPA");
        $files = [];

        if (is_dir($directory)) {
            $items = scandir($directory);
            foreach ($items as $item) {
                if ($item != "." && $item != ".." && is_file($directory . "/" . $item)) {
                    $files[] = [
                        "name" => $item,
                        "size" => filesize($directory . "/" . $item),
                        "date" => date("Y-m-d H:i:s", filemtime($directory . "/" . $item)),
                    ];
                }
            }
            
            // Sort by date descending
            usort($files, function($a, $b) {
                return strtotime($b["date"]) - strtotime($a["date"]);
            });
        }

        return $files;
    }
}
