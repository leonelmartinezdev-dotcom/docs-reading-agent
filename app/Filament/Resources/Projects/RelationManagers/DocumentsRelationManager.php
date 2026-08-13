<?php

namespace App\Filament\Resources\Projects\RelationManagers;

use App\DocumentStatus;
use App\Events\DocumentCreated;
use App\HasDocumentActions;
use App\Models\Document;
use App\Models\DocumentType;
use Filament\Actions\Action;
use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class DocumentsRelationManager extends RelationManager
{
    use HasDocumentActions;

    protected static string $relationship = 'documents';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Toggle::make('requires_analysis')
                    ->label('Requiere análisis'),

                TextInput::make('title')
                    ->label('Titulo')
                    ->placeholder('Titulo del documento'),

                Select::make('document_type_id')
                    ->label('Tipo de documento')
                    ->options(DocumentType::where('active', true)->pluck('label', 'id'))
                    ->searchable()
                    ->required(),

                FileUpload::make('document')
                    ->label('Documento')
                    ->disk('public')
                    ->directory('documents')
                    ->storeFileNamesIn('original_name'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('url')
            ->columns([

                TextColumn::make('title')
                    ->label('Titulo')
                    ->searchable(),

                TextColumn::make('original_name')
                    ->label('Nombre')
                    ->words(2)
                    ->searchable(),

                TextColumn::make('size')
                    ->label('Tamaño')
                    ->formatStateUsing(fn(int $state): string => number_format($state / 1024 / 1024, 2) . ' MB'),

                TextColumn::make('type')
                    ->label('Tipo')
                    ->badge()
                    ->formatStateUsing(fn($state) => $state->label)
                    ->color('info'),


                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->icon(fn(DocumentStatus $state) => $state->icon())
                    ->formatStateUsing(fn(DocumentStatus $state) => $state->label())
                    ->color(fn(DocumentStatus $state) => $state->color()),

            ])
            ->filters([
                //
            ])
            ->headerActions([
                $this->createAction(),
            ])
            ->recordActions([
                $this->editAction(),
                $this->viewAnalysisResult()
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    //DissociateBulkAction::make(),
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('id', 'desc');
    }

}
