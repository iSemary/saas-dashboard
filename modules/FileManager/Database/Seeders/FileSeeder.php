<?php

namespace Modules\FileManager\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\FileManager\Entities\File;
use Modules\FileManager\Entities\Folder;

class FileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $folders = Folder::all();

        if ($folders->isEmpty()) {
            $this->command->warn('No folders found. Skipping file seeding.');
            return;
        }

        $files = [
            [
                'folder_id' => $folders->where('name', 'PDFs')->first()?->id,
                'hash_name' => 'doc_001_' . time() . '.pdf',
                'checksum' => 'sha256:abc123def456ghi789jkl012mno345pqr678stu901vwx234yz',
                'original_name' => 'user_manual.pdf',
                'mime_type' => 'application/pdf',
                'host' => 'local',
                'status' => 'active',
                'access_level' => 'public',
                'size' => 2048576, // 2MB
                'metadata' => [
                    'pages' => 25,
                    'author' => 'John Doe',
                    'created_date' => '2024-01-15',
                    'version' => '1.0',
                ],
                'is_encrypted' => false,
            ],
            [
                'folder_id' => $folders->where('name', 'Photos')->first()?->id,
                'hash_name' => 'img_001_' . time() . '.jpg',
                'checksum' => 'sha256:def456ghi789jkl012mno345pqr678stu901vwx234yzabc123',
                'original_name' => 'company_logo.jpg',
                'mime_type' => 'image/jpeg',
                'host' => 'local',
                'status' => 'active',
                'access_level' => 'public',
                'size' => 512000, // 500KB
                'metadata' => [
                    'width' => 800,
                    'height' => 600,
                    'format' => 'JPEG',
                    'color_space' => 'RGB',
                ],
                'is_encrypted' => false,
            ],
            [
                'folder_id' => $folders->where('name', 'Word Documents')->first()?->id,
                'hash_name' => 'doc_002_' . time() . '.docx',
                'checksum' => 'sha256:ghi789jkl012mno345pqr678stu901vwx234yzabc123def456',
                'original_name' => 'project_proposal.docx',
                'mime_type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'host' => 'local',
                'status' => 'active',
                'access_level' => 'private',
                'size' => 1536000, // 1.5MB
                'metadata' => [
                    'pages' => 12,
                    'author' => 'Jane Smith',
                    'created_date' => '2024-01-20',
                    'word_count' => 2500,
                ],
                'is_encrypted' => true,
            ],
            [
                'folder_id' => $folders->where('name', 'Spreadsheets')->first()?->id,
                'hash_name' => 'sheet_001_' . time() . '.xlsx',
                'checksum' => 'sha256:jkl012mno345pqr678stu901vwx234yzabc123def456ghi789',
                'original_name' => 'financial_report.xlsx',
                'mime_type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'host' => 'local',
                'status' => 'active',
                'access_level' => 'private',
                'size' => 1024000, // 1MB
                'metadata' => [
                    'sheets' => 5,
                    'author' => 'Mike Johnson',
                    'created_date' => '2024-01-18',
                    'formulas' => 150,
                ],
                'is_encrypted' => false,
            ],
            [
                'folder_id' => $folders->where('name', 'Tutorials')->first()?->id,
                'hash_name' => 'vid_001_' . time() . '.mp4',
                'checksum' => 'sha256:mno345pqr678stu901vwx234yzabc123def456ghi789jkl012',
                'original_name' => 'getting_started.mp4',
                'mime_type' => 'video/mp4',
                'host' => 'local',
                'status' => 'active',
                'access_level' => 'public',
                'size' => 52428800, // 50MB
                'metadata' => [
                    'duration' => '00:15:30',
                    'resolution' => '1920x1080',
                    'fps' => 30,
                    'codec' => 'H.264',
                ],
                'is_encrypted' => false,
            ],
            [
                'folder_id' => $folders->where('name', 'Music')->first()?->id,
                'hash_name' => 'aud_001_' . time() . '.mp3',
                'checksum' => 'sha256:pqr678stu901vwx234yzabc123def456ghi789jkl012mno345',
                'original_name' => 'background_music.mp3',
                'mime_type' => 'audio/mpeg',
                'host' => 'local',
                'status' => 'active',
                'access_level' => 'public',
                'size' => 5120000, // 5MB
                'metadata' => [
                    'duration' => '00:03:45',
                    'bitrate' => 128,
                    'sample_rate' => 44100,
                    'channels' => 2,
                ],
                'is_encrypted' => false,
            ],
            [
                'folder_id' => $folders->where('name', 'Icons')->first()?->id,
                'hash_name' => 'ico_001_' . time() . '.png',
                'checksum' => 'sha256:stu901vwx234yzabc123def456ghi789jkl012mno345pqr678',
                'original_name' => 'app_icon.png',
                'mime_type' => 'image/png',
                'host' => 'local',
                'status' => 'active',
                'access_level' => 'public',
                'size' => 25600, // 25KB
                'metadata' => [
                    'width' => 64,
                    'height' => 64,
                    'format' => 'PNG',
                    'transparency' => true,
                ],
                'is_encrypted' => false,
            ],
            [
                'folder_id' => $folders->where('name', 'Email Templates')->first()?->id,
                'hash_name' => 'tpl_001_' . time() . '.html',
                'checksum' => 'sha256:vwx234yzabc123def456ghi789jkl012mno345pqr678stu901',
                'original_name' => 'welcome_email.html',
                'mime_type' => 'text/html',
                'host' => 'local',
                'status' => 'active',
                'access_level' => 'public',
                'size' => 8192, // 8KB
                'metadata' => [
                    'template_type' => 'email',
                    'author' => 'Sarah Wilson',
                    'created_date' => '2024-01-10',
                    'variables' => ['name', 'email', 'company'],
                ],
                'is_encrypted' => false,
            ],
            [
                'folder_id' => $folders->where('name', 'Archives')->first()?->id,
                'hash_name' => 'arc_001_' . time() . '.zip',
                'checksum' => 'sha256:yzabc123def456ghi789jkl012mno345pqr678stu901vwx234',
                'original_name' => 'project_files.zip',
                'mime_type' => 'application/zip',
                'host' => 'local',
                'status' => 'active',
                'access_level' => 'private',
                'size' => 10485760, // 10MB
                'metadata' => [
                    'compression' => 'deflate',
                    'files_count' => 25,
                    'created_date' => '2024-01-22',
                    'password_protected' => false,
                ],
                'is_encrypted' => false,
            ],
            [
                'folder_id' => $folders->where('name', 'Backups')->first()?->id,
                'hash_name' => 'bak_001_' . time() . '.sql',
                'checksum' => 'sha256:abc123def456ghi789jkl012mno345pqr678stu901vwx234yz',
                'original_name' => 'database_backup.sql',
                'mime_type' => 'application/sql',
                'host' => 'local',
                'status' => 'active',
                'access_level' => 'private',
                'size' => 52428800, // 50MB
                'metadata' => [
                    'database' => 'saas_dashboard',
                    'tables' => 45,
                    'backup_date' => '2024-01-25',
                    'compression' => 'gzip',
                ],
                'is_encrypted' => true,
            ],
        ];

        foreach ($files as $fileData) {
            File::create($fileData);
        }

        $this->command->info('Files seeded successfully!');
    }
}
