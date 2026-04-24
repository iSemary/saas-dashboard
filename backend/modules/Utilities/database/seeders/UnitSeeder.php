<?php

namespace Modules\Utilities\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Utilities\Entities\Unit;
use Modules\Utilities\Entities\Type;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get available types or create them if they don't exist
        $weightType = Type::firstOrCreate(['slug' => 'weight'], ['name' => 'Weight', 'status' => 'active']);
        $lengthType = Type::firstOrCreate(['slug' => 'length'], ['name' => 'Length', 'status' => 'active']);
        $volumeType = Type::firstOrCreate(['slug' => 'volume'], ['name' => 'Volume', 'status' => 'active']);
        $areaType = Type::firstOrCreate(['slug' => 'area'], ['name' => 'Area', 'status' => 'active']);
        $temperatureType = Type::firstOrCreate(['slug' => 'temperature'], ['name' => 'Temperature', 'status' => 'active']);
        $timeType = Type::firstOrCreate(['slug' => 'time'], ['name' => 'Time', 'status' => 'active']);
        $speedType = Type::firstOrCreate(['slug' => 'speed'], ['name' => 'Speed', 'status' => 'active']);
        $pressureType = Type::firstOrCreate(['slug' => 'pressure'], ['name' => 'Pressure', 'status' => 'active']);
        $energyType = Type::firstOrCreate(['slug' => 'energy'], ['name' => 'Energy', 'status' => 'active']);
        $digitalStorageType = Type::firstOrCreate(['slug' => 'digital-storage'], ['name' => 'Digital Storage', 'status' => 'active']);

        $units = [
            // Weight Units
            [
                'name' => 'Gram',
                'code' => 'g',
                'type_id' => $weightType->id,
                'base_conversion' => 1.00000,
                'description' => 'Base unit for weight measurement',
                'is_base_unit' => true,
            ],
            [
                'name' => 'Kilogram',
                'code' => 'kg',
                'type_id' => $weightType->id,
                'base_conversion' => 1000.00000,
                'description' => '1000 grams',
                'is_base_unit' => false,
            ],
            [
                'name' => 'Pound',
                'code' => 'lb',
                'type_id' => $weightType->id,
                'base_conversion' => 453.59200,
                'description' => 'Imperial unit of weight',
                'is_base_unit' => false,
            ],
            [
                'name' => 'Ounce',
                'code' => 'oz',
                'type_id' => $weightType->id,
                'base_conversion' => 28.34950,
                'description' => 'Imperial unit of weight',
                'is_base_unit' => false,
            ],
            [
                'name' => 'Ton',
                'code' => 't',
                'type_id' => $weightType->id,
                'base_conversion' => 1000000.00000,
                'description' => 'Metric ton (1000 kg)',
                'is_base_unit' => false,
            ],

            // Length Units
            [
                'name' => 'Meter',
                'code' => 'm',
                'type_id' => $lengthType->id,
                'base_conversion' => 1.00000,
                'description' => 'Base unit for length measurement',
                'is_base_unit' => true,
            ],
            [
                'name' => 'Centimeter',
                'code' => 'cm',
                'type_id' => $lengthType->id,
                'base_conversion' => 0.01000,
                'description' => '0.01 meters',
                'is_base_unit' => false,
            ],
            [
                'name' => 'Millimeter',
                'code' => 'mm',
                'type_id' => $lengthType->id,
                'base_conversion' => 0.00100,
                'description' => '0.001 meters',
                'is_base_unit' => false,
            ],
            [
                'name' => 'Kilometer',
                'code' => 'km',
                'type_id' => $lengthType->id,
                'base_conversion' => 1000.00000,
                'description' => '1000 meters',
                'is_base_unit' => false,
            ],
            [
                'name' => 'Inch',
                'code' => 'in',
                'type_id' => $lengthType->id,
                'base_conversion' => 0.02540,
                'description' => 'Imperial unit of length',
                'is_base_unit' => false,
            ],
            [
                'name' => 'Foot',
                'code' => 'ft',
                'type_id' => $lengthType->id,
                'base_conversion' => 0.30480,
                'description' => 'Imperial unit of length',
                'is_base_unit' => false,
            ],
            [
                'name' => 'Yard',
                'code' => 'yd',
                'type_id' => $lengthType->id,
                'base_conversion' => 0.91440,
                'description' => 'Imperial unit of length',
                'is_base_unit' => false,
            ],
            [
                'name' => 'Mile',
                'code' => 'mi',
                'type_id' => $lengthType->id,
                'base_conversion' => 1609.34400,
                'description' => 'Imperial unit of length',
                'is_base_unit' => false,
            ],

            // Volume Units
            [
                'name' => 'Liter',
                'code' => 'L',
                'type_id' => $volumeType->id,
                'base_conversion' => 1.00000,
                'description' => 'Base unit for volume measurement',
                'is_base_unit' => true,
            ],
            [
                'name' => 'Milliliter',
                'code' => 'mL',
                'type_id' => $volumeType->id,
                'base_conversion' => 0.00100,
                'description' => '0.001 liters',
                'is_base_unit' => false,
            ],
            [
                'name' => 'Cubic Meter',
                'code' => 'm³',
                'type_id' => $volumeType->id,
                'base_conversion' => 1000.00000,
                'description' => '1000 liters',
                'is_base_unit' => false,
            ],
            [
                'name' => 'Gallon (US)',
                'code' => 'gal',
                'type_id' => $volumeType->id,
                'base_conversion' => 3.78541,
                'description' => 'US gallon',
                'is_base_unit' => false,
            ],
            [
                'name' => 'Quart (US)',
                'code' => 'qt',
                'type_id' => $volumeType->id,
                'base_conversion' => 0.94635,
                'description' => 'US quart',
                'is_base_unit' => false,
            ],
            [
                'name' => 'Pint (US)',
                'code' => 'pt',
                'type_id' => $volumeType->id,
                'base_conversion' => 0.47318,
                'description' => 'US pint',
                'is_base_unit' => false,
            ],
            [
                'name' => 'Cup (US)',
                'code' => 'cup',
                'type_id' => $volumeType->id,
                'base_conversion' => 0.23659,
                'description' => 'US cup',
                'is_base_unit' => false,
            ],

            // Area Units
            [
                'name' => 'Square Meter',
                'code' => 'm²',
                'type_id' => $areaType->id,
                'base_conversion' => 1.00000,
                'description' => 'Base unit for area measurement',
                'is_base_unit' => true,
            ],
            [
                'name' => 'Square Centimeter',
                'code' => 'cm²',
                'type_id' => $areaType->id,
                'base_conversion' => 0.00010,
                'description' => '0.0001 square meters',
                'is_base_unit' => false,
            ],
            [
                'name' => 'Square Kilometer',
                'code' => 'km²',
                'type_id' => $areaType->id,
                'base_conversion' => 1000000.00000,
                'description' => '1,000,000 square meters',
                'is_base_unit' => false,
            ],
            [
                'name' => 'Hectare',
                'code' => 'ha',
                'type_id' => $areaType->id,
                'base_conversion' => 10000.00000,
                'description' => '10,000 square meters',
                'is_base_unit' => false,
            ],
            [
                'name' => 'Square Foot',
                'code' => 'ft²',
                'type_id' => $areaType->id,
                'base_conversion' => 0.09290,
                'description' => 'Imperial unit of area',
                'is_base_unit' => false,
            ],
            [
                'name' => 'Square Inch',
                'code' => 'in²',
                'type_id' => $areaType->id,
                'base_conversion' => 0.00065,
                'description' => 'Imperial unit of area',
                'is_base_unit' => false,
            ],
            [
                'name' => 'Acre',
                'code' => 'ac',
                'type_id' => $areaType->id,
                'base_conversion' => 4046.86000,
                'description' => 'Imperial unit of area',
                'is_base_unit' => false,
            ],

            // Temperature Units
            [
                'name' => 'Celsius',
                'code' => '°C',
                'type_id' => $temperatureType->id,
                'base_conversion' => 1.00000,
                'description' => 'Base unit for temperature measurement',
                'is_base_unit' => true,
            ],
            [
                'name' => 'Fahrenheit',
                'code' => '°F',
                'type_id' => $temperatureType->id,
                'base_conversion' => 1.00000,
                'description' => 'Imperial unit of temperature',
                'is_base_unit' => false,
            ],
            [
                'name' => 'Kelvin',
                'code' => 'K',
                'type_id' => $temperatureType->id,
                'base_conversion' => 1.00000,
                'description' => 'Scientific unit of temperature',
                'is_base_unit' => false,
            ],

            // Time Units
            [
                'name' => 'Second',
                'code' => 's',
                'type_id' => $timeType->id,
                'base_conversion' => 1.00000,
                'description' => 'Base unit for time measurement',
                'is_base_unit' => true,
            ],
            [
                'name' => 'Minute',
                'code' => 'min',
                'type_id' => $timeType->id,
                'base_conversion' => 60.00000,
                'description' => '60 seconds',
                'is_base_unit' => false,
            ],
            [
                'name' => 'Hour',
                'code' => 'h',
                'type_id' => $timeType->id,
                'base_conversion' => 3600.00000,
                'description' => '3600 seconds',
                'is_base_unit' => false,
            ],
            [
                'name' => 'Day',
                'code' => 'd',
                'type_id' => $timeType->id,
                'base_conversion' => 86400.00000,
                'description' => '86400 seconds',
                'is_base_unit' => false,
            ],
            [
                'name' => 'Week',
                'code' => 'wk',
                'type_id' => $timeType->id,
                'base_conversion' => 604800.00000,
                'description' => '604800 seconds',
                'is_base_unit' => false,
            ],
            [
                'name' => 'Month',
                'code' => 'mo',
                'type_id' => $timeType->id,
                'base_conversion' => 2629746.00000,
                'description' => 'Average month in seconds',
                'is_base_unit' => false,
            ],
            [
                'name' => 'Year',
                'code' => 'yr',
                'type_id' => $timeType->id,
                'base_conversion' => 31556952.00000,
                'description' => 'Average year in seconds',
                'is_base_unit' => false,
            ],

            // Speed Units
            [
                'name' => 'Meter per Second',
                'code' => 'm/s',
                'type_id' => $speedType->id,
                'base_conversion' => 1.00000,
                'description' => 'Base unit for speed measurement',
                'is_base_unit' => true,
            ],
            [
                'name' => 'Kilometer per Hour',
                'code' => 'km/h',
                'type_id' => $speedType->id,
                'base_conversion' => 0.27778,
                'description' => 'Common speed unit',
                'is_base_unit' => false,
            ],
            [
                'name' => 'Mile per Hour',
                'code' => 'mph',
                'type_id' => $speedType->id,
                'base_conversion' => 0.44704,
                'description' => 'Imperial speed unit',
                'is_base_unit' => false,
            ],
            [
                'name' => 'Knot',
                'code' => 'kn',
                'type_id' => $speedType->id,
                'base_conversion' => 0.51444,
                'description' => 'Nautical speed unit',
                'is_base_unit' => false,
            ],

            // Pressure Units
            [
                'name' => 'Pascal',
                'code' => 'Pa',
                'type_id' => $pressureType->id,
                'base_conversion' => 1.00000,
                'description' => 'Base unit for pressure measurement',
                'is_base_unit' => true,
            ],
            [
                'name' => 'Kilopascal',
                'code' => 'kPa',
                'type_id' => $pressureType->id,
                'base_conversion' => 1000.00000,
                'description' => '1000 pascals',
                'is_base_unit' => false,
            ],
            [
                'name' => 'Bar',
                'code' => 'bar',
                'type_id' => $pressureType->id,
                'base_conversion' => 100000.00000,
                'description' => '100,000 pascals',
                'is_base_unit' => false,
            ],
            [
                'name' => 'PSI',
                'code' => 'psi',
                'type_id' => $pressureType->id,
                'base_conversion' => 6894.76000,
                'description' => 'Pounds per square inch',
                'is_base_unit' => false,
            ],
            [
                'name' => 'Atmosphere',
                'code' => 'atm',
                'type_id' => $pressureType->id,
                'base_conversion' => 101325.00000,
                'description' => 'Standard atmosphere',
                'is_base_unit' => false,
            ],

            // Energy Units
            [
                'name' => 'Joule',
                'code' => 'J',
                'type_id' => $energyType->id,
                'base_conversion' => 1.00000,
                'description' => 'Base unit for energy measurement',
                'is_base_unit' => true,
            ],
            [
                'name' => 'Kilojoule',
                'code' => 'kJ',
                'type_id' => $energyType->id,
                'base_conversion' => 1000.00000,
                'description' => '1000 joules',
                'is_base_unit' => false,
            ],
            [
                'name' => 'Calorie',
                'code' => 'cal',
                'type_id' => $energyType->id,
                'base_conversion' => 4.18400,
                'description' => 'Thermochemical calorie',
                'is_base_unit' => false,
            ],
            [
                'name' => 'Kilocalorie',
                'code' => 'kcal',
                'type_id' => $energyType->id,
                'base_conversion' => 4184.00000,
                'description' => '1000 calories',
                'is_base_unit' => false,
            ],
            [
                'name' => 'Watt Hour',
                'code' => 'Wh',
                'type_id' => $energyType->id,
                'base_conversion' => 3600.00000,
                'description' => 'Energy unit',
                'is_base_unit' => false,
            ],
            [
                'name' => 'Kilowatt Hour',
                'code' => 'kWh',
                'type_id' => $energyType->id,
                'base_conversion' => 3600000.00000,
                'description' => '1000 watt hours',
                'is_base_unit' => false,
            ],

            // Digital Storage Units
            [
                'name' => 'Byte',
                'code' => 'B',
                'type_id' => $digitalStorageType->id,
                'base_conversion' => 1.00000,
                'description' => 'Base unit for digital storage',
                'is_base_unit' => true,
            ],
            [
                'name' => 'Kilobyte',
                'code' => 'KB',
                'type_id' => $digitalStorageType->id,
                'base_conversion' => 1024.00000,
                'description' => '1024 bytes',
                'is_base_unit' => false,
            ],
            [
                'name' => 'Megabyte',
                'code' => 'MB',
                'type_id' => $digitalStorageType->id,
                'base_conversion' => 1024.00000, // 1024^1
                'description' => '1024 kilobytes',
                'is_base_unit' => false,
            ],
            [
                'name' => 'Gigabyte',
                'code' => 'GB',
                'type_id' => $digitalStorageType->id,
                'base_conversion' => 1048576.00000, // 1024^2
                'description' => '1024 megabytes',
                'is_base_unit' => false,
            ],
            [
                'name' => 'Terabyte',
                'code' => 'TB',
                'type_id' => $digitalStorageType->id,
                'base_conversion' => 1073741824.00000, // 1024^3
                'description' => '1024 gigabytes',
                'is_base_unit' => false,
            ],
            [
                'name' => 'Petabyte',
                'code' => 'PB',
                'type_id' => $digitalStorageType->id,
                'base_conversion' => 9999999999.00000, // Max value for decimal(15,5)
                'description' => '1024 terabytes',
                'is_base_unit' => false,
            ],
        ];

        foreach ($units as $unitData) {
            Unit::firstOrCreate(
                ['name' => $unitData['name'], 'code' => $unitData['code']], 
                $unitData
            );
        }

        $this->command->info('Units seeded successfully!');
    }
}
