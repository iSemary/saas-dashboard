<?php

namespace Modules\FileManager\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\FileManager\Entities\Folder;

class FolderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $folders = [
            [
                'name' => 'Documents',
                'description' => 'General document storage',
                'parent_id' => null,
                'status' => 'active',
            ],
            [
                'name' => 'Images',
                'description' => 'Image files and graphics',
                'parent_id' => null,
                'status' => 'active',
            ],
            [
                'name' => 'Videos',
                'description' => 'Video files and media',
                'parent_id' => null,
                'status' => 'active',
            ],
            [
                'name' => 'Audio',
                'description' => 'Audio files and music',
                'parent_id' => null,
                'status' => 'active',
            ],
            [
                'name' => 'Archives',
                'description' => 'Compressed and archived files',
                'parent_id' => null,
                'status' => 'active',
            ],
            [
                'name' => 'Templates',
                'description' => 'Document and email templates',
                'parent_id' => null,
                'status' => 'active',
            ],
            [
                'name' => 'Backups',
                'description' => 'System and data backups',
                'parent_id' => null,
                'status' => 'active',
            ],
            [
                'name' => 'Public',
                'description' => 'Publicly accessible files',
                'parent_id' => null,
                'status' => 'active',
            ],
        ];

        // Create parent folders first
        $createdFolders = [];
        foreach ($folders as $folderData) {
            $folder = Folder::create($folderData);
            $createdFolders[$folderData['name']] = $folder->id;
        }

        // Create child folders
        $childFolders = [
            [
                'name' => 'PDFs',
                'description' => 'PDF documents',
                'parent_name' => 'Documents',
                'status' => 'active',
            ],
            [
                'name' => 'Word Documents',
                'description' => 'Microsoft Word documents',
                'parent_name' => 'Documents',
                'status' => 'active',
            ],
            [
                'name' => 'Spreadsheets',
                'description' => 'Excel and CSV files',
                'parent_name' => 'Documents',
                'status' => 'active',
            ],
            [
                'name' => 'Photos',
                'description' => 'Photographs and images',
                'parent_name' => 'Images',
                'status' => 'active',
            ],
            [
                'name' => 'Icons',
                'description' => 'Icon files and graphics',
                'parent_name' => 'Images',
                'status' => 'active',
            ],
            [
                'name' => 'Logos',
                'description' => 'Company and brand logos',
                'parent_name' => 'Images',
                'status' => 'active',
            ],
            [
                'name' => 'Tutorials',
                'description' => 'Educational video content',
                'parent_name' => 'Videos',
                'status' => 'active',
            ],
            [
                'name' => 'Presentations',
                'description' => 'Presentation videos',
                'parent_name' => 'Videos',
                'status' => 'active',
            ],
            [
                'name' => 'Music',
                'description' => 'Music and audio tracks',
                'parent_name' => 'Audio',
                'status' => 'active',
            ],
            [
                'name' => 'Podcasts',
                'description' => 'Podcast episodes',
                'parent_name' => 'Audio',
                'status' => 'active',
            ],
            [
                'name' => 'Email Templates',
                'description' => 'Email template files',
                'parent_name' => 'Templates',
                'status' => 'active',
            ],
            [
                'name' => 'Document Templates',
                'description' => 'Document template files',
                'parent_name' => 'Templates',
                'status' => 'active',
            ],
        ];

        foreach ($childFolders as $childData) {
            $parentId = $createdFolders[$childData['parent_name']] ?? null;
            unset($childData['parent_name']);
            $childData['parent_id'] = $parentId;
            
            Folder::create($childData);
        }

        $this->command->info('Folders seeded successfully!');
    }
}
