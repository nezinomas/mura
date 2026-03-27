<?php

namespace App\Livewire;

use App\Models\Quote;
use Livewire\Component;

class GrabButton extends Component
{
    public Quote $quote;
    public bool $isGrabbed = false;

    public function mount(Quote $quote)
    {
        $this->quote = $quote;
        
        if (auth()->check()) {
            $this->isGrabbed = $this->quote->isGrabbedBy(auth()->user());
        }
    }

    public function toggle()
    {
        if (! auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        if ($this->isGrabbed) {
            $user->grabs()->detach($this->quote->id);
            $this->isGrabbed = false;
        } else {
            $user->grabs()->syncWithoutDetaching([$this->quote->id]);
            $this->isGrabbed = true;
        }
    }

    public function render()
    {
        return view('livewire.grab-button');
    }
}