<?php

namespace App\Filament\Resources\DocumentTypes;

use App\Filament\Resources\DocumentTypes\Pages\ManageDocumentTypes;
use App\Models\DocumentType;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DocumentTypeResource extends Resource
{
    protected static ?string $model = DocumentType::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSwatch;

    protected static ?string $modelLabel = 'Tipo de Documento';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('label')
                    ->label('Nombre')
                    ->columnSpan(2),
                ColorPicker::make('color')
                    ->label('Color')
                    ->columnSpan(1),
                TextInput::make('description')
                    ->label('Descripcion')
                    ->columnSpanFull(),
                MarkdownEditor::make('prompt')
                    ->label('Prompt')
                    ->columnSpanFull()
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('label')
                    ->label('Nombre'),
                TextColumn::make('description')
                    ->label('Descripcion'),
                ColorColumn::make('color')
                    ->label('Color')
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageDocumentTypes::route('/'),
        ];
    }
}
