<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SalesPage;
use App\Models\User;

class SalesPageSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first(); // assume at least one user exists

        SalesPage::create([
            'user_id' => $user->id,
            'product_name' => 'Ultimate Marketing Course',
            'description' => 'Learn how to scale your business with proven marketing strategies.',
            'features' => [
                'Step-by-step video lessons',
                'Real-world case studies',
                'Downloadable resources'
            ],
            'target_audience' => 'Entrepreneurs and small business owners',
            'price' => 99.99,
            'usp' => 'Proven system used by 10,000+ students',
            'template' => 'modern',
            'cta_label' => 'Enroll Now',
            'cta_url' => 'https://example.com/enroll',
            'generated_content' => json_encode([
                "headline" => "Master Marketing Like a Pro",
                "subheadline" => "Boost your sales with strategies that actually work",
                "description" => "This course gives you everything you need to grow your business online.",
                "benefits" => [
                    "Increase your conversion rates",
                    "Understand your audience deeply",
                    "Build scalable campaigns"
                ],
                "features" => [
                    "10+ hours of content",
                    "Lifetime access",
                    "Expert instructors"
                ],
                "social_proof" => "Trusted by over 10,000 entrepreneurs worldwide",
                "pricing" => "$99.99 one-time payment",
                "cta" => "Join Now and Start Growing"
            ]),
        ]);

        SalesPage::create([
            'user_id' => $user->id,
            'product_name' => 'Fitness Transformation Program',
            'description' => 'A complete guide to achieving your dream body.',
            'features' => [
                'Custom workout plans',
                'Meal prep guides',
                'Weekly coaching calls'
            ],
            'target_audience' => 'People looking to lose weight and get fit',
            'price' => 49.99,
            'usp' => 'Personalized fitness plans that adapt to your progress',
            'template' => 'bold',
            'cta_label' => 'Start Today',
            'cta_url' => 'https://example.com/fitness',
            'generated_content' => json_encode([
                "headline" => "Transform Your Body in 90 Days",
                "subheadline" => "No gimmicks, just real results",
                "description" => "Our proven program helps you burn fat and build muscle efficiently.",
                "benefits" => [
                    "Lose weight faster",
                    "Gain confidence",
                    "Improve overall health"
                ],
                "features" => [
                    "Personalized plans",
                    "Expert coaching",
                    "Progress tracking tools"
                ],
                "social_proof" => "Thousands have already transformed their lives",
                "pricing" => "$49.99 limited offer",
                "cta" => "Get Started Now"
            ]),
        ]);

        SalesPage::create([
            'user_id' => $user->id,
            'product_name' => 'Luxury Skincare Set',
            'description' => 'Premium skincare products for radiant skin.',
            'features' => [
                'Natural ingredients',
                'Dermatologist tested',
                'Suitable for all skin types'
            ],
            'target_audience' => 'Women aged 25-50 interested in skincare',
            'price' => 79.99,
            'usp' => 'Clinically proven results in 2 weeks',
            'template' => 'elegant',
            'cta_label' => 'Shop Now',
            'cta_url' => 'https://example.com/skincare',
            'generated_content' => json_encode([
                "headline" => "Glow Like Never Before",
                "subheadline" => "Experience luxury skincare that works",
                "description" => "Our skincare set rejuvenates your skin and restores its natural glow.",
                "benefits" => [
                    "Reduce wrinkles",
                    "Hydrate deeply",
                    "Brighten skin tone"
                ],
                "features" => [
                    "All-natural formula",
                    "Luxury packaging",
                    "Visible results in weeks"
                ],
                "social_proof" => "Loved by thousands of happy customers",
                "pricing" => "$79.99 per set",
                "cta" => "Buy Now"
            ]),
        ]);
    }
}
