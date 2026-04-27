<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImportSampleController extends Controller
{
    /**
     * Sample data for each entity type
     */
    protected array $sampleData = [
        'brands' => [
            'headers' => ['name', 'code', 'email', 'phone', 'website', 'description', 'status'],
            'rows' => [
                ['Acme Corporation', 'ACM', 'contact@acme.com', '+1-555-0100', 'https://acme.com', 'Leading widget manufacturer', 'active'],
                ['Globex Industries', 'GLB', 'info@globex.com', '+1-555-0101', 'https://globex.com', 'International trading company', 'active'],
                ['Initech LLC', 'INI', 'support@initech.com', '+1-555-0102', 'https://initech.com', 'Software solutions provider', 'active'],
                ['Umbrella Corp', 'UMB', 'hello@umbrella.com', '+1-555-0103', 'https://umbrella.com', 'Pharmaceutical research', 'active'],
                ['Stark Industries', 'STK', 'office@stark.com', '+1-555-0104', 'https://stark.com', 'Advanced technology', 'active'],
            ],
            'description' => 'Brand records with contact information and status',
            'required' => ['name'],
            'optional' => ['code', 'email', 'phone', 'website', 'description', 'status'],
        ],
        'branches' => [
            'headers' => ['name', 'code', 'brand_id', 'email', 'phone', 'address', 'city', 'status'],
            'rows' => [
                ['Headquarters', 'HQ001', '1', 'hq@acme.com', '+1-555-1000', '123 Main Street', 'New York', 'active'],
                ['West Coast Office', 'WC001', '1', 'west@acme.com', '+1-555-1001', '456 West Avenue', 'Los Angeles', 'active'],
                ['East Branch', 'EB001', '2', 'east@globex.com', '+1-555-1002', '789 East Boulevard', 'Boston', 'active'],
                ['South Operations', 'SO001', '3', 'south@initech.com', '+1-555-1003', '321 South Road', 'Austin', 'active'],
                ['Northern Division', 'ND001', '4', 'north@umbrella.com', '+1-555-1004', '654 North Lane', 'Chicago', 'active'],
            ],
            'description' => 'Branch locations linked to brands',
            'required' => ['name', 'brand_id'],
            'optional' => ['code', 'email', 'phone', 'address', 'city', 'status'],
        ],
        'categories' => [
            'headers' => ['name', 'slug', 'description', 'parent_id', 'status'],
            'rows' => [
                ['Electronics', 'electronics', 'Electronic devices and gadgets', '', 'active'],
                ['Clothing', 'clothing', 'Apparel and fashion items', '', 'active'],
                ['Books', 'books', 'Physical and digital books', '', 'active'],
                ['Home & Garden', 'home-garden', 'Home improvement products', '', 'active'],
                ['Sports', 'sports', 'Sports equipment and gear', '', 'active'],
            ],
            'description' => 'Product or content categories',
            'required' => ['name'],
            'optional' => ['slug', 'description', 'parent_id', 'status'],
        ],
        'tags' => [
            'headers' => ['name', 'slug', 'description', 'color', 'status'],
            'rows' => [
                ['Featured', 'featured', 'Featured items on homepage', '#FF5733', 'active'],
                ['New Arrival', 'new-arrival', 'Recently added products', '#33FF57', 'active'],
                ['Best Seller', 'best-seller', 'Top selling items', '#3357FF', 'active'],
                ['Sale', 'sale', 'Items on discount', '#FF33F6', 'active'],
                ['Limited Edition', 'limited', 'Limited availability items', '#F6FF33', 'active'],
            ],
            'description' => 'Tags for organizing and filtering content',
            'required' => ['name'],
            'optional' => ['slug', 'description', 'color', 'status'],
        ],
        'countries' => [
            'headers' => ['name', 'code', 'phone_code', 'currency', 'is_active'],
            'rows' => [
                ['United States', 'US', '+1', 'USD', 'active'],
                ['United Kingdom', 'GB', '+44', 'GBP', 'active'],
                ['Canada', 'CA', '+1', 'CAD', 'active'],
                ['Germany', 'DE', '+49', 'EUR', 'active'],
                ['France', 'FR', '+33', 'EUR', 'active'],
            ],
            'description' => 'Countries for geography setup',
            'required' => ['name', 'code'],
            'optional' => ['phone_code', 'currency', 'is_active'],
        ],
        'provinces' => [
            'headers' => ['name', 'code', 'country_id', 'is_active'],
            'rows' => [
                ['California', 'CA', '1', 'active'],
                ['Texas', 'TX', '1', 'active'],
                ['New York', 'NY', '1', 'active'],
                ['Florida', 'FL', '1', 'active'],
                ['Illinois', 'IL', '1', 'active'],
            ],
            'description' => 'States/Provinces linked to countries',
            'required' => ['name', 'country_id'],
            'optional' => ['code', 'is_active'],
        ],
        'cities' => [
            'headers' => ['name', 'code', 'province_id', 'is_active'],
            'rows' => [
                ['New York City', 'NYC', '3', 'active'],
                ['Los Angeles', 'LA', '1', 'active'],
                ['Chicago', 'CHI', '5', 'active'],
                ['Houston', 'HOU', '2', 'active'],
                ['Phoenix', 'PHX', '1', 'active'],
            ],
            'description' => 'Cities linked to provinces/states',
            'required' => ['name', 'province_id'],
            'optional' => ['code', 'is_active'],
        ],
        'currencies' => [
            'headers' => ['name', 'code', 'symbol', 'exchange_rate', 'is_active'],
            'rows' => [
                ['US Dollar', 'USD', '$', '1.000000', 'active'],
                ['Euro', 'EUR', '€', '0.850000', 'active'],
                ['British Pound', 'GBP', '£', '0.730000', 'active'],
                ['Japanese Yen', 'JPY', '¥', '110.500000', 'active'],
                ['Canadian Dollar', 'CAD', 'C$', '1.250000', 'active'],
            ],
            'description' => 'Currencies with exchange rates',
            'required' => ['name', 'code', 'symbol'],
            'optional' => ['exchange_rate', 'is_active'],
        ],
        'units' => [
            'headers' => ['name', 'code', 'symbol', 'conversion_factor', 'is_active'],
            'rows' => [
                ['Piece', 'pc', 'pcs', '1', 'active'],
                ['Kilogram', 'kg', 'kg', '1', 'active'],
                ['Gram', 'g', 'g', '0.001', 'active'],
                ['Meter', 'm', 'm', '1', 'active'],
                ['Liter', 'l', 'L', '1', 'active'],
            ],
            'description' => 'Measurement units for products',
            'required' => ['name', 'code'],
            'optional' => ['symbol', 'conversion_factor', 'is_active'],
        ],
        'types' => [
            'headers' => ['name', 'slug', 'model_type', 'description', 'is_active'],
            'rows' => [
                ['Product', 'product', 'App\\Models\\Product', 'Standard product type', 'active'],
                ['Service', 'service', 'App\\Models\\Service', 'Service offering type', 'active'],
                ['Subscription', 'subscription', 'App\\Models\\Subscription', 'Recurring subscription', 'active'],
                ['Bundle', 'bundle', 'App\\Models\\Bundle', 'Product bundle type', 'active'],
                ['Digital', 'digital', 'App\\Models\\Digital', 'Downloadable product', 'active'],
            ],
            'description' => 'Entity types for classification',
            'required' => ['name'],
            'optional' => ['slug', 'model_type', 'description', 'is_active'],
        ],
        'industries' => [
            'headers' => ['name', 'code', 'description', 'is_active'],
            'rows' => [
                ['Technology', 'TECH', 'Technology and software', 'active'],
                ['Healthcare', 'HLT', 'Healthcare and medical', 'active'],
                ['Finance', 'FIN', 'Banking and financial', 'active'],
                ['Education', 'EDU', 'Educational services', 'active'],
                ['Retail', 'RTL', 'Retail and consumer goods', 'active'],
            ],
            'description' => 'Industry classifications',
            'required' => ['name'],
            'optional' => ['code', 'description', 'is_active'],
        ],
        'languages' => [
            'headers' => ['name', 'code', 'locale', 'is_active', 'is_default'],
            'rows' => [
                ['English', 'en', 'en_US', 'active', 'yes'],
                ['Spanish', 'es', 'es_ES', 'active', 'no'],
                ['French', 'fr', 'fr_FR', 'active', 'no'],
                ['German', 'de', 'de_DE', 'active', 'no'],
                ['Italian', 'it', 'it_IT', 'active', 'no'],
            ],
            'description' => 'Supported languages for system',
            'required' => ['name', 'code', 'locale'],
            'optional' => ['is_active', 'is_default'],
        ],
        'tenants' => [
            'headers' => ['name', 'email', 'phone', 'domain', 'status'],
            'rows' => [
                ['Acme Tenant', 'admin@acmetenant.com', '+1-555-2000', 'acme.example.com', 'active'],
                ['Globex Tenant', 'info@globextenant.com', '+1-555-2001', 'globex.example.com', 'active'],
                ['Initech Tenant', 'support@initechtenant.com', '+1-555-2002', 'initech.example.com', 'trial'],
            ],
            'description' => 'Tenant/Organization accounts',
            'required' => ['name', 'email'],
            'optional' => ['phone', 'domain', 'status'],
        ],
        'announcements' => [
            'headers' => ['title', 'content', 'start_date', 'end_date', 'is_active'],
            'rows' => [
                ['Welcome!', 'Welcome to our platform. Get started today.', '2025-01-01', '2025-12-31', 'active'],
                ['New Features', 'Check out our latest updates.', '2025-02-01', '2025-03-01', 'active'],
                ['Maintenance', 'Scheduled maintenance this weekend.', '2025-03-20', '2025-03-21', 'active'],
            ],
            'description' => 'System announcements and notices',
            'required' => ['title', 'content'],
            'optional' => ['start_date', 'end_date', 'is_active'],
        ],
    ];

    /**
     * Get sample data preview for an entity
     */
    public function preview(string $entity)
    {
        if (!isset($this->sampleData[$entity])) {
            return response()->json([
                'success' => false,
                'message' => 'No sample data available for this entity',
            ], 404);
        }

        $data = $this->sampleData[$entity];

        return response()->json([
            'success' => true,
            'data' => [
                'entity' => $entity,
                'description' => $data['description'],
                'headers' => $data['headers'],
                'sample_rows' => $data['rows'],
                'required_fields' => $data['required'],
                'optional_fields' => $data['optional'],
                'total_samples' => count($data['rows']),
            ],
        ]);
    }

    /**
     * Download sample CSV file
     */
    public function downloadCsv(string $entity)
    {
        if (!isset($this->sampleData[$entity])) {
            return response()->json([
                'success' => false,
                'message' => 'No sample data available for this entity',
            ], 404);
        }

        $data = $this->sampleData[$entity];
        
        // Generate CSV content
        $output = fopen('php://temp', 'r+');
        
        // Write headers
        fputcsv($output, $data['headers']);
        
        // Write sample rows
        foreach ($data['rows'] as $row) {
            fputcsv($output, $row);
        }
        
        rewind($output);
        $content = stream_get_contents($output);
        fclose($output);

        return response($content)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="' . $entity . '-sample.csv"');
    }

    /**
     * Download sample Excel file
     */
    public function downloadExcel(string $entity)
    {
        if (!isset($this->sampleData[$entity])) {
            return response()->json([
                'success' => false,
                'message' => 'No sample data available for this entity',
            ], 404);
        }

        $data = $this->sampleData[$entity];

        // Create Excel using array structure
        $excelData = [
            $data['headers'],
            ...$data['rows']
        ];

        // Simple XLSX generation (you could use Maatwebsite/Excel here)
        // For now, return CSV with XLSX extension
        $output = fopen('php://temp', 'r+');
        
        foreach ($excelData as $row) {
            fputcsv($output, $row);
        }
        
        rewind($output);
        $content = stream_get_contents($output);
        fclose($output);

        return response($content)
            ->header('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ->header('Content-Disposition', 'attachment; filename="' . $entity . '-sample.xlsx"');
    }

    /**
     * Get all available sample entities
     */
    public function listEntities()
    {
        $entities = [];
        
        foreach ($this->sampleData as $entity => $data) {
            $entities[] = [
                'id' => $entity,
                'name' => ucfirst(str_replace('-', ' ', $entity)),
                'description' => $data['description'],
                'field_count' => count($data['headers']),
                'sample_count' => count($data['rows']),
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $entities,
        ]);
    }
}
