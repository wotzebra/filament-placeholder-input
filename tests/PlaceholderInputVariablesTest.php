<?php

use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;
use Wotz\FilamentPlaceholderInput\Filament\Forms\Components\PlaceholderInput;
use Wotz\FilamentPlaceholderInput\PlaceholderVariable;

class RecordWithVariables extends Model
{
    public function getPlaceholderVariables(): array
    {
        return [PlaceholderVariable::make('from_record', 'From record', 'value')];
    }
}

class RecordWithoutVariables extends Model {}

function placeholderInput(?Model $record = null): PlaceholderInput
{
    $field = PlaceholderInput::make('variables');
    $field->container(Schema::make()->record($record));

    return $field;
}

it('returns the variables it was configured with', function () {
    $variables = [PlaceholderVariable::make('explicit', 'Explicit', 'value')];

    expect(placeholderInput(new RecordWithVariables)->variables($variables)->getVariables())
        ->toBe($variables);
});

it('resolves configured variables from a closure', function () {
    $variables = [PlaceholderVariable::make('explicit', 'Explicit', 'value')];

    expect(placeholderInput(new RecordWithVariables)->variables(fn () => $variables)->getVariables())
        ->toBe($variables);
});

it('falls back to the record when no variables are configured', function () {
    $resolved = placeholderInput(new RecordWithVariables)->getVariables();

    expect($resolved)->toHaveCount(1)
        ->and($resolved[0]->getKey())->toBe('from_record');
});

it('returns an empty array when the record cannot supply variables', function () {
    expect(placeholderInput(new RecordWithoutVariables)->getVariables())->toBe([]);
});

it('returns an empty array when there is no record at all', function () {
    // A create page has no record yet; method_exists(null, ...) is a TypeError.
    expect(placeholderInput()->getVariables())->toBe([]);
});
