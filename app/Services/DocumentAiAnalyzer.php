<?php

namespace App\Services;

use App\Models\Document;
use Exception;
use OpenAI\Laravel\Facades\OpenAI;

class DocumentAiAnalyzer
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function analyze(Document $document, string $content)
    {
        $basePrompt = $document->type->prompt;


        if (empty($basePrompt)) {
            throw new Exception("Prompt empty");
        }
        //$basePrompt = file_get_contents(dirname(__DIR__, 2) . '/prompt.md');
        $prompt = $basePrompt . " " . $content;


        $response = OpenAI::responses()->create([
            'model' => 'gpt-5.4-mini',
            'input' => $prompt,
        ]);

        //$response = "{\n  \"approved\": true,\n  \"description\": \"Recibo de sueldo de Juan Pérez correspondiente a julio de 2026\",\n  \"confidence\": 0.98,\n  \"documentType\": \"salary_receipt\",\n  \"errors\": [],\n  \"warnings\": [\n    \"El documento indica que es de prueba y no tiene validez legal.\",\n    \"Se detecta una línea no válida en el concepto de jubilación, aunque el total de descuentos parece coherente.\"\n  ],\n  \"missingFields\": [],\n  \"detectedFields\": {\n    \"employer\": {\n      \"name\": \"TECNOSOFT SOLUCIONES S.A.\",\n      \"cuit\": \"30-00000000-0\",\n      \"address\": \"Av. Ficticia 1234, Rosario, Santa Fe\"\n    },\n    \"employee\": {\n      \"fullName\": \"Pérez, Juan\",\n      \"cuil\": \"20-00000000-0\",\n      \"legajo\": \"0001\",\n      \"category\": \"Analista Senior\",\n      \"position\": \"Full Stack Developer\",\n      \"convenio\": \"Fuera de convenio\",\n      \"dateOfEntry\": \"01/03/2021\"\n    },\n    \"liquidationPeriod\": \"Julio 2026\",\n    \"receiptNumber\": \"000-00001234\",\n    \"earningsTotal\": 2070000,\n    \"discountsTotal\": 355300,\n    \"netAmount\": 1714700,\n    \"concepts\": [\n      {\n        \"name\": \"Sueldo Básico\",\n        \"type\": \"Remunerativo\",\n        \"amount\": 1800000\n      },\n      {\n        \"name\": \"Presentismo\",\n        \"type\": \"Remunerativo\",\n        \"amount\": 180000\n      },\n      {\n        \"name\": \"Antigüedad (5 años)\",\n        \"type\": \"Remunerativo\",\n        \"amount\": 90000\n      },\n      {\n        \"name\": \"Jubilación (11%)\",\n        \"type\": \"Descuento\",\n        \"amount\": 229900\n      },\n      {\n        \"name\": \"Ley 19.032 - PAMI (3%)\",\n        \"type\": \"Descuento\",\n        \"amount\": 62700\n      },\n      {\n        \"name\": \"Obra Social (3%)\",\n        \"type\": \"Descuento\",\n        \"amount\": 62700\n      }\n    ]\n  }\n}";
        //return json_decode($response);

        return json_decode($response->outputText);
    }
}
