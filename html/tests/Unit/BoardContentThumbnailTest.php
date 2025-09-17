<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\BoardContent;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BoardContentThumbnailTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test hasThumbnail method returns true when thumbnail exists
     */
    public function test_has_thumbnail_returns_true_when_thumbnail_exists()
    {
        $boardContent = new BoardContent([
            'mq_thumbnail_image' => ['thumbnail.jpg'],
            'mq_thumbnail_original' => ['thumbnail.jpg']
        ]);

        $this->assertTrue($boardContent->hasThumbnail());
    }

    /**
     * Test hasThumbnail method returns false when thumbnail is null
     */
    public function test_has_thumbnail_returns_false_when_thumbnail_is_null()
    {
        $boardContent = new BoardContent([
            'mq_thumbnail_image' => null,
            'mq_thumbnail_original' => null
        ]);

        $this->assertFalse($boardContent->hasThumbnail());
    }

    /**
     * Test hasThumbnail method returns false when thumbnail is empty array
     */
    public function test_has_thumbnail_returns_false_when_thumbnail_is_empty_array()
    {
        $boardContent = new BoardContent([
            'mq_thumbnail_image' => [],
            'mq_thumbnail_original' => []
        ]);

        $this->assertFalse($boardContent->hasThumbnail());
    }

    /**
     * Test hasThumbnail method returns false when thumbnail is not array
     */
    public function test_has_thumbnail_returns_false_when_thumbnail_is_not_array()
    {
        $boardContent = new BoardContent([
            'mq_thumbnail_image' => 'not_an_array',
            'mq_thumbnail_original' => 'not_an_array'
        ]);

        $this->assertFalse($boardContent->hasThumbnail());
    }

    /**
     * Test getThumbnailUrl method returns correct URL for stored file
     */
    public function test_get_thumbnail_url_returns_correct_url_for_stored_file()
    {
        $boardContent = new BoardContent([
            'mq_thumbnail_image' => ['thumbnail.jpg'],
            'mq_thumbnail_original' => ['thumbnail.jpg']
        ]);

        $expectedUrl = asset('storage/uploads/board_content/thumbnail.jpg');
        $this->assertEquals($expectedUrl, $boardContent->getThumbnailUrl());
    }

    /**
     * Test getThumbnailUrl method returns URL as-is for external URLs
     */
    public function test_get_thumbnail_url_returns_url_as_is_for_external_urls()
    {
        $externalUrl = 'https://example.com/thumbnail.jpg';
        $boardContent = new BoardContent([
            'mq_thumbnail_image' => [$externalUrl],
            'mq_thumbnail_original' => ['thumbnail.jpg']
        ]);

        $this->assertEquals($externalUrl, $boardContent->getThumbnailUrl());
    }

    /**
     * Test getThumbnailUrl method returns null when no thumbnail
     */
    public function test_get_thumbnail_url_returns_null_when_no_thumbnail()
    {
        $boardContent = new BoardContent([
            'mq_thumbnail_image' => null,
            'mq_thumbnail_original' => null
        ]);

        $this->assertNull($boardContent->getThumbnailUrl());
    }

    /**
     * Test getThumbnailFilename method returns filename when thumbnail exists
     */
    public function test_get_thumbnail_filename_returns_filename_when_thumbnail_exists()
    {
        $boardContent = new BoardContent([
            'mq_thumbnail_image' => ['thumbnail.jpg'],
            'mq_thumbnail_original' => ['thumbnail.jpg']
        ]);

        $this->assertEquals('thumbnail.jpg', $boardContent->getThumbnailFilename());
    }

    /**
     * Test getThumbnailFilename method returns null when no thumbnail
     */
    public function test_get_thumbnail_filename_returns_null_when_no_thumbnail()
    {
        $boardContent = new BoardContent([
            'mq_thumbnail_image' => null,
            'mq_thumbnail_original' => null
        ]);

        $this->assertNull($boardContent->getThumbnailFilename());
    }

    /**
     * Test getThumbnailOriginalName method returns original name when exists
     */
    public function test_get_thumbnail_original_name_returns_original_name_when_exists()
    {
        $boardContent = new BoardContent([
            'mq_thumbnail_image' => ['stored_filename.jpg'],
            'mq_thumbnail_original' => ['original_filename.jpg']
        ]);

        $this->assertEquals('original_filename.jpg', $boardContent->getThumbnailOriginalName());
    }

    /**
     * Test getThumbnailOriginalName method returns null when no thumbnail
     */
    public function test_get_thumbnail_original_name_returns_null_when_no_thumbnail()
    {
        $boardContent = new BoardContent([
            'mq_thumbnail_image' => null,
            'mq_thumbnail_original' => null
        ]);

        $this->assertNull($boardContent->getThumbnailOriginalName());
    }

    /**
     * Test getThumbnailOriginalName method returns null when original is not array
     */
    public function test_get_thumbnail_original_name_returns_null_when_original_is_not_array()
    {
        $boardContent = new BoardContent([
            'mq_thumbnail_image' => ['thumbnail.jpg'],
            'mq_thumbnail_original' => 'not_an_array'
        ]);

        $this->assertNull($boardContent->getThumbnailOriginalName());
    }

    /**
     * Test model casts work correctly for thumbnail fields
     */
    public function test_model_casts_work_correctly_for_thumbnail_fields()
    {
        $user = factory(User::class)->create();

        $boardContent = factory(BoardContent::class)->create([
            'mq_user_id' => $user->mq_user_id,
            'mq_thumbnail_image' => ['thumbnail.jpg'],
            'mq_thumbnail_original' => ['thumbnail.jpg']
        ]);

        // Refresh from database to test casting
        $boardContent = $boardContent->fresh();

        $this->assertIsArray($boardContent->mq_thumbnail_image);
        $this->assertIsArray($boardContent->mq_thumbnail_original);
        $this->assertEquals(['thumbnail.jpg'], $boardContent->mq_thumbnail_image);
        $this->assertEquals(['thumbnail.jpg'], $boardContent->mq_thumbnail_original);
    }

    /**
     * Test fillable fields include thumbnail fields
     */
    public function test_fillable_fields_include_thumbnail_fields()
    {
        $boardContent = new BoardContent();
        $fillable = $boardContent->getFillable();

        $this->assertContains('mq_thumbnail_image', $fillable);
        $this->assertContains('mq_thumbnail_original', $fillable);
    }

    /**
     * Test model can be created with thumbnail data
     */
    public function test_model_can_be_created_with_thumbnail_data()
    {
        $user = factory(User::class)->create();

        $data = [
            'mq_title' => 'Test Post with Thumbnail',
            'mq_content' => 'Test content',
            'mq_category' => '일반',
            'mq_user_id' => $user->mq_user_id,
            'mq_thumbnail_image' => ['thumbnail.jpg'],
            'mq_thumbnail_original' => ['original_thumbnail.jpg'],
            'mq_status' => 1,
            'mq_reg_date' => now()
        ];

        $boardContent = BoardContent::create($data);

        $this->assertNotNull($boardContent);
        $this->assertEquals(['thumbnail.jpg'], $boardContent->mq_thumbnail_image);
        $this->assertEquals(['original_thumbnail.jpg'], $boardContent->mq_thumbnail_original);
        $this->assertTrue($boardContent->hasThumbnail());
    }

    /**
     * Test model handles multiple thumbnails correctly
     */
    public function test_model_handles_multiple_thumbnails_correctly()
    {
        $boardContent = new BoardContent([
            'mq_thumbnail_image' => ['thumb1.jpg', 'thumb2.jpg'],
            'mq_thumbnail_original' => ['original1.jpg', 'original2.jpg']
        ]);

        $this->assertTrue($boardContent->hasThumbnail());
        $this->assertEquals('thumb1.jpg', $boardContent->getThumbnailFilename());
        $this->assertEquals('original1.jpg', $boardContent->getThumbnailOriginalName());

        $expectedUrl = asset('storage/uploads/board_content/thumb1.jpg');
        $this->assertEquals($expectedUrl, $boardContent->getThumbnailUrl());
    }

    /**
     * Test model gracefully handles corrupted thumbnail data
     */
    public function test_model_gracefully_handles_corrupted_thumbnail_data()
    {
        $boardContent = new BoardContent([
            'mq_thumbnail_image' => [''],  // Empty string in array
            'mq_thumbnail_original' => ['']
        ]);

        // Should still consider this as having thumbnail data (array exists)
        $this->assertTrue($boardContent->hasThumbnail());
        $this->assertEquals('', $boardContent->getThumbnailFilename());

        // URL should handle empty filename gracefully
        $url = $boardContent->getThumbnailUrl();
        $this->assertNotNull($url);
    }

    /**
     * Test backward compatibility with existing posts without thumbnails
     */
    public function test_backward_compatibility_with_existing_posts_without_thumbnails()
    {
        $user = factory(User::class)->create();

        // Create post without thumbnail fields (simulating existing data)
        $boardContent = factory(BoardContent::class)->create([
            'mq_user_id' => $user->mq_user_id,
            'mq_thumbnail_image' => null,
            'mq_thumbnail_original' => null
        ]);

        $this->assertFalse($boardContent->hasThumbnail());
        $this->assertNull($boardContent->getThumbnailUrl());
        $this->assertNull($boardContent->getThumbnailFilename());
        $this->assertNull($boardContent->getThumbnailOriginalName());

        // Should not cause any errors
        $this->assertTrue(true);
    }
}