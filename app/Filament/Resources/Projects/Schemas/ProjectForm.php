<?php

namespace App\Filament\Resources\Projects\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('General')
                    ->description('')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nombre')
                            ->required()
                            ->columnSpanFull(),
                        MarkdownEditor::make('description')
                            ->label('Descripcion')
                            ->helperText('Descripcion del proyecto'),
                    ])
                    ->columnSpan(3),

                Section::make('Plazos')
                    ->description('')
                    ->schema([
                        DatePicker::make('start_at')
                            ->label('Fecha de inicio')
                            ->native(false),
                        DatePicker::make('end_at')
                            ->label('Fecha de finalizacion')
                            ->native(false),
                    ])
                    ->columnSpan(2)
            ])
            ->columns(5);
    }
}
