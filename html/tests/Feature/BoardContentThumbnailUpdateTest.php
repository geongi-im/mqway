<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use App\Models\BoardContent;
use App\User;

class BoardContentThumbnailUpdateTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    /**
     * Test PUT /board-content/{idx} with thumbnail update
     */
    public function test_can_update_board_content_with_new_thumbnail()
    {
        // Create user and existing post
        $user = factory(User::class)->create();
        $this->actingAs($user);

        $post = factory(BoardContent::class)->create([
            'mq_user_id' => $user->mq_user_id,
            'mq_thumbnail_image' => null,
            'mq_thumbnail_original' => null
        ]);

        // Create new thumbnail
        $newThumbnail = UploadedFile::fake()->image('new_thumbnail.jpg', 800, 600);

        $response = $this->put("/board-content/{$post->idx}", [
            'mq_title' => 'Updated Post with Thumbnail',
            'mq_content' => 'Updated content with new thumbnail.',
            'mq_category' => '일반',
            'mq_thumbnail_image' => $newThumbnail
        ]);

        $response->assertRedirect("/board-content/{$post->idx}");

        // Check database
        $this->assertDatabaseHas('mq_board_content', [
            'idx' => $post->idx,
            'mq_title' => 'Updated Post with Thumbnail'
        ]);

        $updatedPost = BoardContent::find($post->idx);

        // Check thumbnail was added
        $this->assertNotNull($updatedPost->mq_thumbnail_image);
        $this->assertNotNull($updatedPost->mq_thumbnail_original);
        $this->assertEquals('new_thumbnail.jpg', $updatedPost->mq_thumbnail_original[0]);

        // Check file was stored
        Storage::disk('public')->assertExists('uploads/board_content/' . $updatedPost->mq_thumbnail_image[0]);
    }

    /**
     * Test PUT /board-content/{idx} replacing existing thumbnail
     */
    public function test_can_replace_existing_thumbnail()
    {
        $user = factory(User::class)->create();
        $this->actingAs($user);

        // Create existing thumbnail file
        $oldThumbnail = UploadedFile::fake()->image('old_thumbnail.jpg', 800, 600);
        $oldFilename = 'old_thumbnail_stored.jpg';
        Storage::disk('public')->put('uploads/board_content/' . $oldFilename, $oldThumbnail->getContent());

        $post = factory(BoardContent::class)->create([
            'mq_user_id' => $user->mq_user_id,
            'mq_thumbnail_image' => [$oldFilename],
            'mq_thumbnail_original' => ['old_thumbnail.jpg']
        ]);

        // Create new thumbnail
        $newThumbnail = UploadedFile::fake()->image('new_thumbnail.jpg', 800, 600);

        $response = $this->put("/board-content/{$post->idx}", [
            'mq_title' => 'Updated Post with New Thumbnail',
            'mq_content' => 'Updated content with replaced thumbnail.',
            'mq_category' => '일반',
            'mq_thumbnail_image' => $newThumbnail
        ]);

        $response->assertRedirect("/board-content/{$post->idx}");

        $updatedPost = BoardContent::find($post->idx);

        // Check new thumbnail was set
        $this->assertNotNull($updatedPost->mq_thumbnail_image);
        $this->assertNotNull($updatedPost->mq_thumbnail_original);
        $this->assertEquals('new_thumbnail.jpg', $updatedPost->mq_thumbnail_original[0]);

        // Check new file was stored
        Storage::disk('public')->assertExists('uploads/board_content/' . $updatedPost->mq_thumbnail_image[0]);
    }

    /**
     * Test PUT /board-content/{idx} with thumbnail and attachment updates
     */
    public function test_can_update_both_thumbnail_and_attachments()
    {
        $user = factory(User::class)->create();
        $this->actingAs($user);

        $post = factory(BoardContent::class)->create([
            'mq_user_id' => $user->mq_user_id,
            'mq_thumbnail_image' => null,
            'mq_thumbnail_original' => null,
            'mq_image' => null,
            'mq_original_image' => null
        ]);

        $newThumbnail = UploadedFile::fake()->image('thumbnail.jpg', 800, 600);
        $newAttachment = UploadedFile::fake()->image('attachment.jpg', 1200, 800);

        $response = $this->put("/board-content/{$post->idx}", [
            'mq_title' => 'Updated Post with Both',
            'mq_content' => 'Updated content with thumbnail and attachment.',
            'mq_category' => '일반',
            'mq_thumbnail_image' => $newThumbnail,
            'mq_image' => [$newAttachment]
        ]);

        $response->assertRedirect("/board-content/{$post->idx}");

        $updatedPost = BoardContent::find($post->idx);

        // Check both thumbnail and attachments were added
        $this->assertNotNull($updatedPost->mq_thumbnail_image);
        $this->assertNotNull($updatedPost->mq_thumbnail_original);
        $this->assertNotNull($updatedPost->mq_image);
        $this->assertNotNull($updatedPost->mq_original_image);
    }

    /**
     * Test PUT /board-content/{idx} without changing thumbnail (preserves existing)
     */
    public function test_preserves_existing_thumbnail_when_not_updated()
    {
        $user = factory(User::class)->create();
        $this->actingAs($user);

        $originalThumbnail = ['existing_thumbnail.jpg'];
        $originalThumbnailName = ['existing_thumbnail.jpg'];

        $post = factory(BoardContent::class)->create([
            'mq_user_id' => $user->mq_user_id,
            'mq_thumbnail_image' => $originalThumbnail,
            'mq_thumbnail_original' => $originalThumbnailName
        ]);

        $response = $this->put("/board-content/{$post->idx}", [
            'mq_title' => 'Updated Post Title Only',
            'mq_content' => 'Updated content only.',
            'mq_category' => '일반'
            // No thumbnail field sent - should preserve existing
        ]);

        $response->assertRedirect("/board-content/{$post->idx}");

        $updatedPost = BoardContent::find($post->idx);

        // Check existing thumbnail is preserved
        $this->assertEquals($originalThumbnail, $updatedPost->mq_thumbnail_image);
        $this->assertEquals($originalThumbnailName, $updatedPost->mq_thumbnail_original);
    }

    /**
     * Test validation fails for invalid thumbnail update
     */
    public function test_validation_fails_for_invalid_thumbnail_update()
    {
        $user = factory(User::class)->create();
        $this->actingAs($user);

        $post = factory(BoardContent::class)->create([
            'mq_user_id' => $user->mq_user_id
        ]);

        $invalidFile = UploadedFile::fake()->create('document.pdf', 1000);

        $response = $this->put("/board-content/{$post->idx}", [
            'mq_title' => 'Updated Post',
            'mq_content' => 'Updated content.',
            'mq_category' => '일반',
            'mq_thumbnail_image' => $invalidFile
        ]);

        $response->assertSessionHasErrors('mq_thumbnail_image');
    }

    /**
     * Test unauthorized user cannot update posts
     */
    public function test_unauthorized_user_cannot_update_posts()
    {
        $user1 = factory(User::class)->create();
        $user2 = factory(User::class)->create();

        $post = factory(BoardContent::class)->create([
            'mq_user_id' => $user1->mq_user_id
        ]);

        // Act as different user
        $this->actingAs($user2);

        $thumbnail = UploadedFile::fake()->image('thumbnail.jpg');

        $response = $this->put("/board-content/{$post->idx}", [
            'mq_title' => 'Unauthorized Update',
            'mq_content' => 'This should fail.',
            'mq_category' => '일반',
            'mq_thumbnail_image' => $thumbnail
        ]);

        $response->assertRedirect("/board-content/{$post->idx}");
        $response->assertSessionHas('error');
    }

    /**
     * Test unauthenticated user cannot update posts
     */
    public function test_unauthenticated_user_cannot_update_posts()
    {
        $user = factory(User::class)->create();
        $post = factory(BoardContent::class)->create([
            'mq_user_id' => $user->mq_user_id
        ]);

        $thumbnail = UploadedFile::fake()->image('thumbnail.jpg');

        $response = $this->put("/board-content/{$post->idx}", [
            'mq_title' => 'Unauthorized Update',
            'mq_content' => 'This should fail.',
            'mq_category' => '일반',
            'mq_thumbnail_image' => $thumbnail
        ]);

        $response->assertRedirect('/login');
    }
}