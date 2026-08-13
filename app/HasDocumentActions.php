<?php

namespace App;

use App\Events\DocumentCreated;
use App\Models\Document;
use App\Models\DocumentType;
use App\Services\DocumentService;
use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Storage;
use Filament\Schemas\Components\View;

trait HasDocumentActions
{
    public function createAction()
    {

        return Action::make('addDccument')
            ->label('Cargar Documento')
            ->modalWidth(Width::Large)
            ->form([

                Grid::make(2)
                    ->schema([

                        Toggle::make('requires_analysis')
                            ->label('Requiere análisis')
                            ->onIcon(Heroicon::Sparkles)
                            ->offIcon(Heroicon::Sparkles)
                            ->onColor('info')
                            ->offColor('gray')
                            ->live()
                            ->default(true)
                            ->columnSpan(1),

                        TextInput::make('title')
                            ->label('Titulo')
                            ->placeholder('Titulo del documento')
                            ->columnSpan(2),

                        Select::make('document_type_id')
                            ->label('Tipo de documento')
                            ->options(DocumentType::where('active', true)->pluck('label', 'id'))
                            ->searchable()
                            ->required()
                            ->columnSpan(2),

                        FileUpload::make('document')
                            ->label('Documento')
                            ->disk('public')
                            ->directory('documents')
                            ->storeFileNamesIn('original_name')
                            ->columnSpan(2),


                    ])
            ])
            ->action(function (array $data): void {

                $document = DocumentService::store($data, $this->getOwnerRecord());

                $message = $document->requires_analysis ? 'El documento fue enviado a analizar' : '';

                Notification::make()
                    ->title('Documento creado')
                    ->body($message)
                    ->icon('heroicon-o-document-text')
                    ->iconColor('success')
                    ->send();
            });
    }


    public function editAction()
    {
        return Action::make('edit')
            ->label('Editar')
            ->icon('heroicon-c-pencil-square')
            ->modalWidth(Width::Large)
            ->fillForm(function (Document $record) {
                return [
                    'requires_analysis' => $record->requires_analysis,
                    'title' => $record->title,
                    'document_type_id' => $record->document_type_id,
                    'document' => $record->url,
                    'original_name' => $record->original_name,
                    'approved' => $record->isApprobal,
                    'status' => $record->status
                ];
            })
            ->form([

                Grid::make(2)
                    ->schema([
                        Toggle::make('requires_analysis')
                            ->label('Requiere análisis')
                            ->onIcon(Heroicon::Sparkles)
                            ->offIcon(Heroicon::Sparkles)
                            ->onColor('info')
                            ->offColor('gray')
                            ->live()
                            ->inline(false)
                            ->afterStateUpdated(function ($state, Set $set) {
                                //$set('requires_analysis', !$state);
                            })
                            ->columnSpan(1),

                        Select::make('status')
                            ->label('Estado')
                            ->options(function ($state): array {
                                $options = DocumentStatus::options();

                                if ($state && !isset($options[$state])) {
                                    $options[$state] = DocumentStatus::getLabel($state);
                                }
                                return $options;
                            }),

                        TextInput::make('title')
                            ->label('Titulo')
                            ->placeholder('Titulo del documento')
                            ->columnSpan(2),

                        Select::make('document_type_id')
                            ->label('Tipo de documento')
                            ->options(DocumentType::where('active', true)->pluck('label', 'id'))
                            ->searchable()
                            ->required()
                            ->columnSpan(2),

                        FileUpload::make('document')
                            ->label('Documento')
                            ->disk('public')
                            ->directory('documents')
                            ->storeFileNamesIn('original_name')
                            ->required()
                            ->columnSpan(2),
                    ])
            ])
            ->action(function (Document $record, $data) {

                $document = DocumentService::edit($record, $data);

                Notification::make()
                    ->title('Cambios guardados')
                    ->icon('heroicon-o-document-text')
                    ->iconColor('success')
                    ->send();
            });
    }

    public function analyzeAction()
    {
        return Action::make('analyze-force')
            ->label('Analizar')
            ->color('info')
            ->icon('heroicon-c-sparkles')
            ->requiresConfirmation(true)
            ->modalHeading('Analizar Documento')
            ->modalDescription('Si envías el documento a analizar, el estado de aprobación puede cambiar según el resultado del análisis.')
            ->modalSubmitActionLabel('Confirmar')
            ->modalCancelActionLabel('Cancelar')
            ->action(function (Document $record) {

                DocumentService::forceAnalysis($record);

                Notification::make()
                    ->title('Documento eviado a analizar')
                    ->body('Seras notificado cuando el proceso finalice')
                    ->icon('heroicon-c-sparkles')
                    ->iconColor('info')
                    ->send();
            });
    }


    public function viewAnalysisResult()
    {

        return Action::make('viewAnalysisResult')
            ->label('Analisis')
            ->color('info')
            ->icon('heroicon-c-sparkles')
            //->disabled(fn(Document $record) => !$record->analysis()->count())
            ->fillForm(function (Document $record) {
                $lastAnalysis = $record->analysis()->latest()->first();

                if(!$lastAnalysis) return;

                $data = [
                    'date' => $lastAnalysis?->created_at,
                    'status_changed_by' => $record?->statusChangedBy?->name ?? "OpenAI",
                    'description' => $lastAnalysis?->description,
                    'approved' => $lastAnalysis?->approved,
                    'confidence' => $lastAnalysis?->confidence,
                    'errors' => $lastAnalysis?->errors,
                    'warnings' => $lastAnalysis?->warnings,
                ];
                //dd($data);
                return $data;
            })
            ->modalWidth(Width::ExtraLarge)
            ->modalHeading('Resultado del analisis')
            ->modalDescription('Aqui se muestra el ultimo resultados del analisis del documento')
            ->modalAlignment(Alignment::Center)
            ->modalIcon('heroicon-c-sparkles')
            ->schema([

                Grid::make(4)
                    ->schema([

                        TextInput::make('status_changed_by')
                            ->label('Analizado por')
                            ->disabled()
                            ->readOnly()
                            ->columnSpan(2),

                        DateTimePicker::make('date')
                            ->label('Fecha')
                            ->format('d/m/Y')
                            ->seconds(false)
                            ->columnSpan(2)
                            ->readOnly()
                            ->disabled(),

                        Textarea::make('description')
                            ->disabled()
                            ->readOnly()
                            ->columnSpan(4),

                        View::make('filament.components.analysis-error-list')
                            ->viewData(fn(Get $get) => [
                                'errors' => $get('errors'),
                                'type' => 'danger',
                                'title' => 'Errores encontrados',
                                'icon' => 'heroicon-o-x-circle'
                            ])
                            ->columnSpan(4),

                        View::make('filament.components.analysis-error-list')
                            ->viewData(fn(Get $get) => [
                                'errors' => $get('warnings'),
                                'type' => 'warning',
                                'title' => 'Advertencias',
                                'icon' => 'heroicon-o-exclamation-triangle'
                            ])
                            ->columnSpan(4),

                    ])
            ])
            ->modal()
            ->modalSubmitAction(false)
            ->modalCancelAction(false)
            ->extraModalFooterActions([
                $this->analyzeAction()
            ]);
    }
}
