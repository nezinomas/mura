<?php

use App\Models\User;
use App\Models\Quote;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;


dataset('empty_quotes', [
    'null' => [null],
    ]);


dataset('quotes_with_wrong_lenght', [
    'too long' => [str_repeat('a', 1001)],
    'too short' => ['ab'],
    'only spaces' => ['   ']
]);


dataset('allow_markdown_in_quoutes', [
    'bold' => ['**text**', '<p><strong>text</strong></p>'],
    'italic1' => ['*text*', '<p><em>text</em></p>'],
    'italic2' => ['_text_', '<p><em>text</em></p>'],
    'oold and italic 1' => ['***bold + italic text***', '<p><em><strong>bold + italic text</strong></em></p>'],
    'bold and italic 2' => ['___bold + italic text___ ', '<p><em><strong>bold + italic text</strong></em></p>'],
    'strikethrought' => ['~~text~~', '<p><del>text</del></p>'],
    'link 1' => [
        '<https://google.com>',
        '<p><a href="https://google.com">https://google.com</a></p>'
    ],
    'link 2' => [
        '[Link](https://google.com)',
        '<p><a href="https://google.com">Link</a></p>'
    ],
    'link with title' => [
        '[Link with title](https://google.com "Here the title goes")',
        '<p><a href="https://google.com" title="Here the title goes">Link with title</a></p>'
    ],
]);


dataset('malicious_markdown_in_quoutes', [
    'script' => ['<script>alert("hacked")</script>', '<script'],
    'h1' => ['# Loud HEADER', '<h1'],
    'img' => ['![image](https://bad.com/img.jpg)', '<img'],
]);


beforeEach(function() {
    $this->user = User::factory()->create();
});


test('quote securely attaches to its author', function() {
    $content = 'This is quote.';

    $quote = Quote::factory()->create([
        'user_id' => $this->user->id,
        'content' => $content
    ]);

    expect($quote->user->id)->toBe($this->user->id);
    expect($quote->content)->toBe($content);
});


test('quote defaults to being public', function () {
    $quote = Quote::factory()->create([
        'content' => 'This is a default public thought.',
    ]);

    expect($quote->is_private)->toBeFalse();
});


test('quote can be completely private', function () {
    $quote = Quote::factory()->create([
        'content' => 'This is a secret.',
        'is_private' => true,
    ]);

    expect($quote->is_private)->toBeTrue();
});


test('author_display shows user display_name if user is active', function() {
    $this->user->display_name = 'User Name';


    $quote = Quote::factory()->make(['user_id' => $this->user->id]);

    $quote->setRelation('user', $this->user);

    expect($quote->author_display)->toBe('User Name');
});


test('quote survives but becomes anonymous when user deletes their account', function() {
    $quote = Quote::factory()->create(['user_id' => $this->user->id]);

    $this->user->delete();

    $quote->refresh();

    expect($quote->user_id)->toBeNull();
    expect($quote->author_display)->toBe('(user lost in time)');
});


test('reject empty quote', function($content) {
    $quote = fn() => Quote::factory()->create([
        'user_id' => $this->user->id,
        'content' => $content
    ]);

    expect($quote)->toThrow(QueryException::class);
})->with('empty_quotes');


test('quote length is 1000 symbols', function() {
    $quote = Quote::factory()->create([
        'user_id' => $this->user->id,
        'content' => str_repeat('a', 1000)
    ]);

    expect(strlen($quote->content))->toBe(1000);
});


test('reject quotes with wrong length', function($content) {
    $quote = fn() => Quote::factory()->create([
        'user_id' => $this->user->id,
        'content' => $content
    ]);

    expect($quote)->toThrow(InvalidArgumentException::class);
})->with('quotes_with_wrong_lenght');


test('convert allow markdown in qoutes', function($content, $expect) {
    $quote = Quote::factory()->create([
        'user_id' => $this->user->id,
        'content' => $content
    ]);
    expect(trim($quote->content_html))->toBe($expect);
})->with('allow_markdown_in_quoutes');


test('strip malicious markdown in qoutes', function($content, $tag) {
    $quote = Quote::factory()->create([
        'user_id' => $this->user->id,
        'content' => $content
    ]);
    expect($quote->content_html)->not->toContain($tag);
})->with('malicious_markdown_in_quoutes');


test('quote can be edited within 24 hours of creation', function () {
    $quote = Quote::factory()->create(['user_id' => $this->user->id]);

    $this->travel(23)->hours();

    expect($quote->isEditable())->toBeTrue();
});


test('quote is permanently locked in stone after 24 hours', function () {
    $quote = Quote::factory()->create(['user_id' => $this->user->id]);

    $this->travel(25)->hours();

    expect($quote->isEditable())->toBeFalse();
});


test('the model knows if a quote has been altered after creation', function () {
    $quote = Quote::factory()->create([
        'user_id' => $this->user->id,
        'content' => 'Original thought.',
    ]);

    expect($quote->isEdited())->toBeFalse();

    $this->travel(1)->hours();

    $quote->update(['content' => 'Altered thought.']);

    expect($quote->isEdited())->toBeTrue();
});


test('isMine returns true if logged user is author', function() {
    $quote = Quote::factory()->create(['user_id' => $this->user->id]);

    $this->actingAs($this->user);

    expect($quote->isMine())->toBeTrue();
});


test('isMine return false if logged user is stanger', function() {
    $stranger = User::factory()->create();
    $quote = Quote::factory()->create(['user_id' => $this->user->id]);

    $this->actingAs($stranger);

    expect($quote->isMine())->toBeFalse();
});


test('isMine return false for an aunauthentiated guest', function() {
    $quote = Quote::factory()->create(['user_id' => $this->user->id]);

    expect($quote->isMine())->toBeFalse();
});


test('isGrabbedByMe returns true if logged user grabbed quote', function() {
    $stranger = User::factory()->create();
    $quote = Quote::factory()->create(['user_id' => $stranger->id]);

    $this->user->grabs()->attach($quote->id);

    $this->actingAs($this->user);
    $quote->refresh();

    expect($quote->isGrabbedByMe())->toBeTrue();
});


test('isGrabbedByMe returns false if logged user has not grabbed it', function() {
    $stranger = User::factory()->create();
    $quote = Quote::factory()->create(['user_id' => $stranger->id]);

    $this->actingAs($this->user);

    expect($quote->isGrabbedByMe())->toBeFalse();
});


test('isGrabbedByMe return false safely for unauthenticated guest', function() {
    $quote = Quote::factory()->create();

    // Model's auth()->check() should catch this safely
    expect($quote->isGrabbedByMe())->toBeFalse();
});


test('grabbedBy relationship accurately tracks multiple users', function() {
    $quote = Quote::factory()->create();

    $userA = User::factory()->create();
    $userB = User::factory()->create();

    $userA->grabs()->attach($quote->id);
    $userB->grabs()->attach($quote->id);

    expect($quote->grabbedBy)->toHaveCount(2);
    expect($quote->grabbedBy->pluck('id')->toArray())->toContain($userA->id, $userB->id);
});


test('isGrabbedByMe runs a lightweight exists query instead of hydrating models', function() {
    $quote = Quote::factory()->create();

    $users = User::factory(3)->create();
    $quote->grabbedBy()->attach($users->pluck('id'));

    $this->actingAs($this->user);

    DB::enableQueryLog();
    $quote->isGrabbedByMe();
    $queries = DB::getQueryLog();

    expect(count($queries))->toBe(1);

    $executedSql = $queries[0]['query'];

    expect($executedSql)->toContain('exists');
    expect($executedSql)->not->toContain('select * from `users`');
});