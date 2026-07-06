<?php

namespace Database\Seeders;

use App\Models\Asset;
use App\Models\Category;
use App\Models\Court;
use App\Models\Region;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class AssetSeeder extends Seeder
{
    public function run()
    {
        $assets = [
            // Computers and Laptops
            [
                'slug' => Str::slug('Dell Latitude 5420 Laptop'),
                'asset_id' => 'JSG-LAP-001',
                'asset_name' => 'Dell Latitude 5420 Laptop',
                'asset_tag' => 'JSG-LAP-001',
                'serial_number' => 'DL5420JSG001',
                'model' => 'Latitude 5420',
                'brand' => 'Dell',
                'manufacturer' => 'Dell Inc.',
                'category_id' => 2, // Laptops
                'subcategory_id' => null,
                'region_id' => 1, // Greater Accra
                'court_id' => 1, // Supreme Court
                'description' => '14-inch Laptop for judicial staff',
                'purchase_cost' => 4500.00,
                'current_value' => 4000.00,
                'purchase_date' => '2023-01-15',
                'recieved_date' => '2023-01-18',
                'assigned_date' => '2023-01-20',
                'returned_date' => null,
                'returned_reason' => null,
                'returnee' => null,
                'returned_to' => null,
                'supplier' => 'Dell Ghana',
                'warranty_period' => '3 Years',
                'warranty_expiry' => '2026-01-15',
                'warranty_information' => 'Standard 3-year warranty with on-site service',
                'specifications' => 'Intel Core i5, 8GB RAM, 256GB SSD, Windows 11 Pro',
                'condition' => 'excellent',
                'status' => 'assigned',
                'assigned_to' => 4, // Justice Kwame Mensah
                'assigned_type' => 'judge',
                'ip_address' => '192.168.1.101',
                'mac_address' => '00:1B:44:11:3A:B7',
                'registry_id' => 1, // System Admin
            ],
            [
                'slug' => Str::slug('HP EliteDesk 800 G5 Desktop'),
                'asset_id' => 'JSG-DSK-001',
                'asset_name' => 'HP EliteDesk 800 G5 Desktop',
                'asset_tag' => 'JSG-DSK-001',
                'serial_number' => 'HP800G5JSG001',
                'model' => 'EliteDesk 800 G5',
                'brand' => 'HP',
                'manufacturer' => 'HP Inc.',
                'category_id' => 3, // Desktops
                'subcategory_id' => null,
                'region_id' => 1, // Greater Accra
                'court_id' => 2, // High Court Accra
                'description' => 'Desktop computer for registry office',
                'purchase_cost' => 3200.00,
                'current_value' => 2800.00,
                'purchase_date' => '2023-02-10',
                'recieved_date' => '2023-02-12',
                'assigned_date' => '2023-02-15',
                'returned_date' => null,
                'returned_reason' => null,
                'returnee' => null,
                'returned_to' => null,
                'supplier' => 'HP Ghana',
                'warranty_period' => '3 Years',
                'warranty_expiry' => '2026-02-10',
                'warranty_information' => '3-year parts and labor warranty',
                'specifications' => 'Intel Core i5, 16GB RAM, 512GB SSD, Windows 11 Pro',
                'condition' => 'good',
                'status' => 'assigned',
                'assigned_to' => 2, // ICT Manager
                'assigned_type' => 'staff',
                'ip_address' => '192.168.1.102',
                'mac_address' => '00:1B:44:11:3A:B8',
                'registry_id' => 1, // System Admin
            ],
            [
                'slug' => Str::slug('Lenovo ThinkPad X1 Carbon'),
                'asset_id' => 'JSG-LAP-002',
                'asset_name' => 'Lenovo ThinkPad X1 Carbon',
                'asset_tag' => 'JSG-LAP-002',
                'serial_number' => 'LNX1CJSG002',
                'model' => 'ThinkPad X1 Carbon',
                'brand' => 'Lenovo',
                'manufacturer' => 'Lenovo Group Ltd.',
                'category_id' => 2, // Laptops
                'subcategory_id' => null,
                'region_id' => 2, // Ashanti
                'court_id' => 3, // High Court Kumasi
                'description' => 'Ultrabook for mobile judicial work',
                'purchase_cost' => 5200.00,
                'current_value' => 4800.00,
                'purchase_date' => '2023-03-05',
                'recieved_date' => '2023-03-08',
                'assigned_date' => null,
                'returned_date' => null,
                'returned_reason' => null,
                'returnee' => null,
                'returned_to' => null,
                'supplier' => 'Lenovo Ghana',
                'warranty_period' => '3 Years',
                'warranty_expiry' => '2026-03-05',
                'warranty_information' => 'Premium support with accidental damage protection',
                'specifications' => 'Intel Core i7, 16GB RAM, 512GB SSD, Windows 11 Pro',
                'condition' => 'excellent',
                'status' => 'available',
                'assigned_to' => null,
                'assigned_type' => null,
                'ip_address' => '192.168.2.101',
                'mac_address' => '00:1B:44:11:3A:B9',
                'registry_id' => 1, // System Admin
            ],

            // Additional Computers and Laptops
            [
                'slug' => Str::slug('Dell OptiPlex 7090 Desktop'),
                'asset_id' => 'JSG-DSK-002',
                'asset_name' => 'Dell OptiPlex 7090 Desktop',
                'asset_tag' => 'JSG-DSK-002',
                'serial_number' => 'DL7090JSG002',
                'model' => 'OptiPlex 7090',
                'brand' => 'Dell',
                'manufacturer' => 'Dell Inc.',
                'category_id' => 3, // Desktops
                'subcategory_id' => null,
                'region_id' => 1, // Greater Accra
                'court_id' => 2, // High Court Accra
                'description' => 'Desktop computer for legal department',
                'purchase_cost' => 3500.00,
                'current_value' => 3200.00,
                'purchase_date' => '2023-04-10',
                'recieved_date' => '2023-04-12',
                'assigned_date' => '2023-04-15',
                'returned_date' => '2023-11-20',
                'returned_reason' => 'Upgrade to newer model',
                'returnee' => 'John Doe',
                'returned_to' => 'ICT Department',
                'supplier' => 'Dell Ghana',
                'warranty_period' => '3 Years',
                'warranty_expiry' => '2026-04-10',
                'warranty_information' => 'Standard 3-year warranty',
                'specifications' => 'Intel Core i7, 16GB RAM, 1TB SSD, Windows 11 Pro',
                'condition' => 'good',
                'status' => 'available',
                'assigned_to' => null,
                'assigned_type' => null,
                'ip_address' => '192.168.1.103',
                'mac_address' => '00:1B:44:11:3A:C0',
                'registry_id' => 1, // System Admin
            ],
            [
                'slug' => Str::slug('HP ProBook 450 G8 Laptop'),
                'asset_id' => 'JSG-LAP-003',
                'asset_name' => 'HP ProBook 450 G8 Laptop',
                'asset_tag' => 'JSG-LAP-003',
                'serial_number' => 'HP450G8JSG003',
                'model' => 'ProBook 450 G8',
                'brand' => 'HP',
                'manufacturer' => 'HP Inc.',
                'category_id' => 2, // Laptops
                'subcategory_id' => null,
                'region_id' => 3, // Western
                'court_id' => 4, // High Court Takoradi
                'description' => '15.6-inch business laptop for court clerks',
                'purchase_cost' => 3800.00,
                'current_value' => 3500.00,
                'purchase_date' => '2023-05-15',
                'recieved_date' => '2023-05-18',
                'assigned_date' => '2023-05-20',
                'returned_date' => null,
                'returned_reason' => null,
                'returnee' => null,
                'returned_to' => null,
                'supplier' => 'HP Ghana',
                'warranty_period' => '3 Years',
                'warranty_expiry' => '2026-05-15',
                'warranty_information' => '3-year onsite warranty',
                'specifications' => 'Intel Core i5, 8GB RAM, 512GB SSD, Windows 11 Pro',
                'condition' => 'excellent',
                'status' => 'assigned',
                'assigned_to' => 5, // Court Clerk
                'assigned_type' => 'staff',
                'ip_address' => '192.168.3.102',
                'mac_address' => '00:1B:44:11:3A:C1',
                'registry_id' => 1, // System Admin
            ],

            // Printers
            [
                'slug' => Str::slug('HP LaserJet Pro M404dn'),
                'asset_id' => 'JSG-PRT-001',
                'asset_name' => 'HP LaserJet Pro M404dn',
                'asset_tag' => 'JSG-PRT-001',
                'serial_number' => 'HPLJ404JSG001',
                'model' => 'LaserJet Pro M404dn',
                'brand' => 'HP',
                'manufacturer' => 'HP Inc.',
                'category_id' => 8, // Printers
                'subcategory_id' => null,
                'region_id' => 1, // Greater Accra
                'court_id' => 1, // Supreme Court
                'description' => 'Monochrome laser printer for general office use',
                'purchase_cost' => 1200.00,
                'current_value' => 1000.00,
                'purchase_date' => '2023-04-20',
                'recieved_date' => '2023-04-22',
                'assigned_date' => '2023-04-25',
                'returned_date' => null,
                'returned_reason' => null,
                'returnee' => null,
                'returned_to' => null,
                'supplier' => 'HP Ghana',
                'warranty_period' => '1 Year',
                'warranty_expiry' => '2024-04-20',
                'warranty_information' => '1-year on-site warranty',
                'specifications' => 'Up to 40 ppm, 600 x 600 dpi, Ethernet, Duplex printing',
                'condition' => 'good',
                'status' => 'assigned',
                'assigned_to' => null,
                'assigned_type' => 'department',
                'ip_address' => '192.168.1.201',
                'mac_address' => '00:1B:44:11:3A:C2',
                'registry_id' => 1, // System Admin
            ],
            [
                'slug' => Str::slug('Canon PIXMA TR8550 Printer'),
                'asset_id' => 'JSG-PRT-002',
                'asset_name' => 'Canon PIXMA TR8550 Printer',
                'asset_tag' => 'JSG-PRT-002',
                'serial_number' => 'CNTR8550JSG002',
                'model' => 'PIXMA TR8550',
                'brand' => 'Canon',
                'manufacturer' => 'Canon Inc.',
                'category_id' => 8, // Printers
                'subcategory_id' => null,
                'region_id' => 2, // Ashanti
                'court_id' => 3, // High Court Kumasi
                'description' => 'All-in-one color inkjet printer for document processing',
                'purchase_cost' => 800.00,
                'current_value' => 700.00,
                'purchase_date' => '2023-06-10',
                'recieved_date' => '2023-06-12',
                'assigned_date' => '2023-06-15',
                'returned_date' => null,
                'returned_reason' => null,
                'returnee' => null,
                'returned_to' => null,
                'supplier' => 'Canon Ghana',
                'warranty_period' => '1 Year',
                'warranty_expiry' => '2024-06-10',
                'warranty_information' => '1-year limited warranty',
                'specifications' => 'Print, scan, copy, fax, WiFi, Ethernet, ADF',
                'condition' => 'good',
                'status' => 'assigned',
                'assigned_to' => null,
                'assigned_type' => 'department',
                'ip_address' => '192.168.2.201',
                'mac_address' => '00:1B:44:11:3A:C3',
                'registry_id' => 1, // System Admin
            ],

            // Scanners
            [
                'slug' => Str::slug('Fujitsu ScanSnap iX1600 Scanner'),
                'asset_id' => 'JSG-SCN-001',
                'asset_name' => 'Fujitsu ScanSnap iX1600 Scanner',
                'asset_tag' => 'JSG-SCN-001',
                'serial_number' => 'FJIX1600JSG001',
                'model' => 'ScanSnap iX1600',
                'brand' => 'Fujitsu',
                'manufacturer' => 'Fujitsu Ltd.',
                'category_id' => 9, // Scanners
                'subcategory_id' => null,
                'region_id' => 1, // Greater Accra
                'court_id' => 1, // Supreme Court
                'description' => 'Document scanner for digitizing court records',
                'purchase_cost' => 1500.00,
                'current_value' => 1400.00,
                'purchase_date' => '2023-07-05',
                'recieved_date' => '2023-07-08',
                'assigned_date' => '2023-07-10',
                'returned_date' => null,
                'returned_reason' => null,
                'returnee' => null,
                'returned_to' => null,
                'supplier' => 'Fujitsu Ghana',
                'warranty_period' => '2 Years',
                'warranty_expiry' => '2025-07-05',
                'warranty_information' => '2-year carry-in warranty',
                'specifications' => 'Duplex scanning, 40ppm, ADF, USB and WiFi',
                'condition' => 'excellent',
                'status' => 'assigned',
                'assigned_to' => null,
                'assigned_type' => 'department',
                'ip_address' => '192.168.1.202',
                'mac_address' => '00:1B:44:11:3A:C4',
                'registry_id' => 1, // System Admin
            ],
            [
                'slug' => Str::slug('Epson WorkForce DS-530 Scanner'),
                'asset_id' => 'JSG-SCN-002',
                'asset_name' => 'Epson WorkForce DS-530 Scanner',
                'asset_tag' => 'JSG-SCN-002',
                'serial_number' => 'EPSDS530JSG002',
                'model' => 'WorkForce DS-530',
                'brand' => 'Epson',
                'manufacturer' => 'Seiko Epson Corporation',
                'category_id' => 9, // Scanners
                'subcategory_id' => null,
                'region_id' => 2, // Ashanti
                'court_id' => 3, // High Court Kumasi
                'description' => 'Compact document scanner for office use',
                'purchase_cost' => 900.00,
                'current_value' => 850.00,
                'purchase_date' => '2023-08-12',
                'recieved_date' => '2023-08-15',
                'assigned_date' => '2023-08-18',
                'returned_date' => '2023-12-01',
                'returned_reason' => 'Maintenance required',
                'returnee' => 'Sarah Johnson',
                'returned_to' => 'ICT Support',
                'supplier' => 'Epson Ghana',
                'warranty_period' => '1 Year',
                'warranty_expiry' => '2024-08-12',
                'warranty_information' => '1-year limited warranty',
                'specifications' => '35ppm, duplex, ADF, USB connection',
                'condition' => 'maintenance',
                'status' => 'maintenance',
                'assigned_to' => null,
                'assigned_type' => null,
                'ip_address' => '192.168.2.202',
                'mac_address' => '00:1B:44:11:3A:C5',
                'registry_id' => 1, // System Admin
            ],

            // Photocopiers
            [
                'slug' => Str::slug('Ricoh MP C4503 Photocopier'),
                'asset_id' => 'JSG-COPY-001',
                'asset_name' => 'Ricoh MP C4503 Photocopier',
                'asset_tag' => 'JSG-COPY-001',
                'serial_number' => 'RCMC4503JSG001',
                'model' => 'MP C4503',
                'brand' => 'Ricoh',
                'manufacturer' => 'Ricoh Company Ltd.',
                'category_id' => 10, // Photocopiers
                'subcategory_id' => null,
                'region_id' => 1, // Greater Accra
                'court_id' => 1, // Supreme Court
                'description' => 'Color multifunction photocopier for main registry',
                'purchase_cost' => 8500.00,
                'current_value' => 8000.00,
                'purchase_date' => '2023-03-20',
                'recieved_date' => '2023-03-25',
                'assigned_date' => '2023-03-28',
                'returned_date' => null,
                'returned_reason' => null,
                'returnee' => null,
                'returned_to' => null,
                'supplier' => 'Ricoh Ghana',
                'warranty_period' => '3 Years',
                'warranty_expiry' => '2026-03-20',
                'warranty_information' => '3-year service contract included',
                'specifications' => 'Print, copy, scan, fax, 45ppm, A3 capability',
                'condition' => 'excellent',
                'status' => 'assigned',
                'assigned_to' => null,
                'assigned_type' => 'department',
                'ip_address' => '192.168.1.203',
                'mac_address' => '00:1B:44:11:3A:C6',
                'registry_id' => 1, // System Admin
            ],
            [
                'slug' => Str::slug('Xerox VersaLink C405 Photocopier'),
                'asset_id' => 'JSG-COPY-002',
                'asset_name' => 'Xerox VersaLink C405 Photocopier',
                'asset_tag' => 'JSG-COPY-002',
                'serial_number' => 'XRVL405JSG002',
                'model' => 'VersaLink C405',
                'brand' => 'Xerox',
                'manufacturer' => 'Xerox Corporation',
                'category_id' => 10, // Photocopiers
                'subcategory_id' => null,
                'region_id' => 2, // Ashanti
                'court_id' => 3, // High Court Kumasi
                'description' => 'Color multifunction printer for legal department',
                'purchase_cost' => 7200.00,
                'current_value' => 6800.00,
                'purchase_date' => '2023-09-15',
                'recieved_date' => '2023-09-18',
                'assigned_date' => '2023-09-20',
                'returned_date' => null,
                'returned_reason' => null,
                'returnee' => null,
                'returned_to' => null,
                'supplier' => 'Xerox Ghana',
                'warranty_period' => '2 Years',
                'warranty_expiry' => '2025-09-15',
                'warranty_information' => '2-year comprehensive warranty',
                'specifications' => '40ppm color, A4/A3, scan to email, cloud connectivity',
                'condition' => 'good',
                'status' => 'assigned',
                'assigned_to' => null,
                'assigned_type' => 'department',
                'ip_address' => '192.168.2.203',
                'mac_address' => '00:1B:44:11:3A:C7',
                'registry_id' => 1, // System Admin
            ],

            // Additional Desktops
            [
                'slug' => Str::slug('Apple iMac 24-inch M1'),
                'asset_id' => 'JSG-DSK-003',
                'asset_name' => 'Apple iMac 24-inch M1',
                'asset_tag' => 'JSG-DSK-003',
                'serial_number' => 'APIMACM1JSG003',
                'model' => 'iMac 24-inch M1',
                'brand' => 'Apple',
                'manufacturer' => 'Apple Inc.',
                'category_id' => 3, // Desktops
                'subcategory_id' => null,
                'region_id' => 1, // Greater Accra
                'court_id' => 1, // Supreme Court
                'description' => 'All-in-one desktop for design and media department',
                'purchase_cost' => 7500.00,
                'current_value' => 7000.00,
                'purchase_date' => '2023-10-05',
                'recieved_date' => '2023-10-08',
                'assigned_date' => '2023-10-10',
                'returned_date' => null,
                'returned_reason' => null,
                'returnee' => null,
                'returned_to' => null,
                'supplier' => 'Apple Ghana',
                'warranty_period' => '1 Year',
                'warranty_expiry' => '2024-10-05',
                'warranty_information' => '1-year limited warranty with AppleCare option',
                'specifications' => 'Apple M1 chip, 8GB RAM, 256GB SSD, 24-inch 4.5K display',
                'condition' => 'excellent',
                'status' => 'assigned',
                'assigned_to' => 6, // Graphics Designer
                'assigned_type' => 'staff',
                'ip_address' => '192.168.1.104',
                'mac_address' => '00:1B:44:11:3A:C8',
                'registry_id' => 1, // System Admin
            ],

            // Networking Equipment
            [
                'slug' => Str::slug('Cisco Catalyst 2960 Switch'),
                'asset_id' => 'JSG-SW-001',
                'asset_name' => 'Cisco Catalyst 2960 Switch',
                'asset_tag' => 'JSG-SW-001',
                'serial_number' => 'CS2960JSG001',
                'model' => 'Catalyst 2960',
                'brand' => 'Cisco',
                'manufacturer' => 'Cisco Systems',
                'category_id' => 6, // Switches
                'subcategory_id' => null,
                'region_id' => 1, // Greater Accra
                'court_id' => 1, // Supreme Court
                'description' => '24-port network switch for main server room',
                'purchase_cost' => 800.00,
                'current_value' => 750.00,
                'purchase_date' => '2023-05-10',
                'recieved_date' => '2023-05-12',
                'assigned_date' => '2023-05-15',
                'returned_date' => null,
                'returned_reason' => null,
                'returnee' => null,
                'returned_to' => null,
                'supplier' => 'Cisco Ghana',
                'warranty_period' => 'Lifetime',
                'warranty_expiry' => null,
                'warranty_information' => 'Lifetime hardware warranty',
                'specifications' => '24 x 10/100/1000 ports, 2 x SFP slots',
                'condition' => 'excellent',
                'status' => 'assigned',
                'assigned_to' => null,
                'assigned_type' => 'department',
                'ip_address' => '192.168.1.1',
                'mac_address' => '00:1B:44:11:3A:C9',
                'registry_id' => 1, // System Admin
            ],

            // Assets under maintenance
            [
                'slug' => Str::slug('Dell OptiPlex 7080 Desktop'),
                'asset_id' => 'JSG-DSK-004',
                'asset_name' => 'Dell OptiPlex 7080 Desktop',
                'asset_tag' => 'JSG-DSK-004',
                'serial_number' => 'DL7080JSG004',
                'model' => 'OptiPlex 7080',
                'brand' => 'Dell',
                'manufacturer' => 'Dell Inc.',
                'category_id' => 3, // Desktops
                'subcategory_id' => null,
                'region_id' => 1, // Greater Accra
                'court_id' => 2, // High Court Accra
                'description' => 'Desktop computer for legal research',
                'purchase_cost' => 2800.00,
                'current_value' => 2200.00,
                'purchase_date' => '2022-11-20',
                'recieved_date' => '2022-11-22',
                'assigned_date' => '2022-11-25',
                'returned_date' => '2023-12-01',
                'returned_reason' => 'Hardware failure',
                'returnee' => 'Michael Brown',
                'returned_to' => 'ICT Maintenance',
                'supplier' => 'Dell Ghana',
                'warranty_period' => '3 Years',
                'warranty_expiry' => '2025-11-20',
                'warranty_information' => 'Standard 3-year warranty',
                'specifications' => 'Intel Core i5, 8GB RAM, 1TB HDD, Windows 11 Pro',
                'condition' => 'poor',
                'status' => 'maintenance',
                'assigned_to' => null,
                'assigned_type' => null,
                'last_maintenance' => '2023-12-01',
                'next_maintenance' => '2024-01-15',
                'maintenance_notes' => 'Hard disk failure, awaiting replacement',
                'ip_address' => '192.168.1.105',
                'mac_address' => '00:1B:44:11:3A:D0',
                'registry_id' => 1, // System Admin
            ],

            // Retired assets
            [
                'slug' => Str::slug('HP Compaq 8200 Elite'),
                'asset_id' => 'JSG-DSK-005',
                'asset_name' => 'HP Compaq 8200 Elite',
                'asset_tag' => 'JSG-DSK-005',
                'serial_number' => 'HP8200JSG005',
                'model' => 'Compaq 8200 Elite',
                'brand' => 'HP',
                'manufacturer' => 'HP Inc.',
                'category_id' => 3, // Desktops
                'subcategory_id' => null,
                'region_id' => 1, // Greater Accra
                'court_id' => 1, // Supreme Court
                'description' => 'Legacy desktop computer',
                'purchase_cost' => 1800.00,
                'current_value' => 200.00,
                'purchase_date' => '2018-03-10',
                'recieved_date' => '2018-03-12',
                'assigned_date' => '2018-03-15',
                'returned_date' => '2023-10-01',
                'returned_reason' => 'End of life',
                'returnee' => 'ICT Department',
                'returned_to' => 'Asset Disposal',
                'supplier' => 'HP Ghana',
                'warranty_period' => '3 Years',
                'warranty_expiry' => '2021-03-10',
                'warranty_information' => 'Expired warranty',
                'specifications' => 'Intel Core i3, 4GB RAM, 500GB HDD, Windows 10',
                'condition' => 'broken',
                'status' => 'retired',
                'assigned_to' => null,
                'assigned_type' => null,
                'ip_address' => '192.168.1.106',
                'mac_address' => '00:1B:44:11:3A:D1',
                'registry_id' => 1, // System Admin
            ],

            // More laptops for different regions
            [
                'slug' => Str::slug('Apple MacBook Air M2'),
                'asset_id' => 'JSG-LAP-004',
                'asset_name' => 'Apple MacBook Air M2',
                'asset_tag' => 'JSG-LAP-004',
                'serial_number' => 'APMBAJSG004',
                'model' => 'MacBook Air M2',
                'brand' => 'Apple',
                'manufacturer' => 'Apple Inc.',
                'category_id' => 2, // Laptops
                'subcategory_id' => null,
                'region_id' => 3, // Western
                'court_id' => null,
                'description' => 'MacBook for creative and design work',
                'purchase_cost' => 6500.00,
                'current_value' => 6200.00,
                'purchase_date' => '2023-08-10',
                'recieved_date' => '2023-08-13',
                'assigned_date' => null,
                'returned_date' => null,
                'returned_reason' => null,
                'returnee' => null,
                'returned_to' => null,
                'supplier' => 'Apple Ghana',
                'warranty_period' => '1 Year',
                'warranty_expiry' => '2024-08-10',
                'warranty_information' => '1-year limited warranty',
                'specifications' => 'Apple M2 chip, 8GB RAM, 256GB SSD, macOS',
                'condition' => 'excellent',
                'status' => 'available',
                'assigned_to' => null,
                'assigned_type' => null,
                'ip_address' => '192.168.3.101',
                'mac_address' => '00:1B:44:11:3A:D2',
                'registry_id' => 1, // System Admin
            ],
        ];

        foreach ($assets as $asset) {
            Asset::create($asset);
        }

        // Create some accessories for the assets
        $this->createAccessories();

        // Create some maintenance logs
        $this->createMaintenanceLogs();

        // Create some asset histories
        $this->createAssetHistories();
    }

    private function createAccessories()
    {
        $accessories = [
            [
                'name' => 'Dell Docking Station WD19',
                'description' => 'USB-C docking station for Dell laptops',
                'serial_number' => 'DELLDOCK001',
                'model' => 'WD19',
                'asset_id' => 1,
                'condition' => 'excellent',
                'date_acquired' => '2023-01-15',
                'cost' => 250.00,
                'notes' => 'Comes with power adapter and cables',
            ],
            [
                'name' => 'HP 24-inch Monitor',
                'description' => 'Full HD monitor for desktop setup',
                'serial_number' => 'HPMON24001',
                'model' => '24F',
                'asset_id' => 2,
                'condition' => 'good',
                'date_acquired' => '2023-02-10',
                'cost' => 300.00,
                'notes' => 'Includes HDMI and DisplayPort cables',
            ],
            [
                'name' => 'Laptop Bag',
                'description' => 'Professional laptop carrying case',
                'serial_number' => 'BAGLAP001',
                'model' => 'Executive Pro',
                'asset_id' => 1,
                'condition' => 'good',
                'date_acquired' => '2023-01-20',
                'cost' => 80.00,
                'notes' => 'Water-resistant, multiple compartments',
            ],
            [
                'name' => 'Wireless Mouse',
                'description' => 'Logitech wireless mouse',
                'serial_number' => 'LOGIMSE001',
                'model' => 'M720',
                'asset_id' => 3,
                'condition' => 'excellent',
                'date_acquired' => '2023-03-05',
                'cost' => 45.00,
                'notes' => 'Triple connectivity, ergonomic design',
            ],
        ];

        foreach ($accessories as $accessory) {
            \App\Models\Accessory::create($accessory);
        }
    }

    private function createMaintenanceLogs()
    {
        $logs = [
            [
                'asset_id' => 13, // Under maintenance desktop
                'maintenance_date' => '2023-12-01',
                'type' => 'corrective',
                'description' => 'Hard disk failure diagnosis',
                'actions_taken' => 'Diagnosed failing hard disk, ordered replacement part',
                'cost' => 0.00,
                'technician' => 'ICT Support Team',
                'next_maintenance_date' => '2024-01-15',
                'performed_by' => 2, // ICT Manager
            ],
            [
                'asset_id' => 1, // Dell Laptop
                'maintenance_date' => '2023-06-15',
                'type' => 'preventive',
                'description' => 'Regular maintenance and cleaning',
                'actions_taken' => 'Cleaned internal components, updated drivers, checked battery health',
                'cost' => 0.00,
                'technician' => 'ICT Support Team',
                'next_maintenance_date' => '2023-12-15',
                'performed_by' => 2, // ICT Manager
            ],
            [
                'asset_id' => 6, // HP Printer
                'maintenance_date' => '2023-09-10',
                'type' => 'corrective',
                'description' => 'Paper jam and roller replacement',
                'actions_taken' => 'Cleared paper jam, replaced worn pickup rollers',
                'cost' => 120.00,
                'technician' => 'External Service Provider',
                'next_maintenance_date' => '2024-03-10',
                'performed_by' => 2, // ICT Manager
            ],
            [
                'asset_id' => 9, // Epson Scanner
                'maintenance_date' => '2023-12-01',
                'type' => 'corrective',
                'description' => 'Scanner ADF mechanism repair',
                'actions_taken' => 'Cleaned and lubricated ADF rollers, replaced worn parts',
                'cost' => 85.00,
                'technician' => 'External Service Provider',
                'next_maintenance_date' => '2024-06-01',
                'performed_by' => 2, // ICT Manager
            ],
        ];

        foreach ($logs as $log) {
            \App\Models\MaintenanceLog::create($log);
        }
    }

    private function createAssetHistories()
    {
        $histories = [
            [
                'asset_id' => 1,
                'action' => 'assigned',
                'description' => 'Asset assigned to Justice Kwame Mensah',
                'old_values' => ['assigned_to' => null, 'status' => 'available'],
                'new_values' => ['assigned_to' => 4, 'status' => 'assigned'],
                'performed_by' => 2, // ICT Manager
                'performed_at' => Carbon::parse('2023-01-20 09:30:00'),
            ],
            [
                'asset_id' => 2,
                'action' => 'assigned',
                'description' => 'Asset assigned to ICT Department',
                'old_values' => ['assigned_to' => null, 'status' => 'available'],
                'new_values' => ['assigned_to' => 2, 'status' => 'assigned'],
                'performed_by' => 1, // System Admin
                'performed_at' => Carbon::parse('2023-02-15 14:15:00'),
            ],
            [
                'asset_id' => 4,
                'action' => 'returned',
                'description' => 'Asset returned for upgrade',
                'old_values' => ['status' => 'assigned', 'assigned_to' => 7],
                'new_values' => ['status' => 'available', 'assigned_to' => null],
                'performed_by' => 2, // ICT Manager
                'performed_at' => Carbon::parse('2023-11-20 11:30:00'),
            ],
            [
                'asset_id' => 13,
                'action' => 'maintenance',
                'description' => 'Asset marked for maintenance due to hard disk failure',
                'old_values' => ['status' => 'assigned', 'condition' => 'good'],
                'new_values' => ['status' => 'maintenance', 'condition' => 'poor'],
                'performed_by' => 2, // ICT Manager
                'performed_at' => Carbon::parse('2023-12-01 11:00:00'),
            ],
            [
                'asset_id' => 14,
                'action' => 'retired',
                'description' => 'Asset retired due to end of life',
                'old_values' => ['status' => 'available', 'condition' => 'fair'],
                'new_values' => ['status' => 'retired', 'condition' => 'broken'],
                'performed_by' => 2, // ICT Manager
                'performed_at' => Carbon::parse('2023-10-01 16:45:00'),
            ],
        ];

        foreach ($histories as $history) {
            \App\Models\AssetHistory::create($history);
        }
    }
}