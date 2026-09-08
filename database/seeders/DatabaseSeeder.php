<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\ContentItem;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            ['name' => 'Admin', 'password' => bcrypt('password'), 'role' => 'admin']
        );

        $categories = [
            ['name' => 'Interoperability', 'section' => 'digital_health'],
            ['name' => 'Electronic Medical Records', 'section' => 'digital_health'],
            ['name' => 'Health Information Systems', 'section' => 'digital_health'],
            ['name' => 'Clinical Decision Support', 'section' => 'ai'],
            ['name' => 'Generative AI', 'section' => 'ai'],
            ['name' => 'AI Ethics', 'section' => 'ai'],
            ['name' => 'Health Data Standards', 'section' => 'data'],
            ['name' => 'Digital Health in Africa', 'section' => 'africa'],
        ];

        foreach ($categories as $cat) {
            Category::firstOrCreate(['name' => $cat['name']], $cat);
        }

        $tags = ['FHIR', 'HL7', 'EMR', 'Kenya', 'Ethics', 'Machine Learning', 'Telemedicine'];
        foreach ($tags as $tag) {
            Tag::firstOrCreate(['name' => $tag]);
        }

        $interop = Category::where('name', 'Interoperability')->first();
        $genAi = Category::where('name', 'Generative AI')->first();
        $africa = Category::where('name', 'Digital Health in Africa')->first();

        $article = ContentItem::firstOrCreate(
            ['slug' => 'why-interoperability-is-essential-for-ai-in-healthcare'],
            [
                'type' => 'article',
                'title' => 'Why Interoperability Is Essential for AI in Healthcare',
                'excerpt' => 'AI models are only as good as the data they can access. Without interoperable systems, healthcare AI hits a ceiling fast.',
                'body' => "AI in healthcare depends on data that can move freely between systems...\n\n(full article body goes here)",
                'category_id' => $interop->id,
                'author_id' => $admin->id,
                'status' => 'published',
                'published_at' => now()->subDays(3),
                'read_time_minutes' => 6,
            ]
        );
        $article->tags()->syncWithoutDetaching(Tag::whereIn('name', ['FHIR', 'HL7'])->pluck('id'));

        $research = ContentItem::firstOrCreate(
            ['slug' => 'ai-readiness-of-healthcare-facilities-in-kenya'],
            [
                'type' => 'research',
                'title' => 'AI Readiness of Healthcare Facilities in Kenya',
                'excerpt' => 'A survey-based look at how prepared Kenyan health facilities are to adopt AI-driven clinical tools.',
                'body' => 'Full research write-up...',
                'category_id' => $africa->id,
                'author_id' => $admin->id,
                'status' => 'published',
                'published_at' => now()->subDays(10),
                'read_time_minutes' => 9,
                'extra' => [
                    'author_name' => 'Research Team',
                    'publication_date' => now()->subDays(10)->toDateString(),
                    'key_findings' => 'Most facilities lack the data infrastructure needed for AI adoption.',
                    'source_url' => null,
                ],
            ]
        );

        $project = ContentItem::firstOrCreate(
            ['slug' => 'ai-triage-assistant-prototype'],
            [
                'type' => 'project',
                'title' => 'AI Triage Assistant (Prototype)',
                'excerpt' => 'A prototype that structures patient-reported symptoms into a preliminary triage category.',
                'body' => 'Project write-up...',
                'category_id' => $genAi->id,
                'author_id' => $admin->id,
                'status' => 'published',
                'published_at' => now()->subDays(1),
                'extra' => [
                    'problem' => 'Long waits at first-contact triage in busy clinics.',
                    'solution' => 'A structured symptom intake form feeding a rule-based + ML triage suggestion.',
                    'technology' => 'Laravel API, Python scoring service, React intake form',
                    'status' => 'prototype',
                ],
            ]
        );

        $tool = ContentItem::firstOrCreate(
            ['slug' => 'bmi-calculator'],
            [
                'type' => 'tool',
                'title' => 'BMI Calculator',
                'excerpt' => 'Quick body mass index calculator with WHO category ranges.',
                'category_id' => $interop->id,
                'author_id' => $admin->id,
                'status' => 'published',
                'published_at' => now(),
                'extra' => [
                    'clinical_disclaimer' => 'Educational use only - not a substitute for clinical judgment.',
                    'inputs' => ['height_cm', 'weight_kg'],
                ],
            ]
        );

        $this->command->info('Seeded categories, tags, and sample content items (article, research, project, tool).');
    }
}
