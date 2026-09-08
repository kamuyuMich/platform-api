<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContentItemResource\Pages;
use App\Models\Category;
use App\Models\ContentItem;
use App\Models\Tag;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class ContentItemResource extends Resource
{
    protected static ?string $model = ContentItem::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Content';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Content')
                ->columns(2)
                ->schema([
                    Forms\Components\Select::make('type')
                        ->options([
                            'article' => 'Article',
                            'research' => 'Research',
                            'project' => 'Project',
                            'tool' => 'Tool',
                            'resource' => 'Resource',
                            'service' => 'Consulting Service',
                        ])
                        ->required()
                        ->native(false),

                    Forms\Components\Select::make('status')
                        ->options([
                            'draft' => 'Draft',
                            'published' => 'Published',
                        ])
                        ->default('draft')
                        ->required()
                        ->native(false),

                    Forms\Components\TextInput::make('title')
                        ->required()
                        ->maxLength(255)
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn (string $state, Forms\Set $set) => $set('slug', Str::slug($state)))
                        ->columnSpan(2),

                    Forms\Components\TextInput::make('slug')
                        ->required()
                        ->maxLength(255)
                        ->unique(ignoreRecord: true)
                        ->columnSpan(2),

                    Forms\Components\Textarea::make('excerpt')
                        ->maxLength(500)
                        ->rows(2)
                        ->columnSpan(2),

                    Forms\Components\MarkdownEditor::make('body')
                        ->columnSpan(2),

                    Forms\Components\DateTimePicker::make('published_at')
                        ->native(false),

                    Forms\Components\TextInput::make('read_time_minutes')
                        ->numeric()
                        ->suffix('min'),
                ]),

            Forms\Components\Section::make('Organization')
                ->columns(2)
                ->schema([
                    Forms\Components\Select::make('category_id')
                        ->label('Category')
                        ->options(fn () => Category::query()->pluck('name', 'id'))
                        ->searchable()
                        ->native(false),

                    Forms\Components\Select::make('author_id')
                        ->relationship('author', 'name')
                        ->searchable()
                        ->native(false),

                    Forms\Components\Select::make('tags')
                        ->relationship('tags', 'name')
                        ->multiple()
                        ->preload()
                        ->searchable()
                        ->columnSpan(2),
                ]),

            Forms\Components\Section::make('Featured image')
                ->schema([
                    Forms\Components\SpatieMediaLibraryFileUpload::make('featured_image')
                        ->collection('featured_image')
                        ->image()
                        ->imageEditor(),
                ]),

            Forms\Components\Section::make('SEO')
                ->columns(2)
                ->collapsed()
                ->schema([
                    Forms\Components\TextInput::make('meta_title')->maxLength(255),
                    Forms\Components\TextInput::make('meta_description')->maxLength(255),
                    Forms\Components\TextInput::make('og_image')->maxLength(255)->columnSpan(2),
                ]),

            Forms\Components\Section::make('Type-specific details')
                ->description('Research: author_name, publication_date, key_findings, source_url. Project: problem, solution, technology, status. Tool: clinical_disclaimer, inputs. Service: price, duration, whats_included.')
                ->schema([
                    Forms\Components\KeyValue::make('extra')
                        ->keyLabel('Field')
                        ->valueLabel('Value')
                        ->reorderable(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->sortable(),

                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->limit(50),

                Tables\Columns\TextColumn::make('category.name')
                    ->label('Category')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state) => $state === 'published' ? 'success' : 'gray'),

                Tables\Columns\TextColumn::make('published_at')
                    ->dateTime('M j, Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('author.name')
                    ->label('Author'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'article' => 'Article',
                        'research' => 'Research',
                        'project' => 'Project',
                        'tool' => 'Tool',
                        'resource' => 'Resource',
                        'service' => 'Consulting Service',
                    ]),
                Tables\Filters\SelectFilter::make('status')
                    ->options(['draft' => 'Draft', 'published' => 'Published']),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContentItems::route('/'),
            'create' => Pages\CreateContentItem::route('/create'),
            'edit' => Pages\EditContentItem::route('/{record}/edit'),
        ];
    }
}
