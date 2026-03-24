# mura. Project Architecture & Guidelines

## 1. Project Identity & Aesthetic
* **Name:** mura.
* **Concept:** A quiet, analog writing space. Thoughts are treated like physical paper or pages in a published book.
* **Typography:** * `IBM+Plex+Mono` (Serif) is the primary reading font for all main content.
* **Styling:** Tailwind CSS (v4 paradigm via `app.css`). DaisyUI is available but should be used minimally. Avoid loud colors; prefer muted pastels (e.g., `slate-50` for grabbed thoughts) and subtle UI interactions.

## 2. Core Engineering Principles

### TDD First (Test-Driven Development)
* **Testing Framework:** Pest.
* **Rule:** No feature code is written until the corresponding Pest test is written and failing. We test routes, views, assertions, and database interactions before building them out.

### Pure CSS > JavaScript
* **Rule:** If a UI interaction can be handled natively by CSS, do not use JavaScript or Alpine.js.
* **Implementation:** Heavily rely on Tailwind's `peer` and `group` classes (e.g., using `peer-placeholder-shown` for floating form labels) to handle state changes without JS listeners.

### DRY Architecture & Blade Components
* **Rule:** Do not duplicate large strings of Tailwind classes. 
* **Implementation:** Encapsulate reusable UI elements into Laravel Blade components (e.g., `<x-button>`, `<x-floating-input>`). 
* **Views:** Reuse Blade files when logical (e.g., sharing a single `create.blade.php` file for both creating and editing resources).

### Framework Scaffolding
* **Authentication:** We are actively moving away from standard Laravel Breeze scaffolding in favor of custom, strictly necessary implementations that match our aesthetic and routing needs.

## 3. Development Roadmap

* **Phase 1: Foundation & Auth** - Set up Pest, define the core architecture, and strip away unnecessary Breeze bloat. *(Completed)*
* **Phase 2: The "Thought" Model** - Create the `Quote` model and database migration. *(Completed)*
* **Phase 3: The "Compose" Feature** - TDD route, view, and save logic for creating new thoughts. *(Completed)*
* **Phase 4: The "Home" Feed** - TDD dashboard feed displaying a mix of a user's own thoughts and thoughts they have "grabbed" from others. *(Current Phase)*
* **Phase 5: The "Global" Discover** - TDD public feed allowing guests to view 20 random thoughts from the community. *(Ongoing)*
* **Phase 6: The "Grab" Logic** - Pivot table implementation linking users to other users' thoughts while preserving original author attribution. *(Completed)*
* **Phase 7: The Aesthetic** - Continuous application of the analog, typewriter/book aesthetic across all components. *(Current Phase)*
* **Phase 8: RSS Feed** - Every user gets an RSS feed *(Ongoing)*
* **Phase 9: Import** - Import from Tumbl *(Ongoing)*