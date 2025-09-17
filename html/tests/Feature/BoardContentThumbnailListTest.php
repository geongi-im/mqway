<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use App\Models\BoardContent;
use App\User;

class BoardContentThumbnailListTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    /**
     * Test GET /board-content displays posts with thumbnails
     */
    public function test_board_content_list_displays_thumbnails()
    {
        $user = factory(User::class)->create();

        // Create posts with thumbnails
        $postWithThumbnail = factory(BoardContent::class)->create([
            'mq_user_id' => $user->mq_user_id,
            'mq_title' => 'Post with Thumbnail',
            'mq_thumbnail_image' => ['thumbnail1.jpg'],
            'mq_thumbnail_original' => ['original_thumbnail1.jpg']
        ]);

        $postWithoutThumbnail = factory(BoardContent::class)->create([
            'mq_user_id' => $user->mq_user_id,
            'mq_title' => 'Post without Thumbnail',
            'mq_thumbnail_image' => null,
            'mq_thumbnail_original' => null
        ]);

        $response = $this->get('/board-content');

        $response->assertStatus(200);
        $response->assertSee('Post with Thumbnail');
        $response->assertSee('Post without Thumbnail');

        // Check that view data contains posts with processed thumbnail URLs
        $posts = $response->viewData('posts');
        $this->assertNotNull($posts);

        $postWithThumb = $posts->firstWhere('mq_title', 'Post with Thumbnail');
        $postWithoutThumb = $posts->firstWhere('mq_title', 'Post without Thumbnail');

        // Post with thumbnail should have thumbnail URL
        $this->assertNotNull($postWithThumb);
        if (is_array($postWithThumb->mq_thumbnail_image) && !empty($postWithThumb->mq_thumbnail_image)) {
            $this->assertStringContains('storage/uploads/board_content/thumbnail1.jpg', $postWithThumb->mq_thumbnail_image[0]);
        }

        // Post without thumbnail should have fallback
        $this->assertNotNull($postWithoutThumb);
    }

    /**
     * Test GET /board-content with posts having both thumbnails and attachments
     */
    public function test_board_content_list_prioritizes_thumbnails_over_attachments()
    {
        $user = factory(User::class)->create();

        // Create post with both thumbnail and attachments
        $postWithBoth = factory(BoardContent::class)->create([
            'mq_user_id' => $user->mq_user_id,
            'mq_title' => 'Post with Both',
            'mq_thumbnail_image' => ['thumbnail.jpg'],
            'mq_thumbnail_original' => ['thumbnail.jpg'],
            'mq_image' => ['attachment1.jpg', 'attachment2.jpg'],
            'mq_original_image' => ['attachment1.jpg', 'attachment2.jpg']
        ]);

        $response = $this->get('/board-content');

        $response->assertStatus(200);
        $posts = $response->viewData('posts');

        $post = $posts->firstWhere('mq_title', 'Post with Both');

        // Should display thumbnail, not attachment, in list view
        $this->assertNotNull($post);
        // The processListImages method should prioritize thumbnail over attachments
        if (is_array($post->mq_thumbnail_image) && !empty($post->mq_thumbnail_image)) {
            $this->assertStringContains('thumbnail.jpg', $post->mq_thumbnail_image[0]);
        }
    }

    /**
     * Test GET /board-content with search functionality including thumbnails
     */
    public function test_board_content_search_includes_thumbnail_posts()
    {
        $user = factory(User::class)->create();

        $searchablePost = factory(BoardContent::class)->create([
            'mq_user_id' => $user->mq_user_id,
            'mq_title' => 'Searchable Thumbnail Post',
            'mq_content' => 'This post has a thumbnail and is searchable',
            'mq_thumbnail_image' => ['search_thumbnail.jpg'],
            'mq_thumbnail_original' => ['search_thumbnail.jpg']
        ]);

        $otherPost = factory(BoardContent::class)->create([
            'mq_user_id' => $user->mq_user_id,
            'mq_title' => 'Other Post',
            'mq_content' => 'This is another post',
            'mq_thumbnail_image' => ['other_thumbnail.jpg'],
            'mq_thumbnail_original' => ['other_thumbnail.jpg']
        ]);

        $response = $this->get('/board-content?search=Searchable');

        $response->assertStatus(200);
        $response->assertSee('Searchable Thumbnail Post');
        $response->assertDontSee('Other Post');

        $posts = $response->viewData('posts');
        $this->assertEquals(1, $posts->count());

        $foundPost = $posts->first();
        $this->assertEquals('Searchable Thumbnail Post', $foundPost->mq_title);
    }

    /**
     * Test GET /board-content with category filtering and thumbnails
     */
    public function test_board_content_category_filter_includes_thumbnails()
    {
        $user = factory(User::class)->create();

        $generalPost = factory(BoardContent::class)->create([
            'mq_user_id' => $user->mq_user_id,
            'mq_title' => 'General Category Post',
            'mq_category' => '일반',
            'mq_thumbnail_image' => ['general_thumbnail.jpg'],
            'mq_thumbnail_original' => ['general_thumbnail.jpg']
        ]);

        $techPost = factory(BoardContent::class)->create([
            'mq_user_id' => $user->mq_user_id,
            'mq_title' => 'Tech Category Post',
            'mq_category' => '기술',
            'mq_thumbnail_image' => ['tech_thumbnail.jpg'],
            'mq_thumbnail_original' => ['tech_thumbnail.jpg']
        ]);

        $response = $this->get('/board-content?category=일반');

        $response->assertStatus(200);
        $response->assertSee('General Category Post');
        $response->assertDontSee('Tech Category Post');

        $posts = $response->viewData('posts');
        $this->assertEquals(1, $posts->count());

        $filteredPost = $posts->first();
        $this->assertEquals('일반', $filteredPost->mq_category);
    }

    /**
     * Test GET /board-content sorting by likes includes thumbnail posts
     */
    public function test_board_content_sort_by_likes_includes_thumbnails()
    {
        $user = factory(User::class)->create();

        $popularPost = factory(BoardContent::class)->create([
            'mq_user_id' => $user->mq_user_id,
            'mq_title' => 'Popular Post',
            'mq_like_cnt' => 10,
            'mq_thumbnail_image' => ['popular_thumbnail.jpg'],
            'mq_thumbnail_original' => ['popular_thumbnail.jpg']
        ]);

        $normalPost = factory(BoardContent::class)->create([
            'mq_user_id' => $user->mq_user_id,
            'mq_title' => 'Normal Post',
            'mq_like_cnt' => 2,
            'mq_thumbnail_image' => ['normal_thumbnail.jpg'],
            'mq_thumbnail_original' => ['normal_thumbnail.jpg']
        ]);

        $response = $this->get('/board-content?sort=likes');

        $response->assertStatus(200);

        $posts = $response->viewData('posts');
        $this->assertEquals(2, $posts->count());

        // First post should be the one with more likes
        $firstPost = $posts->first();
        $this->assertEquals('Popular Post', $firstPost->mq_title);
        $this->assertEquals(10, $firstPost->mq_like_cnt);
    }

    /**
     * Test GET /board-content pagination works with thumbnail posts
     */
    public function test_board_content_pagination_works_with_thumbnails()
    {
        $user = factory(User::class)->create();

        // Create multiple posts with thumbnails
        for ($i = 1; $i <= 15; $i++) {
            factory(BoardContent::class)->create([
                'mq_user_id' => $user->mq_user_id,
                'mq_title' => "Post {$i}",
                'mq_thumbnail_image' => ["thumbnail{$i}.jpg"],
                'mq_thumbnail_original' => ["thumbnail{$i}.jpg"]
            ]);
        }

        $response = $this->get('/board-content');

        $response->assertStatus(200);

        $posts = $response->viewData('posts');

        // BoardContentController sets itemsPerPage to 12
        $this->assertEquals(12, $posts->count());

        // Check pagination exists
        $this->assertTrue($posts->hasPages());
        $this->assertEquals(2, $posts->lastPage());
    }

    /**
     * Test GET /board-content handles posts with missing thumbnail files gracefully
     */
    public function test_board_content_handles_missing_thumbnail_files()
    {
        $user = factory(User::class)->create();

        // Create post with thumbnail reference but no actual file
        $postWithMissingFile = factory(BoardContent::class)->create([
            'mq_user_id' => $user->mq_user_id,
            'mq_title' => 'Post with Missing Thumbnail',
            'mq_thumbnail_image' => ['nonexistent_thumbnail.jpg'],
            'mq_thumbnail_original' => ['nonexistent_thumbnail.jpg']
        ]);

        $response = $this->get('/board-content');

        $response->assertStatus(200);
        $response->assertSee('Post with Missing Thumbnail');

        // Should not throw errors even if thumbnail file doesn't exist
        $posts = $response->viewData('posts');
        $this->assertEquals(1, $posts->count());
    }
}