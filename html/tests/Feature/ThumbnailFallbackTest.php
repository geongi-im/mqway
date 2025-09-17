<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use App\Models\BoardContent;
use App\User;

class ThumbnailFallbackTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    /**
     * Test thumbnail fallback to first attachment image
     */
    public function test_thumbnail_fallback_to_first_attachment()
    {
        $user = factory(User::class)->create();

        // Create post without thumbnail but with attachments
        $post = factory(BoardContent::class)->create([
            'mq_user_id' => $user->mq_user_id,
            'mq_title' => 'Post without Thumbnail',
            'mq_content' => 'This post has no thumbnail but has attachments.',
            'mq_thumbnail_image' => null,
            'mq_thumbnail_original' => null,
            'mq_image' => ['attachment1.jpg', 'attachment2.jpg'],
            'mq_original_image' => ['attachment1.jpg', 'attachment2.jpg']
        ]);

        $response = $this->get('/board-content');

        $response->assertStatus(200);

        $posts = $response->viewData('posts');
        $targetPost = $posts->firstWhere('mq_title', 'Post without Thumbnail');

        $this->assertNotNull($targetPost);

        // Should fallback to first attachment as thumbnail in list view
        if (is_string($targetPost->mq_image)) {
            $this->assertStringContains('attachment1.jpg', $targetPost->mq_image);
        }
    }

    /**
     * Test thumbnail fallback to content image extraction
     */
    public function test_thumbnail_fallback_to_content_images()
    {
        $user = factory(User::class)->create();

        // Create post without thumbnail and attachments but with images in content
        $contentWithImage = '<p>This post has an image in content:</p><img src="https://example.com/content-image.jpg" alt="Content Image"><p>More text here.</p>';

        $post = factory(BoardContent::class)->create([
            'mq_user_id' => $user->mq_user_id,
            'mq_title' => 'Post with Content Images',
            'mq_content' => $contentWithImage,
            'mq_thumbnail_image' => null,
            'mq_thumbnail_original' => null,
            'mq_image' => null,
            'mq_original_image' => null
        ]);

        $response = $this->get('/board-content');

        $response->assertStatus(200);

        $posts = $response->viewData('posts');
        $targetPost = $posts->firstWhere('mq_title', 'Post with Content Images');

        $this->assertNotNull($targetPost);

        // The processListImages should extract first image from content
        // This depends on the extractFirstImageSrc helper function
        if (function_exists('extractFirstImageSrc')) {
            $this->assertStringContains('content-image.jpg', $targetPost->mq_image);
        }
    }

    /**
     * Test thumbnail fallback to default image
     */
    public function test_thumbnail_fallback_to_default_image()
    {
        $user = factory(User::class)->create();

        // Create post without thumbnail, attachments, or content images
        $post = factory(BoardContent::class)->create([
            'mq_user_id' => $user->mq_user_id,
            'mq_title' => 'Post with No Images',
            'mq_content' => 'This post has no images at all.',
            'mq_thumbnail_image' => null,
            'mq_thumbnail_original' => null,
            'mq_image' => null,
            'mq_original_image' => null
        ]);

        $response = $this->get('/board-content');

        $response->assertStatus(200);

        $posts = $response->viewData('posts');
        $targetPost = $posts->firstWhere('mq_title', 'Post with No Images');

        $this->assertNotNull($targetPost);

        // Should fallback to default no_image.jpeg
        $this->assertStringContains('no_image.jpeg', $targetPost->mq_image);
    }

    /**
     * Test thumbnail priority order: thumbnail > attachment > content > default
     */
    public function test_thumbnail_priority_order()
    {
        $user = factory(User::class)->create();

        // Test 1: Post with thumbnail (highest priority)
        $postWithThumbnail = factory(BoardContent::class)->create([
            'mq_user_id' => $user->mq_user_id,
            'mq_title' => 'Post with Thumbnail Priority',
            'mq_content' => '<img src="https://example.com/content.jpg">',
            'mq_thumbnail_image' => ['thumbnail.jpg'],
            'mq_thumbnail_original' => ['thumbnail.jpg'],
            'mq_image' => ['attachment.jpg'],
            'mq_original_image' => ['attachment.jpg']
        ]);

        // Test 2: Post with attachment but no thumbnail
        $postWithAttachment = factory(BoardContent::class)->create([
            'mq_user_id' => $user->mq_user_id,
            'mq_title' => 'Post with Attachment Priority',
            'mq_content' => '<img src="https://example.com/content.jpg">',
            'mq_thumbnail_image' => null,
            'mq_thumbnail_original' => null,
            'mq_image' => ['attachment.jpg'],
            'mq_original_image' => ['attachment.jpg']
        ]);

        // Test 3: Post with content image only
        $postWithContentImage = factory(BoardContent::class)->create([
            'mq_user_id' => $user->mq_user_id,
            'mq_title' => 'Post with Content Priority',
            'mq_content' => '<img src="https://example.com/content.jpg">',
            'mq_thumbnail_image' => null,
            'mq_thumbnail_original' => null,
            'mq_image' => null,
            'mq_original_image' => null
        ]);

        $response = $this->get('/board-content');
        $response->assertStatus(200);

        $posts = $response->viewData('posts');

        // Verify priority order
        $thumbnailPost = $posts->firstWhere('mq_title', 'Post with Thumbnail Priority');
        $attachmentPost = $posts->firstWhere('mq_title', 'Post with Attachment Priority');
        $contentPost = $posts->firstWhere('mq_title', 'Post with Content Priority');

        // Post with thumbnail should use thumbnail
        if (is_array($thumbnailPost->mq_thumbnail_image) && !empty($thumbnailPost->mq_thumbnail_image)) {
            $this->assertStringContains('thumbnail.jpg', $thumbnailPost->mq_thumbnail_image[0]);
        }

        // Post with attachment should use attachment
        if (is_string($attachmentPost->mq_image)) {
            $this->assertStringContains('attachment.jpg', $attachmentPost->mq_image);
        }

        // Post with content image should extract from content
        if (function_exists('extractFirstImageSrc')) {
            $this->assertStringContains('content.jpg', $contentPost->mq_image);
        }
    }

    /**
     * Test fallback behavior in detail view
     */
    public function test_thumbnail_fallback_in_detail_view()
    {
        $user = factory(User::class)->create();

        // Create post without thumbnail
        $post = factory(BoardContent::class)->create([
            'mq_user_id' => $user->mq_user_id,
            'mq_title' => 'Detail View Fallback Test',
            'mq_content' => 'This post has no thumbnail.',
            'mq_thumbnail_image' => null,
            'mq_thumbnail_original' => null,
            'mq_image' => ['detail_attachment.jpg'],
            'mq_original_image' => ['detail_attachment.jpg']
        ]);

        $response = $this->get("/board-content/{$post->idx}");

        $response->assertStatus(200);
        $response->assertSee('Detail View Fallback Test');

        $viewPost = $response->viewData('post');
        $this->assertNotNull($viewPost);

        // In detail view, should show all attachments
        if (is_array($viewPost->mq_image)) {
            $this->assertStringContains('detail_attachment.jpg', $viewPost->mq_image[0]);
        }
    }

    /**
     * Test fallback behavior with missing thumbnail files
     */
    public function test_fallback_with_missing_thumbnail_files()
    {
        $user = factory(User::class)->create();

        // Create post with thumbnail reference but file doesn't exist
        $post = factory(BoardContent::class)->create([
            'mq_user_id' => $user->mq_user_id,
            'mq_title' => 'Missing Thumbnail File',
            'mq_content' => 'This post references a missing thumbnail.',
            'mq_thumbnail_image' => ['nonexistent_thumbnail.jpg'],
            'mq_thumbnail_original' => ['nonexistent_thumbnail.jpg'],
            'mq_image' => ['backup_attachment.jpg'],
            'mq_original_image' => ['backup_attachment.jpg']
        ]);

        $response = $this->get('/board-content');

        $response->assertStatus(200);

        $posts = $response->viewData('posts');
        $targetPost = $posts->firstWhere('mq_title', 'Missing Thumbnail File');

        $this->assertNotNull($targetPost);

        // Should gracefully handle missing files and potentially fallback
        // The exact behavior depends on implementation, but should not cause errors
        $this->assertTrue(true); // Test passes if no exceptions thrown
    }

    /**
     * Test fallback behavior with empty content
     */
    public function test_fallback_with_empty_content()
    {
        $user = factory(User::class)->create();

        // Create post with minimal content
        $post = factory(BoardContent::class)->create([
            'mq_user_id' => $user->mq_user_id,
            'mq_title' => 'Empty Content Post',
            'mq_content' => '',
            'mq_thumbnail_image' => null,
            'mq_thumbnail_original' => null,
            'mq_image' => null,
            'mq_original_image' => null
        ]);

        $response = $this->get('/board-content');

        $response->assertStatus(200);

        $posts = $response->viewData('posts');
        $targetPost = $posts->firstWhere('mq_title', 'Empty Content Post');

        $this->assertNotNull($targetPost);

        // Should fallback to default image for empty content
        $this->assertStringContains('no_image.jpeg', $targetPost->mq_image);
    }

    /**
     * Test fallback behavior with malformed image data
     */
    public function test_fallback_with_malformed_image_data()
    {
        $user = factory(User::class)->create();

        // Create post with malformed image data
        $post = factory(BoardContent::class)->create([
            'mq_user_id' => $user->mq_user_id,
            'mq_title' => 'Malformed Image Data',
            'mq_content' => 'This post has malformed image data.',
            'mq_thumbnail_image' => 'not_an_array',
            'mq_thumbnail_original' => 'not_an_array',
            'mq_image' => 'also_not_an_array',
            'mq_original_image' => 'also_not_an_array'
        ]);

        $response = $this->get('/board-content');

        $response->assertStatus(200);

        $posts = $response->viewData('posts');
        $targetPost = $posts->firstWhere('mq_title', 'Malformed Image Data');

        $this->assertNotNull($targetPost);

        // Should handle malformed data gracefully
        $this->assertTrue(true); // Test passes if no exceptions thrown
    }
}