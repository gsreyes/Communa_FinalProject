<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SystemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
         $this->seedBillTypes();
        $this->seedTicketCategories();
    }
    /* Seed bill types */
    private function seedBillTypes(): void
    {
        $billTypes = [
            [
                'name' => 'Water Bill',
                'description' => 'Monthly water consumption charges',
                'code' => 'WATER',
                'is_active' => true,
            ],
            [
                'name' => 'Association Dues',
                'description' => 'Monthly condominium association dues',
                'code' => 'DUES',
                'is_active' => true,
            ],
            [
                'name' => 'Maintenance Fee',
                'description' => 'Building maintenance and common area upkeep',
                'code' => 'MAINTENANCE',
                'is_active' => true,
            ],
            [
                'name' => 'Rules Violation Fine',
                'description' => 'Fine for violating condominium rules and regulations',
                'code' => 'VIOLATION',
                'is_active' => true,
            ],
            [
                'name' => 'Special Assessment',
                'description' => 'Special assessment for building improvements or repairs',
                'code' => 'ASSESSMENT',
                'is_active' => true,
            ],
        ];

        foreach ($billTypes as $type) {
            BillType::updateOrCreate(
                ['code' => $type['code']],
                $type
            );
        }
    }
    /* Seed ticket categories */
     private function seedTicketCategories(): void
    {
        $categories = [
            // Concern type
            [
                'type' => 'concern',
                'name' => 'Maintenance Issue',
                'description' => 'Report broken facilities, leaks, or maintenance problems',
                'code' => 'MAINTENANCE_ISSUE',
                'is_active' => true,
            ],
            [
                'type' => 'concern',
                'name' => 'Noise Complaint',
                'description' => 'Report excessive noise or disturbances',
                'code' => 'NOISE_COMPLAINT',
                'is_active' => true,
            ],
            [
                'type' => 'concern',
                'name' => 'Security Issue',
                'description' => 'Report security concerns or unauthorized access',
                'code' => 'SECURITY_ISSUE',
                'is_active' => true,
            ],
            [
                'type' => 'concern',
                'name' => 'Cleanliness Issue',
                'description' => 'Report cleanliness or hygiene problems in common areas',
                'code' => 'CLEANLINESS_ISSUE',
                'is_active' => true,
            ],
            [
                'type' => 'concern',
                'name' => 'General Complaint',
                'description' => 'Other concerns not covered by specific categories',
                'code' => 'GENERAL_COMPLAINT',
                'is_active' => true,
            ],
            // Request type
            [
                'type' => 'request',
                'name' => 'Gate Pass Request',
                'description' => 'Request a gate pass for vehicles',
                'code' => 'GATE_PASS',
                'is_active' => true,
            ],
            [
                'type' => 'request',
                'name' => 'Visitor Pass Request',
                'description' => 'Request a visitor pass for guests',
                'code' => 'VISITOR_PASS',
                'is_active' => true,
            ],
            [
                'type' => 'request',
                'name' => 'Work Permit Request',
                'description' => 'Request a work permit for contractors or maintenance workers',
                'code' => 'WORK_PERMIT',
                'is_active' => true,
            ],
            [
                'type' => 'request',
                'name' => 'CCTV Footage Request',
                'description' => 'Request CCTV footage from security',
                'code' => 'CCTV_REQUEST',
                'is_active' => true,
            ],
            [
                'type' => 'request',
                'name' => 'Maintenance Request',
                'description' => 'Request maintenance or repairs for your unit',
                'code' => 'MAINTENANCE_REQUEST',
                'is_active' => true,
            ],
            [
                'type' => 'request',
                'name' => 'General Request',
                'description' => 'Other requests not covered by specific categories',
                'code' => 'GENERAL_REQUEST',
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            TicketCategory::updateOrCreate(
                ['code' => $category['code']],
                $category
            );
        }
    }
}
