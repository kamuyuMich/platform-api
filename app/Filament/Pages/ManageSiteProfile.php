<?php

namespace App\Filament\Pages;

use App\Models\SiteProfile;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class ManageSiteProfile extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';

    protected static ?string $navigationLabel = 'Profile & About';

    protected static ?string $title = 'Doctor Profile';

    protected static string $view = 'filament.pages.manage-site-profile';

    public ?array $data = [];

    public function mount(): void
    {
        $profile = SiteProfile::current();

        $this->form->fill([
            ...$profile->toArray(),
            'photo' => $profile->getFirstMedia('photo')?->id,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->statePath('data')
            ->schema([
                Forms\Components\Section::make('Basic info')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('name')->required()->columnSpan(2),
                        Forms\Components\TextInput::make('credentials')
                            ->placeholder('e.g. MBChB, MPH'),
                        Forms\Components\TextInput::make('title')
                            ->placeholder('e.g. Physician & Digital Health Consultant'),
                        Forms\Components\TextInput::make('years_experience')->numeric(),
                        Forms\Components\TagsInput::make('specialties')
                            ->placeholder('Add a specialty and press Enter')
                            ->columnSpan(2),
                    ]),

                Forms\Components\Section::make('Photo')
                    ->schema([
                        Forms\Components\SpatieMediaLibraryFileUpload::make('photo')
                            ->collection('photo')
                            ->image()
                            ->imageEditor()
                            ->model(fn () => SiteProfile::current()),
                    ]),

                Forms\Components\Section::make('Bio')
                    ->schema([
                        Forms\Components\Textarea::make('short_bio')
                            ->rows(2)
                            ->helperText('Short version - used in cards and previews.'),
                        Forms\Components\MarkdownEditor::make('bio')
                            ->helperText('Full version - used on the About page.'),
                    ]),

                Forms\Components\Section::make('Contact & links')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('email')->email(),
                        Forms\Components\TextInput::make('phone'),
                        Forms\Components\TextInput::make('linkedin_url')->url(),
                        Forms\Components\TextInput::make('twitter_url')->url(),
                        Forms\Components\TextInput::make('youtube_url')->url(),
                        Forms\Components\TextInput::make('booking_url')
                            ->url()
                            ->helperText('Calendly or similar - powers the "Book a Consultation" button.'),
                    ]),
            ]);
    }

    public function save(): void
    {
        $data = $this->form->getState();
        unset($data['photo']); // media handled separately by the upload field itself

        SiteProfile::current()->update($data);

        Notification::make()
            ->title('Profile saved')
            ->success()
            ->send();
    }
}
