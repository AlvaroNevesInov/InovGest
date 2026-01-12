<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\User;
use App\Models\VatRate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ArticleTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected VatRate $vatRate;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->vatRate = VatRate::factory()->create();
    }

    /** @test */
    public function it_displays_articles_index_page()
    {
        $response = $this->actingAs($this->user)
            ->get(route('settings.articles.index'));

        $response->assertStatus(200);
    }

    /** @test */
    public function it_can_create_an_article()
    {
        Storage::fake('public');

        $articleData = [
            'reference' => 'ART-001',
            'name' => 'Test Article',
            'description' => 'Test description',
            'price' => 99.99,
            'vat_rate_id' => $this->vatRate->id,
            'photo' => UploadedFile::fake()->image('article.jpg'),
            'notes' => 'Test notes',
            'active' => true,
        ];

        $response = $this->actingAs($this->user)
            ->post(route('settings.articles.store'), $articleData);

        $response->assertRedirect();

        $this->assertDatabaseHas('articles', [
            'reference' => 'ART-001',
            'name' => 'Test Article',
            'price' => 99.99,
        ]);
    }

    /** @test */
    public function it_validates_required_fields()
    {
        $response = $this->actingAs($this->user)
            ->post(route('settings.articles.store'), []);

        $response->assertSessionHasErrors(['reference', 'name', 'price', 'vat_rate_id']);
    }

    /** @test */
    public function it_validates_unique_reference()
    {
        Article::factory()->create([
            'reference' => 'ART-001',
            'vat_rate_id' => $this->vatRate->id,
        ]);

        $response = $this->actingAs($this->user)
            ->post(route('settings.articles.store'), [
                'reference' => 'ART-001',
                'name' => 'Test Article',
                'price' => 99.99,
                'vat_rate_id' => $this->vatRate->id,
            ]);

        $response->assertSessionHasErrors(['reference']);
    }

    /** @test */
    public function it_can_update_an_article()
    {
        $article = Article::factory()->create([
            'vat_rate_id' => $this->vatRate->id,
            'name' => 'Old Name',
        ]);

        $response = $this->actingAs($this->user)
            ->put(route('settings.articles.update', $article), [
                'reference' => $article->reference,
                'name' => 'New Name',
                'price' => 199.99,
                'vat_rate_id' => $this->vatRate->id,
                'active' => true,
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('articles', [
            'id' => $article->id,
            'name' => 'New Name',
            'price' => 199.99,
        ]);
    }

    /** @test */
    public function it_can_delete_an_article()
    {
        $article = Article::factory()->create([
            'vat_rate_id' => $this->vatRate->id,
        ]);

        $response = $this->actingAs($this->user)
            ->delete(route('settings.articles.destroy', $article));

        $response->assertRedirect(route('settings.articles.index'));

        $this->assertDatabaseMissing('articles', [
            'id' => $article->id,
        ]);
    }

    /** @test */
    public function it_searches_articles_by_name_or_reference()
    {
        Article::factory()->create([
            'reference' => 'SEARCH-001',
            'name' => 'Searchable Article',
            'vat_rate_id' => $this->vatRate->id,
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('settings.articles.index', ['search' => 'Searchable']));

        $response->assertStatus(200);
    }

    /** @test */
    public function it_filters_active_articles()
    {
        Article::factory()->create([
            'vat_rate_id' => $this->vatRate->id,
            'active' => true,
        ]);

        Article::factory()->create([
            'vat_rate_id' => $this->vatRate->id,
            'active' => false,
        ]);

        $activeArticles = Article::active()->get();

        $this->assertCount(1, $activeArticles);
    }

    /** @test */
    public function unauthenticated_users_cannot_access_articles()
    {
        $response = $this->get(route('settings.articles.index'));

        $response->assertRedirect(route('login'));
    }
}
