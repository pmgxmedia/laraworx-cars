<?php

namespace Database\Seeders;

use App\Models\Banner;
use App\Models\Faq;
use App\Models\Page;
use Illuminate\Database\Seeder;

class ContentSeeder extends Seeder
{
    public function run(): void
    {
        $pages = [
            'about' => [
                'title' => 'About WorxCars',
                'excerpt' => 'WorxCars is a premium South African vehicle marketplace where buyers connect with trusted sellers and can receive a financial reward after a qualifying purchase.',
                'content' => "<h2>Buy your car. Get paid.</h2><p>WorxCars is a South African vehicle marketplace and vehicle-sales campaign platform that connects buyers with verified vehicle dealerships and approved private sellers.</p><p>Each vehicle listing clearly displays vehicle information, images, pricing, seller details, transactional information, financing estimates, the WorxCars Buyer Reward payable to a successful buyer, and the terms and conditions that apply.</p><p>When a customer successfully purchases a qualifying vehicle through WorxCars and the transaction is verified, WorxCars receives its agreed commission from the dealership or seller. A predefined portion of that commission is allocated to the customer as a WorxCars Buyer Reward, subject to the platform's terms and successful transaction verification.</p><p>Rewards are subject to eligibility. A reward is not automatically payable merely because a user submits an enquiry.</p>",
            ],
            'terms' => [
                'title' => 'Terms and Conditions',
                'excerpt' => 'The terms on which WorxCars operates its marketplace.',
                'content' => "<h2>Terms and Conditions</h2><p>WorxCars is a marketplace that connects buyers with independent dealerships and approved private sellers. WorxCars is not the seller of the vehicles listed on the platform unless a specific listing states otherwise.</p><p>Vehicle details, pricing and availability are provided by the seller and may change without notice. All purchases are concluded directly between the buyer and the seller. Product loyalty, reward and commission arrangements are described on each eligible listing.</p><p>By using the platform you accept these terms, our Privacy Policy and the Buyer Reward Terms.</p>",
            ],
            'privacy' => [
                'title' => 'Privacy Policy',
                'excerpt' => 'How WorxCars collects and protects your personal information.',
                'content' => "<h2>Privacy Policy</h2><p>WorxCars processes personal information (including identity, contact and transaction information) to provide the marketplace service, verify purchases and pay eligible rewards. Information is processed in accordance with the Protection of Personal Information Act (POPIA).</p><p>We never store raw payment card details. Bank account information used for reward payouts is handled through secure, controlled processes.</p><p>Contact support@worxcars.co.za for any privacy request.</p>",
            ],
            'reward-terms' => [
                'title' => 'WorxCars Buyer Reward Terms',
                'excerpt' => 'The eligibility rules that apply to WorxCars Buyer Rewards.',
                'content' => "<h2>Buyer Reward Terms</h2><p><strong>The reward becomes eligible only when all of the following are satisfied:</strong></p><ol><li>The customer identifies with and qualifies for an active reward campaign.</li><li>The customer purchases the qualifying vehicle through the participating seller or dealership.</li><li>The transaction is verified by WorxCars.</li><li>The dealership or seller confirms the sale.</li><li>WorxCars confirms the transaction and approves the reward.</li><li>The payout process is completed.</li></ol><p>A reward is <strong>not</strong> payable merely for registering, viewing a vehicle, enquiring about a vehicle, saving a vehicle or contacting a dealership.</p><p>All rewards are subject to eligibility and compliance with these terms. WorxCars reserves the right to reject claims that are fraudulent, incomplete or otherwise non-compliant.</p>",
            ],
            'disclaimer' => [
                'title' => 'Disclaimer',
                'excerpt' => 'Important legal disclaimer.',
                'content' => "<h2>Disclaimer</h2><p>Vehicle information on the WorxCars platform is provided by the respective dealerships and private sellers. WorxCars does not guarantee the accuracy, availability or condition of any listed vehicle.</p><p>Finance estimates are indicative only and depend on approved financing. The WorxCars Buyer Reward is subject to eligibility, verification and the terms that apply to each campaign.</p><p>Nothing on this platform constitutes financial or legal advice.</p>",
            ],
            'faq' => [
                'title' => 'Frequently Asked Questions',
                'excerpt' => 'Answers to the questions buyers ask most.',
                'content' => '<h2>Frequently Asked Questions</h2><p>Browse the frequently asked questions below or contact support@worxcars.co.za.</p>',
            ],
            'contact' => [
                'title' => 'Contact WorxCars',
                'excerpt' => 'Get in touch with the WorxCars team.',
                'content' => '<h2>Contact Us</h2><p>Complete the enquiry form on this page and the WorxCars team will respond. For dealership partnerships, visit the Dealership Partnership page.</p>',
            ],
            'how-it-works' => [
                'title' => 'How WorxCars Works',
                'excerpt' => 'Browse, buy, claim and get paid.',
                'content' => "<h2>How WorxCars Works</h2><ul><li><strong>01 - Find Your Vehicle:</strong> Browse verified vehicles and campaigns.</li><li><strong>02 - Choose Your Reward:</strong> See the buyer reward associated with the vehicle.</li><li><strong>03 - Buy Your Vehicle:</strong> Complete the purchase with the participating dealership or seller.</li><li><strong>04 - Get Paid:</strong> Submit your purchase claim and receive your eligible WorxCars Buyer Reward after verification.</li></ul>",
            ],
        ];

        foreach ($pages as $slug => $data) {
            Page::firstOrCreate(
                ['slug' => $slug],
                [
                    'title' => $data['title'],
                    'excerpt' => $data['excerpt'],
                    'content' => $data['content'],
                    'meta_title' => $data['title'].' | WorxCars',
                    'meta_description' => $data['excerpt'],
                    'is_published' => true,
                    'published_at' => now(),
                ]
            );
        }

        $faqs = [
            ['When do I actually receive the Buyer Reward?', 'The reward is paid only after a successful qualifying purchase has been verified, the seller has confirmed the sale, WorxCars has approved the reward, and the payout process is completed.', 'reward'],
            ['Is the reward paid just for enquiring?', 'No. Registering, viewing, saving or enquiring about a vehicle does not make the reward payable. The full purchase and verification process must be completed.', 'reward'],
            ['Who can sell on WorxCars?', 'Verified dealerships and approved private sellers can list vehicles. All sellers must pass WorxCars verification.', 'sellers'],
            ['Does WorxCars own the vehicles?', 'No. Unless a specific listing states otherwise, WorxCars is a marketplace and the vehicle is owned by the dealership or private seller.', 'platform'],
            ['Can I finance the vehicle?', 'Most sellers offer finance assistance. Use the enquiry form to request a finance estimate for a specific vehicle.', 'buying'],
            ['What is the minimum Buyer Reward?', 'The minimum advertised reward is configurable by WorxCars administrators. Each eligible listing displays its own reward, terms and conditions.', 'reward'],
            ['What happens if my claim is rejected?', 'If a claim does not meet the eligibility rules, it may be rejected. WorxCars provides a reason and you can contact support if you believe there was a mistake.', 'claims'],
            ['How long does payout take?', 'Once a reward is approved, the payout process begins. Timelines depend on the verification and payout provider chosen by WorxCars.', 'claims'],
        ];

        foreach ($faqs as $i => [$question, $answer, $category]) {
            Faq::firstOrCreate(['question' => $question], [
                'answer' => $answer,
                'category' => $category,
                'sort_order' => $i,
                'is_published' => true,
            ]);
        }

        Banner::firstOrCreate(['position' => 'homepage', 'title' => 'Buy & Get Paid Campaign'], [
            'title' => 'Buy & Get Paid Campaign',
            'subtitle' => 'Select vehicles now carry an eligible WorxCars Buyer Reward. Buy your next car and get paid.',
            'link_url' => '/vehicles?sort=reward_desc',
            'position' => 'homepage',
            'sort_order' => 1,
            'is_active' => true,
        ]);
    }
}