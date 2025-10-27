<?php

namespace Modules\Utilities\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Utilities\Entities\StaticPage;
use Illuminate\Support\Str;

class StaticPageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $staticPages = [
            [
                'name' => 'Terms of Service',
                'slug' => 'terms-of-service',
                'description' => 'Terms and conditions for using our service',
                'body' => '<h1>Terms of Service</h1>

<h2>1. Acceptance of Terms</h2>
<p>By accessing and using this service, you accept and agree to be bound by the terms and provision of this agreement.</p>

<h2>2. Use License</h2>
<p>Permission is granted to temporarily download one copy of the materials on our website for personal, non-commercial transitory viewing only.</p>

<h2>3. Disclaimer</h2>
<p>The materials on our website are provided on an "as is" basis. We make no warranties, expressed or implied, and hereby disclaim and negate all other warranties including without limitation, implied warranties or conditions of merchantability, fitness for a particular purpose, or non-infringement of intellectual property or other violation of rights.</p>

<h2>4. Limitations</h2>
<p>In no event shall our company or its suppliers be liable for any damages (including, without limitation, damages for loss of data or profit, or due to business interruption) arising out of the use or inability to use the materials on our website, even if we or our authorized representative has been notified orally or in writing of the possibility of such damage.</p>

<h2>5. Accuracy of Materials</h2>
<p>The materials appearing on our website could include technical, typographical, or photographic errors. We do not warrant that any of the materials on its website are accurate, complete, or current.</p>

<h2>6. Links</h2>
<p>We have not reviewed all of the sites linked to our website and are not responsible for the contents of any such linked site. The inclusion of any link does not imply endorsement by us of the site.</p>

<h2>7. Modifications</h2>
<p>We may revise these terms of service for its website at any time without notice. By using this website, you are agreeing to be bound by the then current version of these terms of service.</p>

<h2>8. Governing Law</h2>
<p>These terms and conditions are governed by and construed in accordance with the laws and you irrevocably submit to the exclusive jurisdiction of the courts in that state or location.</p>',
                'status' => 'active',
            ],
            [
                'name' => 'Privacy Policy',
                'slug' => 'privacy-policy',
                'description' => 'Our privacy policy and data protection practices',
                'body' => '<h1>Privacy Policy</h1>

<h2>1. Information We Collect</h2>
<p>We collect information you provide directly to us, such as when you create an account, make a purchase, or contact us for support.</p>

<h2>2. How We Use Your Information</h2>
<p>We use the information we collect to provide, maintain, and improve our services, process transactions, and communicate with you.</p>

<h2>3. Information Sharing</h2>
<p>We do not sell, trade, or otherwise transfer your personal information to third parties without your consent, except as described in this policy.</p>

<h2>4. Data Security</h2>
<p>We implement appropriate security measures to protect your personal information against unauthorized access, alteration, disclosure, or destruction.</p>

<h2>5. Cookies</h2>
<p>We use cookies and similar technologies to enhance your experience on our website and to collect information about how you use our services.</p>

<h2>6. Third-Party Services</h2>
<p>We may use third-party services to help us provide and improve our services. These services may have access to your personal information.</p>

<h2>7. Data Retention</h2>
<p>We retain your personal information for as long as necessary to provide our services and fulfill the purposes outlined in this policy.</p>

<h2>8. Your Rights</h2>
<p>You have the right to access, update, or delete your personal information. You may also opt out of certain communications from us.</p>

<h2>9. Changes to This Policy</h2>
<p>We may update this privacy policy from time to time. We will notify you of any changes by posting the new policy on this page.</p>

<h2>10. Contact Us</h2>
<p>If you have any questions about this privacy policy, please contact us at privacy@example.com.</p>',
                'status' => 'active',
            ],
            [
                'name' => 'About Us',
                'slug' => 'about-us',
                'description' => 'Learn more about our company and mission',
                'body' => '<h1>About Us</h1>

<h2>Our Mission</h2>
<p>We are dedicated to providing innovative solutions that help businesses grow and succeed in the digital age. Our mission is to empower organizations with cutting-edge technology and exceptional service.</p>

<h2>Our Story</h2>
<p>Founded in 2020, we started as a small team of passionate developers with a vision to create software that makes a difference. Today, we serve thousands of customers worldwide with our comprehensive suite of business solutions.</p>

<h2>Our Values</h2>
<ul>
<li><strong>Innovation:</strong> We constantly push the boundaries of what\'s possible</li>
<li><strong>Quality:</strong> We deliver products and services that exceed expectations</li>
<li><strong>Integrity:</strong> We conduct business with honesty and transparency</li>
<li><strong>Customer Focus:</strong> Our customers\' success is our success</li>
<li><strong>Collaboration:</strong> We work together to achieve common goals</li>
</ul>

<h2>Our Team</h2>
<p>Our diverse team of professionals brings together expertise in technology, business, and customer service. We are united by our passion for excellence and our commitment to delivering value to our customers.</p>

<h2>Our Technology</h2>
<p>We leverage the latest technologies and best practices to build robust, scalable, and secure solutions. Our platform is designed to grow with your business and adapt to your changing needs.</p>

<h2>Our Commitment</h2>
<p>We are committed to continuous improvement and innovation. We listen to our customers, learn from their feedback, and evolve our products and services to meet their evolving needs.</p>

<h2>Contact Information</h2>
<p>For more information about our company, please contact us at info@example.com or visit our office at 123 Business Street, City, State 12345.</p>',
                'status' => 'active',
            ],
            [
                'name' => 'Contact Us',
                'slug' => 'contact-us',
                'description' => 'Get in touch with our team',
                'body' => '<h1>Contact Us</h1>

<h2>Get in Touch</h2>
<p>We\'d love to hear from you. Whether you have a question about our services, need technical support, or want to discuss a potential partnership, we\'re here to help.</p>

<h2>Contact Information</h2>
<div class="contact-info">
    <h3>Address</h3>
    <p>123 Business Street<br>
    Suite 100<br>
    City, State 12345<br>
    United States</p>

    <h3>Phone</h3>
    <p>+1 (555) 123-4567</p>

    <h3>Email</h3>
    <p>info@example.com</p>

    <h3>Business Hours</h3>
    <p>Monday - Friday: 9:00 AM - 6:00 PM (EST)<br>
    Saturday: 10:00 AM - 4:00 PM (EST)<br>
    Sunday: Closed</p>
</div>

<h2>Support</h2>
<p>For technical support, please email support@example.com or use our online support portal. Our support team is available 24/7 to assist you.</p>

<h2>Sales</h2>
<p>Interested in our services? Contact our sales team at sales@example.com or call +1 (555) 123-4567 to speak with a representative.</p>

<h2>Partnerships</h2>
<p>We welcome partnership opportunities. Please contact us at partnerships@example.com to discuss how we can work together.</p>

<h2>Media Inquiries</h2>
<p>For media inquiries and press releases, please contact our communications team at media@example.com.</p>

<h2>Feedback</h2>
<p>We value your feedback and suggestions. Please share your thoughts with us at feedback@example.com.</p>',
                'status' => 'active',
            ],
            [
                'name' => 'Help Center',
                'slug' => 'help-center',
                'description' => 'Find answers to common questions and get help',
                'body' => '<h1>Help Center</h1>

<h2>Welcome to Our Help Center</h2>
<p>Find answers to common questions, learn how to use our platform, and get the support you need.</p>

<h2>Getting Started</h2>
<h3>Account Setup</h3>
<ul>
<li>How to create an account</li>
<li>Setting up your profile</li>
<li>Configuring your preferences</li>
<li>Understanding your dashboard</li>
</ul>

<h3>Basic Features</h3>
<ul>
<li>Navigating the interface</li>
<li>Creating your first project</li>
<li>Managing your data</li>
<li>Understanding permissions</li>
</ul>

<h2>Common Questions</h2>
<h3>Account & Billing</h3>
<ul>
<li>How do I update my billing information?</li>
<li>What payment methods do you accept?</li>
<li>How do I cancel my subscription?</li>
<li>Can I change my plan?</li>
</ul>

<h3>Technical Support</h3>
<ul>
<li>System requirements</li>
<li>Browser compatibility</li>
<li>Mobile app usage</li>
<li>Troubleshooting common issues</li>
</ul>

<h2>User Guides</h2>
<h3>For Administrators</h3>
<ul>
<li>User management</li>
<li>System configuration</li>
<li>Security settings</li>
<li>Backup and recovery</li>
</ul>

<h3>For End Users</h3>
<ul>
<li>Daily operations</li>
<li>Reporting features</li>
<li>Collaboration tools</li>
<li>Mobile access</li>
</ul>

<h2>Video Tutorials</h2>
<p>Watch our step-by-step video tutorials to learn how to use our platform effectively.</p>

<h2>API Documentation</h2>
<p>For developers, we provide comprehensive API documentation to help you integrate with our platform.</p>

<h2>Still Need Help?</h2>
<p>If you can\'t find what you\'re looking for, our support team is here to help. Contact us at support@example.com or use our live chat feature.</p>',
                'status' => 'active',
            ],
            [
                'name' => 'FAQ',
                'slug' => 'faq',
                'description' => 'Frequently asked questions and answers',
                'body' => '<h1>Frequently Asked Questions</h1>

<h2>General Questions</h2>
<h3>What is your service?</h3>
<p>Our service is a comprehensive business management platform that helps organizations streamline their operations, manage their data, and improve their productivity.</p>

<h3>How much does it cost?</h3>
<p>We offer flexible pricing plans to suit different business needs. Please contact our sales team for detailed pricing information.</p>

<h3>Is there a free trial?</h3>
<p>Yes, we offer a 30-day free trial for new users. No credit card required to get started.</p>

<h2>Account & Billing</h2>
<h3>How do I create an account?</h3>
<p>Simply click the "Sign Up" button on our homepage and follow the registration process. You\'ll need to provide basic information and verify your email address.</p>

<h3>What payment methods do you accept?</h3>
<p>We accept all major credit cards (Visa, MasterCard, American Express), PayPal, and bank transfers for annual subscriptions.</p>

<h3>Can I change my plan?</h3>
<p>Yes, you can upgrade or downgrade your plan at any time. Changes will be reflected in your next billing cycle.</p>

<h2>Technical Questions</h2>
<h3>What are the system requirements?</h3>
<p>Our platform is web-based and works on all modern browsers. We recommend using the latest version of Chrome, Firefox, Safari, or Edge.</p>

<h3>Is my data secure?</h3>
<p>Yes, we take data security seriously. We use industry-standard encryption, regular security audits, and comply with major security frameworks.</p>

<h3>Do you offer API access?</h3>
<p>Yes, we provide comprehensive API access for developers. Please refer to our API documentation for details.</p>

<h2>Support</h2>
<h3>How do I get support?</h3>
<p>You can reach our support team via email at support@example.com, through our live chat feature, or by submitting a support ticket.</p>

<h3>What are your support hours?</h3>
<p>Our support team is available 24/7 for urgent issues. Standard support is available Monday through Friday, 9 AM to 6 PM EST.</p>

<h3>Do you offer training?</h3>
<p>Yes, we offer comprehensive training programs for new users and administrators. Contact us for more information.</p>

<h2>Data & Privacy</h2>
<h3>Where is my data stored?</h3>
<p>Your data is stored in secure, geographically distributed data centers with multiple backups and redundancy.</p>

<h3>Can I export my data?</h3>
<p>Yes, you can export your data at any time in various formats including CSV, JSON, and XML.</p>

<h3>What happens to my data if I cancel?</h3>
<p>We retain your data for 30 days after cancellation. You can request immediate deletion if needed.</p>',
                'status' => 'active',
            ],
        ];

        foreach ($staticPages as $pageData) {
            StaticPage::firstOrCreate(
                ['slug' => $pageData['slug']], 
                $pageData
            );
        }

        $this->command->info('Static pages seeded successfully!');
    }
}
