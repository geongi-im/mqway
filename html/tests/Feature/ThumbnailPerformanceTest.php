<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use App\Models\BoardContent;
use App\User;

class ThumbnailPerformanceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    /**
     * Test list view performance with large number of posts with thumbnails
     */
    public function test_list_view_performance_with_large_number_of_posts()
    {
        $user = factory(User::class)->create();

        // Create 50 posts with thumbnails
        $startTime = microtime(true);

        for ($i = 1; $i <= 50; $i++) {
            factory(BoardContent::class)->create([
                'mq_user_id' => $user->mq_user_id,
                'mq_title' => "Performance Test Post {$i}",
                'mq_thumbnail_image' => ["thumbnail_{$i}.jpg"],
                'mq_thumbnail_original' => ["thumbnail_{$i}.jpg"]
            ]);
        }

        $creationTime = microtime(true) - $startTime;

        // Test list view response time
        $startTime = microtime(true);
        $response = $this->get('/board-content');
        $responseTime = microtime(true) - $startTime;

        $response->assertStatus(200);

        // Performance assertions
        $this->assertLessThan(2.0, $creationTime, 'Post creation should take less than 2 seconds');
        $this->assertLessThan(1.0, $responseTime, 'List view should respond within 1 second');

        // Verify all posts are displayed
        $posts = $response->viewData('posts');
        $this->assertGreaterThanOrEqual(12, $posts->count()); // Default pagination
    }

    /**
     * Test thumbnail upload performance
     */
    public function test_thumbnail_upload_performance()
    {
        $user = factory(User::class)->create();
        $this->actingAs($user);

        // Test upload performance with various file sizes
        $fileSizes = [100, 500, 1024, 2048]; // KB

        foreach ($fileSizes as $sizeKb) {
            $thumbnail = UploadedFile::fake()->image("thumbnail_{$sizeKb}kb.jpg", 800, 600)->size($sizeKb);

            $startTime = microtime(true);

            $response = $this->post('/board-content', [
                'mq_title' => "Performance Test {$sizeKb}KB",
                'mq_content' => 'Performance test content.',
                'mq_category' => '일반',
                'mq_thumbnail_image' => $thumbnail
            ]);

            $uploadTime = microtime(true) - $startTime;

            $response->assertRedirect();

            // Performance assertion - should complete within reasonable time
            $maxTime = $sizeKb < 1024 ? 2.0 : 3.0; // More time for larger files
            $this->assertLessThan($maxTime, $uploadTime,
                "Upload of {$sizeKb}KB file should complete within {$maxTime} seconds");

            // Verify post was created successfully
            $this->assertDatabaseHas('mq_board_content', [
                'mq_title' => "Performance Test {$sizeKb}KB"
            ]);
        }
    }

    /**
     * Test multiple concurrent thumbnail uploads
     */
    public function test_concurrent_thumbnail_uploads_performance()
    {
        $user = factory(User::class)->create();
        $this->actingAs($user);

        $uploads = [];
        $startTime = microtime(true);

        // Simulate concurrent uploads
        for ($i = 1; $i <= 5; $i++) {
            $thumbnail = UploadedFile::fake()->image("concurrent_{$i}.jpg", 800, 600);

            $uploads[] = [
                'mq_title' => "Concurrent Upload {$i}",
                'mq_content' => "Concurrent upload test {$i}.",
                'mq_category' => '일반',
                'mq_thumbnail_image' => $thumbnail
            ];
        }

        // Execute uploads sequentially (simulating concurrent processing)
        foreach ($uploads as $uploadData) {
            $response = $this->post('/board-content', $uploadData);
            $response->assertRedirect();
        }

        $totalTime = microtime(true) - $startTime;

        // Performance assertion
        $this->assertLessThan(10.0, $totalTime, 'Five concurrent uploads should complete within 10 seconds');

        // Verify all uploads succeeded
        for ($i = 1; $i <= 5; $i++) {
            $this->assertDatabaseHas('mq_board_content', [
                'mq_title' => "Concurrent Upload {$i}"
            ]);
        }
    }

    /**
     * Test thumbnail processing with different image formats
     */
    public function test_thumbnail_processing_different_formats_performance()
    {
        $user = factory(User::class)->create();
        $this->actingAs($user);

        $formats = ['jpg', 'png', 'gif'];

        foreach ($formats as $format) {
            $thumbnail = UploadedFile::fake()->image("test.{$format}", 800, 600);

            $startTime = microtime(true);

            $response = $this->post('/board-content', [
                'mq_title' => "Format Test {$format}",
                'mq_content' => "Testing {$format} format.",
                'mq_category' => '일반',
                'mq_thumbnail_image' => $thumbnail
            ]);

            $processTime = microtime(true) - $startTime;

            $response->assertRedirect();

            // Performance assertion
            $this->assertLessThan(3.0, $processTime,
                "Processing {$format} format should complete within 3 seconds");

            // Verify file was stored
            $post = BoardContent::where('mq_title', "Format Test {$format}")->first();
            $this->assertNotNull($post);
            $this->assertNotNull($post->mq_thumbnail_image);

            Storage::disk('public')->assertExists("uploads/board_content/{$post->mq_thumbnail_image[0]}");
        }
    }

    /**
     * Test database query performance with thumbnail data
     */
    public function test_database_query_performance_with_thumbnail_data()
    {
        $user = factory(User::class)->create();

        // Create posts with varying thumbnail configurations
        for ($i = 1; $i <= 30; $i++) {
            $thumbnailConfig = match ($i % 3) {
                0 => [
                    'mq_thumbnail_image' => ["thumbnail_{$i}.jpg"],
                    'mq_thumbnail_original' => ["original_{$i}.jpg"]
                ],
                1 => [
                    'mq_thumbnail_image' => null,
                    'mq_thumbnail_original' => null,
                    'mq_image' => ["attachment_{$i}.jpg"],
                    'mq_original_image' => ["attachment_{$i}.jpg"]
                ],
                2 => [
                    'mq_thumbnail_image' => null,
                    'mq_thumbnail_original' => null,
                    'mq_image' => null,
                    'mq_original_image' => null
                ]
            };

            factory(BoardContent::class)->create(array_merge([
                'mq_user_id' => $user->mq_user_id,
                'mq_title' => "Query Test Post {$i}"
            ], $thumbnailConfig));
        }

        // Test various query scenarios
        $scenarios = [
            'list_all' => fn() => BoardContent::where('mq_status', 1)->get(),
            'with_thumbnails' => fn() => BoardContent::where('mq_status', 1)
                ->whereNotNull('mq_thumbnail_image')->get(),
            'paginated' => fn() => BoardContent::where('mq_status', 1)->paginate(12),
            'search' => fn() => BoardContent::where('mq_status', 1)
                ->where('mq_title', 'like', '%Query%')->get(),
        ];

        foreach ($scenarios as $scenarioName => $query) {
            $startTime = microtime(true);
            $results = $query();
            $queryTime = microtime(true) - $startTime;

            $this->assertLessThan(0.5, $queryTime,
                "Query scenario '{$scenarioName}' should complete within 0.5 seconds");

            $this->assertNotNull($results);
        }
    }

    /**
     * Test image processing and storage performance
     */
    public function test_image_processing_storage_performance()
    {
        $user = factory(User::class)->create();
        $this->actingAs($user);

        // Test with progressively larger images
        $imageSizes = [
            ['width' => 400, 'height' => 300],
            ['width' => 800, 'height' => 600],
            ['width' => 1200, 'height' => 900],
            ['width' => 1600, 'height' => 1200]
        ];

        foreach ($imageSizes as $index => $size) {
            $thumbnail = UploadedFile::fake()->image(
                "performance_{$size['width']}x{$size['height']}.jpg",
                $size['width'],
                $size['height']
            );

            $startTime = microtime(true);

            $response = $this->post('/board-content', [
                'mq_title' => "Size Test {$size['width']}x{$size['height']}",
                'mq_content' => 'Size test content.',
                'mq_category' => '일반',
                'mq_thumbnail_image' => $thumbnail
            ]);

            $processingTime = microtime(true) - $startTime;

            $response->assertRedirect();

            // Performance assertion - larger images may take more time
            $maxTime = 2.0 + ($index * 0.5); // Progressive time allowance
            $this->assertLessThan($maxTime, $processingTime,
                "Processing {$size['width']}x{$size['height']} image should complete within {$maxTime} seconds");
        }
    }

    /**
     * Test thumbnail deletion performance
     */
    public function test_thumbnail_deletion_performance()
    {
        $user = factory(User::class)->create();

        // Create posts with thumbnails
        $posts = [];
        for ($i = 1; $i <= 10; $i++) {
            $filename = "delete_test_{$i}.jpg";
            Storage::disk('public')->put("uploads/board_content/{$filename}", 'fake_content');

            $posts[] = factory(BoardContent::class)->create([
                'mq_user_id' => $user->mq_user_id,
                'mq_title' => "Delete Test {$i}",
                'mq_thumbnail_image' => [$filename],
                'mq_thumbnail_original' => [$filename]
            ]);
        }

        // Test bulk deletion performance
        $startTime = microtime(true);

        foreach ($posts as $post) {
            $post->delete();
        }

        $deletionTime = microtime(true) - $startTime;

        // Performance assertion
        $this->assertLessThan(2.0, $deletionTime,
            'Deleting 10 posts with thumbnails should complete within 2 seconds');

        // Verify files were deleted
        foreach ($posts as $index => $post) {
            $filename = "delete_test_{$index}.jpg";
            Storage::disk('public')->assertMissing("uploads/board_content/{$filename}");
        }
    }

    /**
     * Test memory usage during thumbnail operations
     */
    public function test_memory_usage_during_thumbnail_operations()
    {
        $user = factory(User::class)->create();
        $this->actingAs($user);

        $initialMemory = memory_get_usage(true);

        // Perform multiple thumbnail operations
        for ($i = 1; $i <= 10; $i++) {
            $thumbnail = UploadedFile::fake()->image("memory_test_{$i}.jpg", 800, 600);

            $this->post('/board-content', [
                'mq_title' => "Memory Test {$i}",
                'mq_content' => 'Memory test content.',
                'mq_category' => '일반',
                'mq_thumbnail_image' => $thumbnail
            ]);

            // Check memory usage doesn't grow excessively
            $currentMemory = memory_get_usage(true);
            $memoryIncrease = $currentMemory - $initialMemory;

            // Memory increase should be reasonable (less than 50MB)
            $this->assertLessThan(50 * 1024 * 1024, $memoryIncrease,
                'Memory usage should not increase by more than 50MB during thumbnail operations');
        }
    }
}