<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use App\Models\BoardContent;
use App\User;

class BoardContentThumbnailCreateTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    /**
     * Test POST /board-content with thumbnail and attachments
     */
    public function test_can_create_board_content_with_thumbnail_and_attachments()
    {
        // Create a test user
        $user = factory(User::class)->create();
        $this->actingAs($user);

        // Create test files
        $thumbnail = UploadedFile::fake()->image('thumbnail.jpg', 800, 600);
        $attachment1 = UploadedFile::fake()->image('attachment1.jpg', 1200, 800);
        $attachment2 = UploadedFile::fake()->image('attachment2.png', 1000, 1000);

        $response = $this->post('/board-content', [
            'mq_title' => 'Test Post with Thumbnail',
            'mq_content' => 'This is a test post with thumbnail and attachments.',
            'mq_category' => '일반',
            'mq_thumbnail_image' => $thumbnail,
            'mq_image' => [$attachment1, $attachment2]
        ]);

        // Should redirect to show page
        $response->assertRedirect();

        // Check database
        $this->assertDatabaseHas('mq_board_content', [
            'mq_title' => 'Test Post with Thumbnail',
            'mq_content' => 'This is a test post with thumbnail and attachments.',
            'mq_user_id' => $user->mq_user_id
        ]);

        $boardContent = BoardContent::where('mq_title', 'Test Post with Thumbnail')->first();

        // Check thumbnail fields are populated
        $this->assertNotNull($boardContent->mq_thumbnail_image);
        $this->assertNotNull($boardContent->mq_thumbnail_original);

        // Check attachment fields are populated
        $this->assertNotNull($boardContent->mq_image);
        $this->assertNotNull($boardContent->mq_original_image);
        $this->assertCount(2, $boardContent->mq_image);

        // Check files are stored
        Storage::disk('public')->assertExists('uploads/board_content/' . $boardContent->mq_thumbnail_image[0]);
        Storage::disk('public')->assertExists('uploads/board_content/' . $boardContent->mq_image[0]);
        Storage::disk('public')->assertExists('uploads/board_content/' . $boardContent->mq_image[1]);
    }

    /**
     * Test POST /board-content with thumbnail only
     */
    public function test_can_create_board_content_with_thumbnail_only()
    {
        $user = factory(User::class)->create();
        $this->actingAs($user);

        $thumbnail = UploadedFile::fake()->image('thumbnail.jpg', 800, 600);

        $response = $this->post('/board-content', [
            'mq_title' => 'Test Post with Thumbnail Only',
            'mq_content' => 'This is a test post with thumbnail only.',
            'mq_category' => '일반',
            'mq_thumbnail_image' => $thumbnail
        ]);

        $response->assertRedirect();

        $boardContent = BoardContent::where('mq_title', 'Test Post with Thumbnail Only')->first();

        // Check thumbnail fields are populated
        $this->assertNotNull($boardContent->mq_thumbnail_image);
        $this->assertNotNull($boardContent->mq_thumbnail_original);

        // Check attachment fields are empty
        $this->assertNull($boardContent->mq_image);
        $this->assertNull($boardContent->mq_original_image);
    }

    /**
     * Test POST /board-content with attachments only (existing behavior)
     */
    public function test_can_create_board_content_with_attachments_only()
    {
        $user = factory(User::class)->create();
        $this->actingAs($user);

        $attachment = UploadedFile::fake()->image('attachment.jpg', 1200, 800);

        $response = $this->post('/board-content', [
            'mq_title' => 'Test Post with Attachments Only',
            'mq_content' => 'This is a test post with attachments only.',
            'mq_category' => '일반',
            'mq_image' => [$attachment]
        ]);

        $response->assertRedirect();

        $boardContent = BoardContent::where('mq_title', 'Test Post with Attachments Only')->first();

        // Check thumbnail fields are empty
        $this->assertNull($boardContent->mq_thumbnail_image);
        $this->assertNull($boardContent->mq_thumbnail_original);

        // Check attachment fields are populated
        $this->assertNotNull($boardContent->mq_image);
        $this->assertNotNull($boardContent->mq_original_image);
    }

    /**
     * Test validation errors for invalid thumbnails
     */
    public function test_validation_fails_for_invalid_thumbnail()
    {
        $user = factory(User::class)->create();
        $this->actingAs($user);

        // Test with non-image file
        $invalidFile = UploadedFile::fake()->create('document.pdf', 1000);

        $response = $this->post('/board-content', [
            'mq_title' => 'Test Post',
            'mq_content' => 'Test content.',
            'mq_category' => '일반',
            'mq_thumbnail_image' => $invalidFile
        ]);

        $response->assertSessionHasErrors('mq_thumbnail_image');
    }

    /**
     * Test validation fails for oversized thumbnail
     */
    public function test_validation_fails_for_oversized_thumbnail()
    {
        $user = factory(User::class)->create();
        $this->actingAs($user);

        // Create oversized file (3MB)
        $oversizedFile = UploadedFile::fake()->image('large.jpg')->size(3072);

        $response = $this->post('/board-content', [
            'mq_title' => 'Test Post',
            'mq_content' => 'Test content.',
            'mq_category' => '일반',
            'mq_thumbnail_image' => $oversizedFile
        ]);

        $response->assertSessionHasErrors('mq_thumbnail_image');
    }

    /**
     * Test unauthenticated user cannot create posts
     */
    public function test_unauthenticated_user_cannot_create_posts()
    {
        $thumbnail = UploadedFile::fake()->image('thumbnail.jpg');

        $response = $this->post('/board-content', [
            'mq_title' => 'Test Post',
            'mq_content' => 'Test content.',
            'mq_category' => '일반',
            'mq_thumbnail_image' => $thumbnail
        ]);

        $response->assertRedirect('/login');
    }
}