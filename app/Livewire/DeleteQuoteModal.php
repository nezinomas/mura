<?php

namespace App\Livewire;

use App\Models\Quote;
use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Gate;

class DeleteQuoteModal extends Component
{
    public ?Quote $quote = null;
    public bool $showModal = false;
    public bool $isGrabbed = false;

    // This perfectly matches your Test's dispatch event!
    #[On('confirmDelete')]
    public function confirmDelete($quoteId)
    {
        $this->quote = Quote::findOrFail($quoteId);
        $this->isGrabbed = $this->quote->grabbedBy()->exists();
        $this->showModal = true;
    }

    public function destroy()
    {
        if (! $this->quote) {
            return;
        }

        Gate::authorize('delete', $this->quote);
        $this->quote->disownOrDelete();
        $this->showModal = false;

        return redirect('/dashboard');
    }

    public function render()
    {
        return view('livewire.delete-quote-modal');
    }
}