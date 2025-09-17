<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use App\Models\BoardContent;
use App\User;

class ThumbnailUploadIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    /**
     * Test complete thumbnail upload workflow from create to view
     */
    public function test_complete_thumbnail_upload_workflow()
    {
        $user = factory(User::class)->create();
        $this->actingAs($user);

        // Step 1: Create post with thumbnail
        $thumbnail = UploadedFile::fake()->image('test_thumbnail.jpg', 800, 600);
        $attachment = UploadedFile::fake()->image('test_attachment.jpg', 1200, 800);

        $createResponse = $this->post('/board-content', [
            'mq_title' => 'Integration Test Post',
            'mq_content' => 'This is an integration test for thumbnail upload.',
            'mq_category' => '일반',
            'mq_thumbnail_image' => $thumbnail,
            'mq_image' => [$attachment]
        ]);

        $createResponse->assertRedirect();

        // Step 2: Verify post was created in database
        $this->assertDatabaseHas('mq_board_content', [
            'mq_title' => 'Integration Test Post',
            'mq_user_id' => $user->mq_user_id
        ]);

        $post = BoardContent::where('mq_title', 'Integration Test Post')->first();
        $this->assertNotNull($post);

        // Step 3: Verify thumbnail and attachment files were stored
        $this->assertNotNull($post->mq_thumbnail_image);
        $this->assertNotNull($post->mq_thumbnail_original);
        $this->assertNotNull($post->mq_image);
        $this->assertNotNull($post->mq_original_image);

        $thumbnailFilename = $post->mq_thumbnail_image[0];
        $attachmentFilename = $post->mq_image[0];

        Storage::disk('public')->assertExists("uploads/board_content/{$thumbnailFilename}");
        Storage::disk('public')->assertExists("uploads/board_content/{$attachmentFilename}");

        // Step 4: Test list view displays correctly
        $listResponse = $this->get('/board-content');
        $listResponse->assertStatus(200);
        $listResponse->assertSee('Integration Test Post');

        // Step 5: Test detail view displays correctly
        $detailResponse = $this->get("/board-content/{$post->idx}");
        $detailResponse->assertStatus(200);
        $detailResponse->assertSee('Integration Test Post');
        $detailResponse->assertSee('This is an integration test for thumbnail upload.');

        // Step 6: Test edit form loads with existing thumbnail
        $editFormResponse = $this->get("/board-content/{$post->idx}/edit");
        $editFormResponse->assertStatus(200);
        $editFormResponse->assertSee('Integration Test Post');

        // Step 7: Test update with new thumbnail
        $newThumbnail = UploadedFile::fake()->image('updated_thumbnail.jpg', 800, 600);

        $updateResponse = $this->put("/board-content/{$post->idx}", [
            'mq_title' => 'Updated Integration Test Post',
            'mq_content' => 'Updated content with new thumbnail.',
            'mq_category' => '일반',
            'mq_thumbnail_image' => $newThumbnail
        ]);

        $updateResponse->assertRedirect("/board-content/{$post->idx}");

        // Step 8: Verify update was successful
        $updatedPost = BoardContent::find($post->idx);
        $this->assertEquals('Updated Integration Test Post', $updatedPost->mq_title);
        $this->assertEquals('updated_thumbnail.jpg', $updatedPost->mq_thumbnail_original[0]);

        $newThumbnailFilename = $updatedPost->mq_thumbnail_image[0];
        Storage::disk('public')->assertExists("uploads/board_content/{$newThumbnailFilename}");
    }

    /**
     * Test thumbnail upload with various file formats
     */
    public function test_thumbnail_upload_supports_various_formats()
    {
        $user = factory(User::class)->create();
        $this->actingAs($user);

        $formats = [
            'jpg' => UploadedFile::fake()->image('test.jpg', 800, 600),
            'jpeg' => UploadedFile::fake()->image('test.jpeg', 800, 600),
            'png' => UploadedFile::fake()->image('test.png', 800, 600),
            'gif' => UploadedFile::fake()->image('test.gif', 800, 600),
        ];

        foreach ($formats as $format => $file) {
            $response = $this->post('/board-content', [
                'mq_title' => "Test Post {$format}",
                'mq_content' => "Testing {$format} format thumbnail.",
                'mq_category' => '일반',
                'mq_thumbnail_image' => $file
            ]);

            $response->assertRedirect();

            $post = BoardContent::where('mq_title', "Test Post {$format}")->first();
            $this->assertNotNull($post);
            $this->assertNotNull($post->mq_thumbnail_image);

            Storage::disk('public')->assertExists("uploads/board_content/{$post->mq_thumbnail_image[0]}");
        }
    }

    /**
     * Test thumbnail upload handles file size limits
     */
    public function test_thumbnail_upload_respects_size_limits()
    {
        $user = factory(User::class)->create();
        $this->actingAs($user);

        // Test with acceptable size (1MB)
        $acceptableFile = UploadedFile::fake()->image('acceptable.jpg', 800, 600)->size(1024);

        $successResponse = $this->post('/board-content', [
            'mq_title' => 'Acceptable Size Post',
            'mq_content' => 'Testing acceptable file size.',
            'mq_category' => '일반',
            'mq_thumbnail_image' => $acceptableFile
        ]);

        $successResponse->assertRedirect();
        $this->assertDatabaseHas('mq_board_content', [
            'mq_title' => 'Acceptable Size Post'
        ]);

        // Test with oversized file (3MB)
        $oversizedFile = UploadedFile::fake()->image('oversized.jpg', 2000, 2000)->size(3072);

        $failResponse = $this->post('/board-content', [
            'mq_title' => 'Oversized File Post',
            'mq_content' => 'Testing oversized file.',
            'mq_category' => '일반',
            'mq_thumbnail_image' => $oversizedFile
        ]);

        $failResponse->assertSessionHasErrors('mq_thumbnail_image');
        $this->assertDatabaseMissing('mq_board_content', [
            'mq_title' => 'Oversized File Post'
        ]);
    }

    /**
     * Test thumbnail upload with concurrent requests
     */
    public function test_thumbnail_upload_handles_concurrent_requests()
    {
        $user = factory(User::class)->create();
        $this->actingAs($user);

        // Simulate multiple thumbnail uploads happening at the same time
        $responses = [];

        for ($i = 1; $i <= 3; $i++) {
            $thumbnail = UploadedFile::fake()->image("concurrent_{$i}.jpg", 800, 600);

            $responses[] = $this->post('/board-content', [
                'mq_title' => "Concurrent Post {$i}",
                'mq_content' => "Testing concurrent upload {$i}.",
                'mq_category' => '일반',
                'mq_thumbnail_image' => $thumbnail
            ]);
        }

        // All requests should succeed
        foreach ($responses as $response) {
            $response->assertRedirect();
        }

        // All posts should be created
        for ($i = 1; $i <= 3; $i++) {
            $this->assertDatabaseHas('mq_board_content', [
                'mq_title' => "Concurrent Post {$i}"
            ]);

            $post = BoardContent::where('mq_title', "Concurrent Post {$i}")->first();
            $this->assertNotNull($post->mq_thumbnail_image);

            Storage::disk('public')->assertExists("uploads/board_content/{$post->mq_thumbnail_image[0]}");
        }
    }

    /**
     * Test thumbnail deletion workflow
     */
    public function test_thumbnail_deletion_workflow()
    {
        $user = factory(User::class)->create();
        $this->actingAs($user);

        // Create post with thumbnail
        $thumbnail = UploadedFile::fake()->image('to_delete.jpg', 800, 600);

        $createResponse = $this->post('/board-content', [
            'mq_title' => 'Post to Delete',
            'mq_content' => 'This post will be deleted.',
            'mq_category' => '일반',
            'mq_thumbnail_image' => $thumbnail
        ]);

        $createResponse->assertRedirect();

        $post = BoardContent::where('mq_title', 'Post to Delete')->first();
        $thumbnailFilename = $post->mq_thumbnail_image[0];

        // Verify file exists
        Storage::disk('public')->assertExists("uploads/board_content/{$thumbnailFilename}");

        // Delete the post
        $deleteResponse = $this->delete("/board-content/{$post->idx}");
        $deleteResponse->assertRedirect('/board-content');

        // Verify post is deleted from database
        $this->assertDatabaseMissing('mq_board_content', [
            'idx' => $post->idx
        ]);

        // Verify thumbnail file is deleted (if using hard delete)
        Storage::disk('public')->assertMissing("uploads/board_content/{$thumbnailFilename}");
    }

    /**
     * Test error handling during thumbnail upload
     */
    public function test_thumbnail_upload_error_handling()
    {
        $user = factory(User::class)->create();
        $this->actingAs($user);

        // Test with corrupted file
        $corruptedFile = UploadedFile::fake()->create('corrupted.jpg', 0);

        $response = $this->post('/board-content', [
            'mq_title' => 'Corrupted File Post',
            'mq_content' => 'Testing corrupted file.',
            'mq_category' => '일반',
            'mq_thumbnail_image' => $corruptedFile
        ]);

        $response->assertSessionHasErrors();

        // Test with non-image file
        $textFile = UploadedFile::fake()->create('document.txt', 1000);

        $response = $this->post('/board-content', [
            'mq_title' => 'Text File Post',
            'mq_content' => 'Testing non-image file.',
            'mq_category' => '일반',
            'mq_thumbnail_image' => $textFile
        ]);

        $response->assertSessionHasErrors('mq_thumbnail_image');
    }
}